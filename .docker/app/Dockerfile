FROM php:8.2-fpm-alpine as php

ENV WORKDIR /var/project
WORKDIR ${WORKDIR}

RUN apk update

# pdftk
RUN apk add openjdk8
RUN wget https://gitlab.com/pdftk-java/pdftk/-/jobs/924565145/artifacts/raw/build/libs/pdftk-all.jar
RUN mv pdftk-all.jar pdftk.jar

RUN touch pdftk
RUN echo "#!/usr/bin/env bash" > pdftk
RUN echo "java -jar \"\$0.jar\" \"\$@\""  >> pdftk
RUN chmod 775 pdftk*
RUN mv pdftk* /usr/local/bin


# Install basic packages
RUN apk --no-cache add curl git bash jq make
# Install nginx
RUN apk --no-cache add nginx
# PHP extensions
RUN docker-php-ext-install pdo_mysql

# XDebug ## Do not install on prod container
RUN apk --no-cache add nano htop
RUN apk add --update linux-headers
RUN apk add --no-cache $PHPIZE_DEPS \
      && pecl install xdebug

# Required for testcoverage report ## Do not install on prod container
RUN pecl install pcov
RUN docker-php-ext-enable pcov
RUN docker-php-ext-configure pcntl --enable-pcntl \
  && docker-php-ext-install \
    pcntl

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# Install npm
RUN apk --no-cache add npm

# Configure environment:
COPY ./.docker/app/nginx/http.d/default.conf /etc/nginx/http.d/default.conf
COPY ./.docker/app/nginx/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/app/fpm/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./.docker/app/fpm/php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./.docker/app/php/php.ini-production /usr/local/etc/php/php.ini

# Fix permissions
RUN chown -R nginx:nginx /var/log/nginx

# Start httpd:
ENTRYPOINT ["sh", "./.docker/app/docker-entrypoint.sh"]
CMD ["sh", "./.docker/app/nginx.sh"]
EXPOSE 80