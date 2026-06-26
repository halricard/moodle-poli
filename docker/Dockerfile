# Moodle dev image - PHP 8.4 + Apache
# PHP 8.4 latest patch (>= 8.4.22). Pin here if exact patch needed.
FROM php:8.4-apache

ARG HOST_UID=1000
ARG HOST_GID=1000
ARG NODE_VERSION=22
ARG MOODLE_HOST=moodle.localhost

ENV DEBIAN_FRONTEND=noninteractive

# --- System libs + build deps for PHP extensions ---
# Base php:8.4 já traz: curl ctype dom fileinfo filter hash iconv mbstring
# openssl pcre simplexml tokenizer xml xmlreader xmlwriter sodium opcache.
# Instalamos apenas o que falta p/ o Moodle: gd intl zip soap exif mysqli ldap bcmath.
RUN apt-get update && apt-get install -y --no-install-recommends \
      libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev \
      libicu-dev libzip-dev libxml2-dev libldap2-dev \
      git unzip ghostscript ca-certificates curl \
    # --- Node + npm (theme/grunt builds) ---
    && curl -fsSL "https://deb.nodesource.com/setup_${NODE_VERSION}.x" | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    # --- PHP extensions exigidas/recomendadas pelo Moodle (faltantes na base) ---
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install -j"$(nproc)" \
       gd intl zip soap exif mysqli ldap bcmath \
    # --- Xdebug ---
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    # --- Cleanup (disk optimization) ---
    && apt-get purge -y --auto-remove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* /usr/share/man/* \
    && rm -rf /usr/local/lib/php/test /usr/local/lib/php/doc

# --- Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- PHP config ---
COPY docker/php/php.ini      /usr/local/etc/php/conf.d/zz-moodle.ini
COPY docker/php/opcache.ini  /usr/local/etc/php/conf.d/zz-opcache.ini
COPY docker/php/xdebug.ini   /usr/local/etc/php/conf.d/zz-xdebug.ini

# --- Apache config ---
COPY docker/apache/moodle.conf /etc/apache2/sites-available/moodle.conf
RUN a2enmod rewrite ssl headers \
    && a2dissite 000-default \
    && a2ensite moodle \
    && printf "ServerName %s\n" "${MOODLE_HOST}" >> /etc/apache2/apache2.conf

# --- Remap www-data to host user so editor + browser share write perms ---
RUN if [ "${HOST_GID}" != "33" ]; then groupmod -g "${HOST_GID}" www-data || true; fi \
    && if [ "${HOST_UID}" != "33" ]; then usermod  -u "${HOST_UID}" www-data || true; fi

WORKDIR /var/www/html
