#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "${ROOT_DIR}"

SUDO=""
if [ "$(id -u)" -ne 0 ] && command -v sudo >/dev/null 2>&1; then
  SUDO="sudo"
fi

# ---------------------------------------------------------------------------
# Config
# ---------------------------------------------------------------------------
HOST_UID="$(id -u)"
HOST_GID="$(id -g)"
export HOST_UID HOST_GID

load_env() {
  if [ ! -f "${ROOT_DIR}/.env" ]; then
    echo "Criando .env a partir de .env.example..."
    cp "${ROOT_DIR}/.env.example" "${ROOT_DIR}/.env"
    {
      echo "HOST_UID=${HOST_UID}"
      echo "HOST_GID=${HOST_GID}"
    } >> "${ROOT_DIR}/.env"
  fi
  # shellcheck disable=SC1091
  set -a; . "${ROOT_DIR}/.env"; set +a
}

compose() {
  if docker compose version >/dev/null 2>&1; then
    docker compose "$@"
  elif command -v docker-compose >/dev/null 2>&1; then
    docker-compose "$@"
  else
    echo "ERRO: docker compose não encontrado. Instale Docker Compose v2." >&2
    exit 1
  fi
}

mexec() { compose exec -T -u "${HOST_UID}:${HOST_GID}" moodle "$@"; }

ensure_docker() {
  if docker info >/dev/null 2>&1; then return 0; fi
  echo "Docker não responde. Tentando iniciar..."
  command -v systemctl >/dev/null 2>&1 && ${SUDO} systemctl start docker >/dev/null 2>&1 || true
  command -v service   >/dev/null 2>&1 && ${SUDO} service   docker start >/dev/null 2>&1 || true
  if docker info >/dev/null 2>&1; then return 0; fi
  echo "ERRO: Docker indisponível. Verifique o serviço e permissões em /var/run/docker.sock" >&2
  return 1
}

# ---------------------------------------------------------------------------
# Setup steps
# ---------------------------------------------------------------------------
download_moodle() {
  if [ -f "${ROOT_DIR}/moodle/version.php" ]; then
    echo "✓ Código do Moodle já presente em ./moodle"
    return 0
  fi
  local url="https://download.moodle.org/download.php/direct/stable${MOODLE_BRANCH}/moodle-${MOODLE_VERSION}.tgz"
  echo "Baixando Moodle ${MOODLE_VERSION} ..."
  echo "  ${url}"
  curl -fL "${url}" -o /tmp/moodle.tgz
  # O tarball extrai para um diretório 'moodle/' na raiz do projeto
  tar xzf /tmp/moodle.tgz -C "${ROOT_DIR}"
  rm -f /tmp/moodle.tgz
  echo "✓ Moodle extraído em ./moodle"
}

generate_certs() {
  local cert="${ROOT_DIR}/docker/certs/moodle.localhost.pem"
  local key="${ROOT_DIR}/docker/certs/moodle.localhost-key.pem"
  mkdir -p "${ROOT_DIR}/docker/certs"
  if [ -f "${cert}" ] && [ -f "${key}" ]; then
    echo "✓ Certificado SSL já existe"
    return 0
  fi
  if command -v mkcert >/dev/null 2>&1; then
    echo "Gerando certificado confiável com mkcert..."
    mkcert -install >/dev/null 2>&1 || true
    mkcert -cert-file "${cert}" -key-file "${key}" "${MOODLE_HOST}" >/dev/null 2>&1
    echo "✓ Certificado mkcert criado (confiável no navegador)"
  else
    echo "mkcert não encontrado. Gerando self-signed com openssl (navegador exibirá aviso)..."
    openssl req -x509 -nodes -newkey rsa:2048 -days 825 \
      -keyout "${key}" -out "${cert}" \
      -subj "/CN=${MOODLE_HOST}" \
      -addext "subjectAltName=DNS:${MOODLE_HOST}" >/dev/null 2>&1
    echo "✓ Certificado self-signed criado. (Instale mkcert para certificado confiável.)"
  fi
}

ensure_hosts_entry() {
  if grep -qE "[[:space:]]${MOODLE_HOST}(\$|[[:space:]])" /etc/hosts 2>/dev/null; then
    return 0
  fi
  echo "⚠ '${MOODLE_HOST}' não está em /etc/hosts."
  echo "  Para acessar, adicione (precisa de sudo):"
  echo "    echo '127.0.0.1 ${MOODLE_HOST}' | ${SUDO} tee -a /etc/hosts"
}

wait_db() {
  echo "Aguardando MySQL ficar pronto..."
  local deadline=$((SECONDS + 120))
  while [ "${SECONDS}" -lt "${deadline}" ]; do
    if compose exec -T db mysqladmin ping -h 127.0.0.1 -uroot -p"${DB_ROOT_PASSWORD}" >/dev/null 2>&1; then
      echo "✓ MySQL pronto"
      return 0
    fi
    sleep 2
  done
  echo "⚠ Timeout aguardando MySQL." >&2
  return 1
}

install_moodle() {
  if [ -f "${ROOT_DIR}/moodle/config.php" ]; then
    echo "✓ Moodle já instalado (config.php existe)"
    return 0
  fi
  echo "Instalando Moodle via CLI (admin/cli/install.php)..."
  mexec php admin/cli/install.php \
    --wwwroot="${MOODLE_WWWROOT}" \
    --dataroot=/var/www/moodledata \
    --dbtype=mysqli --dbhost=db --dbport=3306 \
    --dbname="${DB_NAME}" --dbuser="${DB_USER}" --dbpass="${DB_PASSWORD}" \
    --fullname="${MOODLE_FULLNAME}" --shortname="${MOODLE_SHORTNAME}" \
    --adminuser="${MOODLE_ADMIN_USER}" --adminpass="${MOODLE_ADMIN_PASS}" \
    --adminemail="${MOODLE_ADMIN_EMAIL}" \
    --non-interactive --agree-license
  echo "✓ Moodle instalado"
}

configure_mail() {
  echo "Configurando SMTP (mailpit)..."
  mexec php admin/cli/cfg.php --name=smtphosts  --set="mailpit:1025" >/dev/null 2>&1 || true
  mexec php admin/cli/cfg.php --name=smtpsecure --set="" >/dev/null 2>&1 || true
  mexec php admin/cli/cfg.php --name=smtpuser   --set="" >/dev/null 2>&1 || true
  mexec php admin/cli/cfg.php --name=smtppass   --set="" >/dev/null 2>&1 || true
  echo "✓ SMTP apontado para mailpit:1025"
}

cleanup_disk() {
  echo "Limpando resíduos (logs/caches) para otimizar disco..."
  # Caches/temp do Moodle (recriados sob demanda)
  rm -rf "${ROOT_DIR}/moodledata/cache/"* \
         "${ROOT_DIR}/moodledata/localcache/"* \
         "${ROOT_DIR}/moodledata/temp/"* \
         "${ROOT_DIR}/moodledata/trashdir/"* \
         "${ROOT_DIR}/moodledata/sessions/"* 2>/dev/null || true
  # Logs soltos
  find "${ROOT_DIR}/moodledata" -name '*.log' -type f -delete 2>/dev/null || true
  # Resíduos de pacotes no container
  compose exec -T moodle bash -c "apt-get clean >/dev/null 2>&1; rm -rf /tmp/* /var/tmp/* /var/lib/apt/lists/* >/dev/null 2>&1" 2>/dev/null || true
  echo "✓ Limpeza concluída"
}

# ---------------------------------------------------------------------------
# Commands
# ---------------------------------------------------------------------------
run_start() {
  echo "=========================================="
  echo "Moodle Dev - start"
  echo "=========================================="
  load_env

  echo ""; echo "[1/7] Verificando Docker..."
  ensure_docker || exit 1
  echo "✓ Docker OK"

  echo ""; echo "[2/7] Preparando pastas..."
  mkdir -p "${ROOT_DIR}/moodle" "${ROOT_DIR}/moodledata" "${ROOT_DIR}/data/mysql"
  chmod +x "${ROOT_DIR}/dev.sh" 2>/dev/null || true

  echo ""; echo "[3/7] Baixando código do Moodle..."
  download_moodle

  echo ""; echo "[4/7] Gerando certificado SSL..."
  generate_certs
  ensure_hosts_entry

  echo ""; echo "[5/7] Buildando e subindo containers..."
  compose up -d --build --remove-orphans
  wait_db

  echo ""; echo "[6/7] Instalando/configurando Moodle..."
  install_moodle
  configure_mail

  echo ""; echo "[7/7] Otimizando disco..."
  cleanup_disk

  echo ""
  echo "=========================================="
  echo "✓ Ambiente pronto"
  echo "=========================================="
  echo "Moodle:      ${MOODLE_WWWROOT}"
  echo "  login:     ${MOODLE_ADMIN_USER} / ${MOODLE_ADMIN_PASS}"
  echo "phpMyAdmin:  http://localhost:${PMA_PORT}"
  echo "Mailpit:     http://localhost:${MAILPIT_PORT}"
  echo "=========================================="
}

run_stop() {
  load_env
  echo "Parando containers..."
  compose down --remove-orphans "$@"
  echo "✓ Parado"
}

run_reset() {
  load_env
  echo "ATENÇÃO: remove containers, imagens, banco (data/mysql) e moodledata."
  echo "O código em ./moodle e ./moodle/config.php serão MANTIDOS por padrão."
  read -r -p "Digite 'sim' para confirmar: " confirm
  [ "${confirm}" = "sim" ] || { echo "Cancelado."; exit 0; }

  ensure_docker || exit 1
  compose down --remove-orphans --rmi all -v 2>/dev/null || true
  ${SUDO} rm -rf "${ROOT_DIR}/data/mysql" "${ROOT_DIR}/moodledata"
  echo "✓ Banco e moodledata removidos"
  echo ""
  read -r -p "Remover também ./moodle/config.php para reinstalar do zero? (sim/não): " rc
  if [ "${rc}" = "sim" ]; then
    rm -f "${ROOT_DIR}/moodle/config.php"
    echo "✓ config.php removido"
  fi
  echo ""
  run_start
}

run_shell() { load_env; mexec bash || compose exec -u "${HOST_UID}:${HOST_GID}" moodle bash; }

run_cron() { load_env; mexec php admin/cli/cron.php; }

run_backup() {
  load_env
  local ts; ts="$(date +%Y%m%d_%H%M%S)"
  local dir="${ROOT_DIR}/backups/moodle-backup-${ts}"
  mkdir -p "${dir}"
  echo "Backup do banco..."
  compose exec -T db mysqldump -uroot -p"${DB_ROOT_PASSWORD}" --no-tablespaces "${DB_NAME}" > "${dir}/database.sql"
  echo "Backup do moodledata..."
  tar czf "${dir}/moodledata.tar.gz" -C "${ROOT_DIR}" moodledata
  cp "${ROOT_DIR}/moodle/config.php" "${dir}/config.php" 2>/dev/null || true
  echo "✓ Backup em ${dir}"
  du -sh "${dir}"
}

run_restore() {
  load_env
  local src="${1:-}"
  if [ -z "${src}" ] || [ ! -d "${src}" ]; then
    echo "Uso: ./dev.sh restore <dir-do-backup>"
    ls -1d "${ROOT_DIR}/backups/moodle-backup-"* 2>/dev/null || echo "  Nenhum backup."
    exit 1
  fi
  echo "ATENÇÃO: sobrescreve banco e moodledata atuais."
  read -r -p "Digite 'sim' para confirmar: " confirm
  [ "${confirm}" = "sim" ] || { echo "Cancelado."; exit 0; }

  echo "Restaurando banco..."
  compose exec -T db mysql -uroot -p"${DB_ROOT_PASSWORD}" "${DB_NAME}" < "${src}/database.sql"
  echo "Restaurando moodledata..."
  rm -rf "${ROOT_DIR}/moodledata"
  tar xzf "${src}/moodledata.tar.gz" -C "${ROOT_DIR}"
  echo "✓ Restaurado"
}

usage() {
  cat <<EOF
Uso: ./dev.sh <comando>

  start            Sobe e prepara todo o ambiente Moodle
  stop             Para os containers
  reset            Recria do zero (apaga banco e moodledata)
  shell            Abre shell no container do Moodle (como seu usuário)
  cron             Roda o cron do Moodle uma vez
  backup           Backup do banco + moodledata + config.php
  restore <dir>    Restaura a partir de um backup
EOF
}

main() {
  local cmd="${1:-}"; shift || true
  case "${cmd}" in
    start|-start)     run_start "$@" ;;
    stop|-stop)       run_stop "$@" ;;
    reset|-reset)     run_reset "$@" ;;
    shell|-shell)     run_shell "$@" ;;
    cron|-cron)       run_cron "$@" ;;
    backup|-backup)   run_backup "$@" ;;
    restore|-restore) run_restore "$@" ;;
    -h|--help|help|"") usage ;;
    *) echo "ERRO: comando inválido: ${cmd}" >&2; usage >&2; exit 1 ;;
  esac
}

main "$@"
