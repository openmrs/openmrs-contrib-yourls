#!/bin/sh
set -e

/usr/local/bin/php /yourls/migrate.php

exec /usr/local/sbin/php-fpm --nodaemonize
