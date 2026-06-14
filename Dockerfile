# syntax=docker/dockerfile:1

###############################################################################
# SaveurIA - Laravel 12 + Vue 3 + Tailwind
# Image de production : Nginx + PHP-FPM + Supervisor (conteneur unique)
# Cible : Coolify (Traefik route le HTTP vers le port 80 expose)
###############################################################################

ARG PHP_VERSION=8.4

###############################################################################
# Stage 1 : Dependances PHP (vendor) avec Composer
###############################################################################
FROM composer:2 AS vendor

WORKDIR /app

# Copie uniquement les fichiers necessaires a l'install pour profiter du cache
COPY composer.json composer.lock ./

# Installe les deps de prod uniquement, sans scripts (artisan pas encore dispo)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --ignore-platform-reqs \
    --prefer-dist \
    --no-interaction

# Copie le reste du code et genere l'autoloader optimise
COPY . .
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

###############################################################################
# Stage 2 : Build des assets front (Vue/Vite)
###############################################################################
FROM node:20-alpine AS node-build

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

# On a besoin du code source + du vendor (ziggy genere les routes au build)
COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

###############################################################################
# Stage 3 : Image finale de production
###############################################################################
FROM php:${PHP_VERSION}-fpm-alpine AS production

# --- Paquets systeme + serveur web + superviseur ---
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    zip \
    intl \
    mbstring \
    pdo_mysql \
    gd \
    opcache \
    bcmath

# --- Config OPcache pour la prod ---
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.enable_cli=0'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=16'; \
    echo 'opcache.max_accelerated_files=20000'; \
    echo 'opcache.validate_timestamps=0'; \
    echo 'opcache.revalidate_freq=0'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# --- Code applicatif + vendor + assets compiles ---
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=node-build --chown=www-data:www-data /app/public/build ./public/build

# --- Nettoyage : retire le marqueur Vite dev (sinon assets casses en prod) ---
RUN rm -f public/hot

# --- Arborescence storage + permissions correctes (pas de 777) ---
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# --- Configs Nginx / PHP-FPM / Supervisor / Entrypoint ---
COPY docker/nginx.conf      /etc/nginx/nginx.conf
COPY docker/php-fpm.conf    /usr/local/etc/php-fpm.d/zz-saveuria.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh   /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Coolify/Traefik route le HTTP vers ce port
EXPOSE 8080

# Health check : Nginx repond a /up (route sante native de Laravel 11+)
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8080/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
