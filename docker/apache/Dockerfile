FROM ubuntu:bionic

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -yqq --no-install-recommends \
    ca-certificates \
    curl \
    apache2 \
    libapache2-mod-php \
    php7.2-cli \
    php7.2-bcmath \
    php7.2-bz2 \
    php7.2-curl \
    php7.2-intl \
    php7.2-json \
    php7.2-mbstring \
    php7.2-mysql \
    php7.2-xml \
    php7.2-xmlrpc \
    php7.2-xsl \
    php7.2-zip \
    && rm -rf /var/www \
    && mkdir -p /var/www \
    && chown www-data:www-data /var/www \
    && rm -rf /var/log/apache2/* \
    && rm -rf /var/lib/apt/lists/*

COPY image/ /

RUN a2enmod rewrite \
    && a2disconf charset localized-error-pages other-vhosts-access-log serve-cgi-bin \
    && a2enconf server-name

ARG APACHE_DBG=0

RUN if [ "$APACHE_DBG" = 1 ] ; then phpenmod dbg ; fi

RUN ln -sf /dev/stdout /var/log/apache2/access.log
RUN ln -sf /dev/stderr /var/log/apache2/error.log

EXPOSE 80/tcp

WORKDIR /var/www

RUN curl -sSL https://getcomposer.org/download/1.6.5/composer.phar -o /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer

ENTRYPOINT ["/usr/local/sbin/docker-entrypoint.sh"]

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
