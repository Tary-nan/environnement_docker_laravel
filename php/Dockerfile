#FROM php:7.4-fpm
FROM php:7.4-apache

# Arguments defined in docker-compose.yml
ARG USER
ARG USER_ID
ARG GROUP_ID
ARG USER
ARG NODE_VERSION

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli && a2enmod rewrite

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Get latest node
RUN curl --silent -SL https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz | tar -xJC /opt
ENV PATH=/opt/node-v$NODE_VERSION-linux-x64/bin:$PATH
COPY ./apache/demo.apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY ./bashrc /root/.bashrc

# Create system user to run Composer and Artisan Commands
RUN useradd $USER --shell=/bin/bash -g www-data -g root
# RUN mkdir -p /home/$USER/.composer
COPY ./bashrc /home/$USER/.bashrc
COPY ./laravel/user.sh /home/$USER/
COPY ./laravel/run_on_dock_1st_time.sh /home/$USER/
# Pour git
RUN groupadd $USER
RUN chown -R $USER.$USER /home/$USER

# Set working directory
WORKDIR /var/www/html
