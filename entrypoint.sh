#!/bin/bash
set -e

# Run official WP entrypoint to set up files + wp-config.php
# Passing 'true' makes it exit after setup without starting Apache
/usr/local/bin/docker-entrypoint.sh true

# Parse DB host (may include :port)
DB_HOST_ONLY=$(echo "$WORDPRESS_DB_HOST" | cut -d: -f1)
DB_PORT=$(echo "$WORDPRESS_DB_HOST" | grep -o ':[0-9]*' | tr -d ':')
DB_PORT=${DB_PORT:-3306}

# Wait for DB to be ready
echo "[entrypoint] Waiting for database at $DB_HOST_ONLY:$DB_PORT..."
for i in $(seq 1 30); do
  if mysqladmin ping -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" --silent 2>/dev/null; then
    echo "[entrypoint] Database ready."
    break
  fi
  echo "[entrypoint] Attempt $i/30 — retrying in 3s..."
  sleep 3
done

# Import DB only if WordPress tables don't exist yet
TABLE_COUNT=$(mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" \
  -e "SHOW TABLES LIKE 'wp_posts';" 2>/dev/null | wc -l)

if [ "$TABLE_COUNT" -eq 0 ]; then
  echo "[entrypoint] Importing database..."
  gunzip -c /docker-entrypoint-initdb.d/dump.sql.gz \
    | mysql -h"$DB_HOST_ONLY" -P"$DB_PORT" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME"

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

# Start Apache
exec apache2-foreground
