#!/bin/bash
set -e

# Run official WP entrypoint to set up files + wp-config.php
/usr/local/bin/docker-entrypoint.sh true

# ── Resolve DB connection ──────────────────────────────────────────────────
# Railway MySQL plugin exposes MYSQL_HOST / MYSQL_PORT directly.
# WORDPRESS_DB_HOST may be "host:port" or just "host" or empty.

if [ -n "$MYSQL_HOST" ]; then
  DB_HOST_ONLY="$MYSQL_HOST"
  DB_PORT="${MYSQL_PORT:-3306}"
elif [ -n "$WORDPRESS_DB_HOST" ]; then
  DB_HOST_ONLY="${WORDPRESS_DB_HOST%%:*}"
  DB_PORT="${WORDPRESS_DB_HOST##*:}"
  # If no colon was present, port equals host — reset to default
  [ "$DB_PORT" = "$DB_HOST_ONLY" ] && DB_PORT="3306"
else
  echo "[entrypoint] ERROR: No DB host found in env vars. Aborting."
  exit 1
fi

DB_USER="${MYSQL_USER:-${WORDPRESS_DB_USER}}"
DB_PASS="${MYSQL_PASSWORD:-${WORDPRESS_DB_PASSWORD}}"
DB_NAME="${MYSQL_DATABASE:-${WORDPRESS_DB_NAME}}"

echo "[entrypoint] DB → $DB_HOST_ONLY:$DB_PORT / $DB_NAME"

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
