############################################################
# Dockerfile to build a wyn container image
# Based on Ubuntu
############################################################

# Set the base image to Ubuntu
FROM ubuntu:latest

# File Author / Maintainer
MAINTAINER kriskbx

# Silence debconf
ENV DEBIAN_FRONTEND noninteractive

# Common environment variables
ENV COMPOSER_HOME /.composer
ENV CONF_DIR_PHP7_CLI /etc/php/7.0/cli
ENV PATH ${PATH}:/.composer/vendor/bin

RUN \

# Add PPA for PHP 7.0
echo '' >> /etc/apt/sources.list && \
echo '# PPA for PHP 7.0' >> /etc/apt/sources.list && \
echo 'deb http://ppa.launchpad.net/ondrej/php-7.0/ubuntu trusty main' >> /etc/apt/sources.list && \
echo 'deb-src http://ppa.launchpad.net/ondrej/php-7.0/ubuntu trusty main' >> /etc/apt/sources.list && \
echo '' >> /etc/apt/sources.list && \
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys E5267A6C && \

# All our dependencies
apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    git \
    php7.0-cli \
    php7.0-curl \
    php7.0-gd \
    php-imagick \
    php7.0-intl \
    php7.0-json \
    php7.0-ldap \
    php7.0-mcrypt \
    php7.0-mysql \
    php7.0-pgsql \
    php7.0-sqlite3 \
    xdg-utils \
    vim && \

# Remove cache
apt-get clean && rm -rf /var/lib/apt/lists/* && \

# Install composer
curl -sS http://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \

# Speed up composer
composer global require hirak/prestissimo

# Install wyn
ADD build/wyn.phar /usr/local/bin/wyn
RUN chmod a+x /usr/local/bin/wyn

# Preserve data
VOLUME ["/root/.composer", "/root/.wyn"]

# Set default container command
CMD ["backup:daemon"]

# Set entrypoint
ENTRYPOINT ["/usr/local/bin/wyn"]

