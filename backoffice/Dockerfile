FROM php:8.2-apache

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql \
	&& a2enmod rewrite headers expires deflate

COPY public/ /var/www/html/public/
COPY includes/ /var/www/html/includes/

RUN chown -R www-data:www-data /var/www/html

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
	&& sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

RUN printf "<Directory /var/www/html/public>\n    AllowOverride All\n</Directory>\n" > /etc/apache2/conf-available/allow-override.conf \
	&& a2enconf allow-override

EXPOSE 80


