FROM php:7.3-fpm-alpine
RUN apk add --update-cache \
        curl \
        icu-dev \
        php \
        php-mbstring \
        php-ctype \
        php-iconv \
        php-intl \
        php-sqlite3 \
        git
RUN docker-php-ext-install intl
COPY /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
RUN composer install
COPY crontab.txt /crontab.txt
COPY script.sh /script.sh
COPY entry.sh /entry.sh
RUN chmod 755 /script.sh /entry.sh
RUN /usr/bin/crontab /crontab.txt
CMD ["/entry.sh"]
EXPOSE 9000