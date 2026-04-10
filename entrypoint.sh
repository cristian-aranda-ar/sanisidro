#!/bin/bash
set -e

# Run official WP entrypoint to set up files + wp-config.php
/usr/local/bin/docker-entrypoint.sh true

# ── Debug: show available MySQL-related env vars (no passwords) ────────────
echo "[entrypoint] MySQL env vars available:"
env | grep -iE "^(MYSQL|DATABASE|WORDPRESS_DB)" | grep -v -iE "PASS|PASSWORD" | sort || true

# ── Resolve DB connection ──────────────────────────────────────────────────
# Try Railway MySQL plugin variable formats (with and without underscores)
DB_HOST_ONLY="${MYSQL_HOST:-${MYSQLHOST:-${WORDPRESS_DB_HOST%%:*}}}"
DB_PORT="${MYSQL_PORT:-${MYSQLPORT:-3306}}"
DB_USER="${MYSQL_USER:-${MYSQLUSER:-${WORDPRESS_DB_USER}}}"
DB_PASS="${MYSQL_PASSWORD:-${MYSQLPASSWORD:-${WORDPRESS_DB_PASSWORD}}}"
DB_NAME="${MYSQL_DATABASE:-${MYSQLDATABASE:-${WORDPRESS_DB_NAME}}}"

# If host still empty, try parsing from MYSQL_URL or DATABASE_URL
if [ -z "$DB_HOST_ONLY" ] && [ -n "${MYSQL_URL:-${DATABASE_URL:-}}" ]; then
  URL="${MYSQL_URL:-$DATABASE_URL}"
  DB_HOST_ONLY=$(echo "$URL" | sed -E 's|.*@([^:/]+)[:/].*|\1|')
  DB_PORT=$(echo "$URL"     | sed -E 's|.*@[^:]+:([0-9]+)/.*|\1|')
  DB_NAME=$(echo "$URL"     | sed -E 's|.*/([^?]+).*|\1|')
  DB_USER=$(echo "$URL"     | sed -E 's|.*//([^:]+):.*|\1|')
  DB_PASS=$(echo "$URL"     | sed -E 's|.*://[^:]+:([^@]+)@.*|\1|')
fi

echo "[entrypoint] DB → host=$DB_HOST_ONLY port=$DB_PORT db=$DB_NAME"

if [ -z "$DB_HOST_ONLY" ]; then
  echo "[entrypoint] ERROR: Could not resolve DB host. Check your Railway env vars."
  exit 1
fi

# ── Wait for DB ────────────────────────────────────────────────────────────
echo "[entrypoint] Waiting for database..."
for i in $(seq 1 40); do
  if mysqladmin ping -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" --silent 2>/dev/null; then
    echo "[entrypoint] Database ready."
    break
  fi
  [ "$i" -eq 40 ] && echo "[entrypoint] Gave up waiting for DB." && exit 1
  echo "[entrypoint] Attempt $i/40 — retrying in 3s..."
  sleep 3
done

# ── Import DB if tables missing ────────────────────────────────────────────
TABLE_COUNT=$(mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" \
  -e "SHOW TABLES LIKE 'wp_posts';" 2>/dev/null | wc -l)

if [ "$TABLE_COUNT" -eq 0 ]; then
  echo "[entrypoint] Importing database..."
  gunzip -c /docker-entrypoint-initdb.d/dump.sql.gz \
    | mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME"

  if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    SITE_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    echo "[entrypoint] Setting site URL to $SITE_URL"
    wp --allow-root --path=/var/www/html option update siteurl "$SITE_URL"
    wp --allow-root --path=/var/www/html option update home "$SITE_URL"
  fi

  wp --allow-root --path=/var/www/html theme activate sanisidro-theme 2>/dev/null || true
  echo "[entrypoint] Setup complete."
else
  echo "[entrypoint] Database already imported, skipping."
fi

exec apache2-foreground
