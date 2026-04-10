#!/bin/bash
set -e

# ── Fix Apache MPM conflict ────────────────────────────────────────────────────
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# ── Copy WordPress core files if not present ──────────────────────────────────
if [ ! -f /var/www/html/index.php ]; then
  echo "[entrypoint] Copying WordPress core files from /usr/src/wordpress..."
  tar -cf - --one-file-system -C /usr/src/wordpress . | tar xf - -C /var/www/html/
  chown -R www-data:www-data /var/www/html
  echo "[entrypoint] WordPress core files copied."
fi

# ── Debug: show DB-related env vars (no passwords) ────────────────────────────
echo "[entrypoint] DB env vars:"
env | grep -iE "(MYSQL|DATABASE|DB_|_DB)" | grep -v -iE "(PASS|SECRET|PWD)" | sort || true

# ── Resolve DB connection ──────────────────────────────────────────────────────
_URL="${MYSQL_PRIVATE_URL:-${MYSQL_URL:-${DATABASE_URL:-}}}"
if [ -n "$_URL" ]; then
  DB_USER=$(echo "$_URL" | sed -E 's|^[a-z]+://([^:]+):.*|\1|')
  DB_PASS=$(echo "$_URL" | sed -E 's|^[a-z]+://[^:]+:([^@]+)@.*|\1|')
  DB_HOST_ONLY=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@([^:/]+)[:/].*|\1|')
  DB_PORT=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@[^:]+:([0-9]+)/.*|\1|')
  DB_NAME=$(echo "$_URL" | sed -E 's|^[a-z]+://[^@]+@[^/]+/([^?]+).*|\1|')
  [ -z "$DB_PORT" ] || ! echo "$DB_PORT" | grep -qE '^[0-9]+$' && DB_PORT=3306
fi

DB_HOST_ONLY="${DB_HOST_ONLY:-${MYSQL_HOST:-${MYSQLHOST:-${WORDPRESS_DB_HOST%%:*}}}}"
DB_PORT="${DB_PORT:-${MYSQL_PORT:-${MYSQLPORT:-3306}}}"
DB_USER="${DB_USER:-${MYSQL_USER:-${MYSQLUSER:-${WORDPRESS_DB_USER}}}}"
DB_PASS="${DB_PASS:-${MYSQL_PASSWORD:-${MYSQLPASSWORD:-${WORDPRESS_DB_PASSWORD}}}}"
DB_NAME="${DB_NAME:-${MYSQL_DATABASE:-${MYSQLDATABASE:-${WORDPRESS_DB_NAME}}}}"

echo "[entrypoint] DB → host=$DB_HOST_ONLY port=$DB_PORT db=$DB_NAME user=$DB_USER"

if [ -z "$DB_HOST_ONLY" ]; then
  echo "[entrypoint] ERROR: Could not resolve DB host."
  exit 1
fi

# ── Generate wp-config.php if missing ─────────────────────────────────────────
if [ ! -f /var/www/html/wp-config.php ]; then
  echo "[entrypoint] Generating wp-config.php..."
  wp config create \
    --allow-root \
    --path=/var/www/html \
    "--dbhost=${DB_HOST_ONLY}:${DB_PORT}" \
    "--dbname=${DB_NAME}" \
    "--dbuser=${DB_USER}" \
    "--dbpass=${DB_PASS}" \
    "--dbprefix=${WORDPRESS_TABLE_PREFIX:-wp_}" \
    --skip-check \
    --extra-php <<'PHP'
/* Trust Railway's HTTPS proxy so WordPress generates https:// URLs */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
    $_SERVER['HTTPS'] = 'on';
}
PHP
  echo "[entrypoint] wp-config.php created."
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
  echo "[entrypoint] Import done."
else
  echo "[entrypoint] Database already imported, skipping."
fi

# ── Install WooCommerce if not present ────────────────────────────────────────
if [ ! -d /var/www/html/wp-content/plugins/woocommerce ]; then
  echo "[entrypoint] Installing WooCommerce..."
  wp --allow-root --path=/var/www/html plugin install woocommerce --activate
  echo "[entrypoint] WooCommerce installed."
fi

# ── Ensure HTTPS proxy fix is in wp-config.php ───────────────────────────────
if ! grep -q 'HTTP_X_FORWARDED_PROTO' /var/www/html/wp-config.php 2>/dev/null; then
  echo "[entrypoint] Adding HTTPS proxy fix to wp-config.php..."
  sed -i "s|<?php|<?php\n/* Trust Railway HTTPS proxy */\nif ( isset( \$_SERVER['HTTP_X_FORWARDED_PROTO'] ) \&\& \$_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) { \$_SERVER['HTTPS'] = 'on'; }|" \
    /var/www/html/wp-config.php
fi

# ── Always update site URL and activate theme ─────────────────────────────────
if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
  SITE_URL="https://$RAILWAY_PUBLIC_DOMAIN"
  echo "[entrypoint] Setting site URL to $SITE_URL"
  wp --allow-root --path=/var/www/html option update siteurl "$SITE_URL" || true
  wp --allow-root --path=/var/www/html option update home "$SITE_URL" || true
  wp --allow-root --path=/var/www/html theme activate sanisidro-theme 2>/dev/null || true
fi

# ── Fix permissions & start Apache ────────────────────────────────────────────
chown -R www-data:www-data /var/www/html 2>/dev/null || true

exec apache2-foreground
