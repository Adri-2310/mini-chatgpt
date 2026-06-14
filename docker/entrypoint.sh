#!/bin/sh
set -e

cd /var/www/html

echo "==> SaveurIA : initialisation du conteneur..."

# Genere APP_KEY si absent (idealement la definir dans Coolify pour la persistance)
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY absent : generation d'une cle temporaire (a definir dans Coolify !)"
    php artisan key:generate --force || true
fi

# Attente de la base de donnees (max ~60s)
echo "==> Attente de la base de donnees ($DB_HOST:$DB_PORT)..."
i=0
until php artisan db:show >/dev/null 2>&1 || [ "$i" -ge 30 ]; do
    i=$((i + 1))
    echo "    DB pas encore prete... ($i/30)"
    sleep 2
done

# Migrations (--force = non interactif en prod)
echo "==> Execution des migrations..."
php artisan migrate --force || echo "!! Migrations echouees (verifier la DB)"

# Cache de prod (config, routes, vues, events)
echo "==> Optimisation des caches Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

# Lien symbolique du storage (si FILESYSTEM_DISK public utilise)
php artisan storage:link 2>/dev/null || true

# Permissions pour les logs
chmod 777 /var/www/html/storage/logs 2>/dev/null || true

echo "==> Initialisation terminee. Demarrage de Supervisor (Nginx + PHP-FPM + workers)."

exec "$@"
