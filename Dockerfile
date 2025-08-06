FROM php:8.4.6-apache
RUN apt-get update && apt-get install -y git zip unzip libicu-dev
RUN docker-php-ext-install intl

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod headers rewrite
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY tempest /var/www/html/tempest
RUN mkdir /var/www/html/app && chown www-data:www-data /var/www/html/app

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.json /var/www/html/composer.json
COPY composer.lock /var/www/html/composer.lock
ENV COMPOSER_HOME=/.composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN --mount=type=cache,target=/.composer/cache composer install --no-dev --optimize-autoloader

COPY app /var/www/html/app
COPY public /var/www/html/public
COPY .env /var/www/html/.env

RUN chown -R www-data:www-data /var/www/html
RUN php tempest discovery:generate

EXPOSE 80