FROM php:8.2-apache

RUN a2dismod mpm_event || true \
    && a2dismod mpm_worker || true \
    && a2dismod mpm_prefork || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]