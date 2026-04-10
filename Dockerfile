FROM wordpress:apache

# Install WP-CLI
RUN curl -sO https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# Install mysql client for DB import and gunzip
RUN apt-get update && apt-get install -y default-mysql-client gunzip && rm -rf /var/lib/apt/lists/*

# Copy theme into image
COPY theme/sanisidro-theme /var/www/html/wp-content/themes/sanisidro-theme

# Copy DB dump
COPY db/dump.sql.gz /docker-entrypoint-initdb.d/dump.sql.gz

# Copy custom entrypoint
COPY entrypoint.sh /usr/local/bin/custom-entrypoint.sh
RUN chmod +x /usr/local/bin/custom-entrypoint.sh

ENTRYPOINT ["custom-entrypoint.sh"]
CMD ["apache2-foreground"]
