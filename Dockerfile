FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN chown -R www-data:www-data /var/www

COPY ./server-default.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY . .
# RUN composer install --ignore-platform-reqs

EXPOSE 8080
CMD ["apache2-foreground"]
