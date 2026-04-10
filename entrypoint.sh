#!/bin/bash
set -e

# Run official WP entrypoint to set up files + wp-config.php
/usr/local/bin/docker-entrypoint.sh true

# ── Debug: show ALL env vars that might contain DB info ───────────────────────
echo "[entrypoint] All DB-related env vars (no passwords):"
env | grep -iE "(MYSQL|DATABASE|POSTGRES|DB_|_DB)" | grep -v -iE "(PASS|SECRET|PWD)" | sort || true

# ── Resolve from URL first (most reliable on Railway) ─────────────────────────
# Railway typically exposes MYSQL_PRIVATE_URL or DATABASE_URL for linked services
_URL="${MYSQL_PRIVATE_URL:-${MYSQL_URL:-${DATABASE_URL:-}}}"

if [ -n "$_URL" ]; then
  echo "[entrypoint] Parsing connection from URL..."
  # mysql://user:pass@host:port/dbname  OR  mysql://user:pass@host/dbname
  DB_USER=$(echo "$_URL" | sed -E 's|^[a-z]+://([^:]+):.*|\1|')
  DB_PASS=$(echo "$_URL" | sed -E 's|^[a-z]+://[^:]+:([^@]+)@.*|\1|')
  DB_HOST_ONLY=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@([^:/]+)[:/].*|\1|')
  DB_PORT=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@[^:]+:([0-9]+)/.*|\1|')
  DB_NAME=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@[^/]+/([^?]+).*|\1|')
  # Port may not be in URL — default to 3306
  [ -z "$DB_PORT" ] || ! echo "$DB_PORT" | grep -qE '^[0-9]+$' && DB_PORT=3306
fi

# ── Fallback: individual env vars (many naming conventions) ───────────────────
DB_HOST_ONLY="${DB_HOST_ONLY:-${MYSQL_HOST:-${MYSQLHOST:-${MYSQL_PRIVATE_HOST:-${WORDPRESS_DB_HOST%%:*}}}}}"
DB_PORT="${DB_PORT:-${MYSQL_PORT:-${MYSQLPORT:-${MYSQL_PRIVATE_PORT:-3306}}}}"
DB_USER="${DB_USER:-${MYSQL_USER:-${MYSQLUSER:-${WORDPRESS_DB_USER}}}}"
DB_PASS="${DB_PASS:-${MYSQL_PASSWORD:-${MYSQLPASSWORD:-${WORDPRESS_DB_PASSWORD}}}}"
DB_NAME="${DB_NAME:-${MYSQL_DATABASE:-${MYSQLDATABASE:-${WORDPRESS_DB_NAME}}}}"

echo "[entrypoint] DB → host=$DB_HOST_ONLY port=$DB_PORT db=$DB_NAME user=$DB_USER"

if [ -z "$DB_HOST_ONLY" ]; then
  echo "[entrypoint] ERROR: Could not resolve DB host."
  echo "[entrypoint] Set WORDPRESS_DB_HOST (and USER/PASSWORD/NAME) in your Railway service variables,"
  echo "[entrypoint] referencing your MySQL service: e.g. \${{MySQL.MYSQLHOST}}"
  exit 1
fi

# ── Wait for DB ────────────────────────────────────────────────────────────────
echo "[entrypoint] Waiting for database at $DB_HOST_ONLY:$DB_PORT..."
export MYSQL_PWD="$DB_PASS"
for i in $(seq 1 40); do
  if mysqladmin ping -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" --silent --connect-timeout=3 2>/dev/null; then
    echo "[entrypoint] Database ready."
    break
  fi
  [ "$i" -eq 40 ] && echo "[entrypoint] Gave up waiting for DB." && exit 1
  echo "[entrypoint] Attempt $i/40 — retrying in 3s..."
  sleep 3
done

# ── Import DB if tables missing ───────────────────────────────────────────────
TABLE_COUNT=$(mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" "$DB_NAME" \
  -e "SHOW TABLES LIKE 'wp_posts';" 2>/dev/null | wc -l)

if [ "$TABLE_COUNT" -eq 0 ]; then
  echo "[entrypoint] Importing database..."
  gunzip -c /docker-entrypoint-initdb.d/dump.sql.gz \
    | mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$DB_USER" "$DB_NAME"

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
