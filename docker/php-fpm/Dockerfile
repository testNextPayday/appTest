FROM phpdockerio/php73-fpm:latest
WORKDIR "/application"
COPY ./ ./

# Fix debconf warnings upon build
ARG DEBIAN_FRONTEND=noninteractive

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.3-bcmath  php7.3-mysql php-redis php-xdebug php7.3-gd \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    && chmod -R 777 storage