#!/bin/sh
set -eu

export PORT="${PORT:-8080}"

rm -f /var/www/html/data/cache/config-cache.php
sed "s|\${PORT}|${PORT}|g" /etc/nginx/templates/default.conf.template > /etc/nginx/conf.d/default.conf

echo "Railway runtime check: PORT=${PORT}"
echo "Railway runtime check: public/index.php=$(test -f /var/www/html/public/index.php && echo present || echo missing)"
echo "Railway runtime check: DB_HOST=$(if [ -n "${DB_HOST:-${MYSQLHOST:-}}" ]; then echo set; else echo missing; fi)"
echo "Railway runtime check: DB_PORT=$(if [ -n "${DB_PORT:-${MYSQLPORT:-}}" ]; then echo set; else echo missing; fi)"
echo "Railway runtime check: DB_NAME=$(if [ -n "${DB_NAME:-${MYSQLDATABASE:-}}" ]; then echo set; else echo missing; fi)"
echo "Railway runtime check: DB_USER=$(if [ -n "${DB_USER:-${MYSQLUSER:-}}" ]; then echo set; else echo missing; fi)"
echo "Railway runtime check: DB_PASS=$(if [ -n "${DB_PASS:-${MYSQLPASSWORD:-}}" ]; then echo set; else echo missing; fi)"
echo "Railway runtime check: DATABASE_URL=$(if [ -n "${DATABASE_URL:-${MYSQL_URL:-}}" ]; then echo set; else echo missing; fi)"

php-fpm -D
exec nginx -g "daemon off;"
