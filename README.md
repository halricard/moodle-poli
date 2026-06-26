# Moodle Dev (Docker)

Ambiente de desenvolvimento Moodle com PHP 8.4 + Xdebug, MySQL 8.4, phpMyAdmin,
Mailpit (e-mail de dev) e SSL local em `https://moodle.localhost`.

## Requisitos
- Docker + Docker Compose v2
- (Opcional, recomendado) [mkcert](https://github.com/FiloSottile/mkcert) para certificado SSL confiável
- Entrada em `/etc/hosts`: `127.0.0.1 moodle.localhost`

## Uso
```bash
./dev.sh start     # baixa Moodle, gera SSL, sobe tudo e instala
./dev.sh stop      # para
./dev.sh reset     # recria do zero
./dev.sh shell     # shell no container (seu usuário)
./dev.sh cron      # roda o cron uma vez
./dev.sh backup    # backup banco + moodledata + config
./dev.sh restore <dir>
```

## Acessos
| Serviço     | URL                          | Login            |
|-------------|------------------------------|------------------|
| Moodle      | https://moodle.localhost     | admin / Admin123! |
| phpMyAdmin  | http://localhost:8081        | root / root      |
| Mailpit     | http://localhost:8025        | —                |

## Estrutura / Volumes (visíveis no projeto)
- `./moodle/`     → código do Moodle (editável no editor; instale plugins aqui)
- `./moodledata/` → dados do Moodle (uploads, cache)
- `./data/mysql/` → dados do banco (persistente)

## Permissões
O container roda o Apache/PHP com **seu UID/GID** (remapeado no build). Assim:
- você edita arquivos no editor, e
- o Moodle no navegador grava (upload, instalar plugin)

ambos como o mesmo usuário, sem conflito.

## Xdebug
Modo `trigger` na porta `9003`, host `host.docker.internal`. No VS Code use a
extensão PHP Debug, `idekey=VSCODE`. Dispare com a flag/extension do navegador.

## Versões
Defina em `.env`: `MOODLE_VERSION` / `MOODLE_BRANCH` (ex.: `5.2.1` / `502`).
A imagem usa `php:8.4-apache` (último patch da série 8.4).
