#!/bin/bash
set -e

# Run the official WordPress entrypoint first (sets up wp-config.php)
source /usr/local/bin/docker-entrypoint.sh

# Wait for DB to be ready
echo "Waiting for database..."
until mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" -e "SELECT 1" &>/dev/null; do
  sleep 2
done

# Import DB if WordPress tables don't exist yet
TABLE_COUNT=$(mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME" -e "SHOW TABLES LIKE 'wp_posts';" 2>/dev/null | wc -l)

if [ "$TABLE_COUNT" -eq 0 ]; then
  echo "Importing database..."
  gunzip -c /docker-entrypoint-initdb.d/dump.sql.gz | mysql -h"$WORDPRESS_DB_HOST" -u"$WORDPRESS_DB_USER" -p"$WORDPRESS_DB_PASSWORD" "$WORDPRESS_DB_NAME"

  # Update siteurl and home to the Railway URL
  if [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    SITE_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    wp --allow-root --path=/var/www/html option update siteurl "$SITE_URL"
    wp --allow-root --path=/var/www/html option update home "$SITE_URL"
    echo "Site URL set to $SITE_URL"
  fi

  # Activate theme and WooCommerce
  wp --allow-root --path=/var/www/html theme activate sanisidro-theme 2>/dev/null || true
  wp --allow-root --path=/var/www/html plugin install woocommerce --activate 2>/dev/null || true

  echo "Setup complete."
fi

exec "$@"
