FROM php:8.3-apache

# Włącz moduł rewrite
RUN a2enmod rewrite

# Zainstaluj pakiety, rozszerzenia PHP i oczyść po sobie
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    git \
    wget \
    libicu-dev \
    libmagickwand-dev \
    librabbitmq-dev \
    libssh-dev \
    software-properties-common \
    npm \
    && docker-php-ext-install pdo mysqli pdo_mysql zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && npm install npm@latest -g \
    && npm install n -g \
    && n latest \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Pobierz i zainstaluj Composer
RUN wget https://getcomposer.org/download/2.0.9/composer.phar \
    && mv composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

# Ustaw zmienną środowiskową dla katalogu dokumentów Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Zmień konfigurację Apache, aby wskazywała na nowy katalog
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Ustaw domyślne polecenie do uruchomienia Apache
CMD ["apache2-foreground"]
