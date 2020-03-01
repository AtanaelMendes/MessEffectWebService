FROM php:7.1-apache

ENV XDEBUG_PORT 9000

# Instalando dependências do sistema
RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    software-properties-common \
    && apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y \
    vim \
    curl \
    git \
    wget \
    unzip \
    zlib1g-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libxml2-dev \
    && apt-get clean


# Instalando dependências do PHP
RUN docker-php-ext-configure \
    gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/; \
    docker-php-ext-install \
    mbstring \
    zip \
    pdo \
    pdo_pgsql \
    soap \
    gd

# Instalando composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Instalando xDebug
RUN yes | pecl install xdebug && \
    echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini

ADD config/php.ini /usr/local/etc/php/php.ini
ADD config/000-default.conf /etc/apache2/sites-available/000-default.conf
ADD config/custom-xdebug.ini /usr/local/etc/php/conf.d/custom-xdebug.ini
COPY bin/* /usr/local/bin/


RUN chmod 777 -Rf /var/www /var/www/.* \
	&& chown -Rf www-data:www-data /var/www /var/www/.* \
	&& usermod -u 1000 www-data \
	&& chsh -s /bin/bash www-data\
	&& a2enmod rewrite \
	&& a2enmod headers
