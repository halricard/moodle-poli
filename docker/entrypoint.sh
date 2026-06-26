#!/bin/bash
set -e

# Confia na CA local (mkcert rootCA ou cert self-signed) para que requisições
# server-side do Moodle (ex.: check do router) validem https://moodle.localhost.
if [ -f /etc/apache2/certs/rootCA.pem ]; then
  cp /etc/apache2/certs/rootCA.pem /usr/local/share/ca-certificates/moodle-local-ca.crt
  update-ca-certificates >/dev/null 2>&1 || true
fi

exec docker-php-entrypoint apache2-foreground
