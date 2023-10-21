#!/bin/sh -e
/usr/local/sbin/php-fpm -D
exec nginx -g 'daemon off;'