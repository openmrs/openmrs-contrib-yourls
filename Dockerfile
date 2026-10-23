FROM php:8.5.12-fpm-alpine

ENV APP_ROOT /yourls

# YOURLS publishes no checksums, so this is the SHA-256 of GitHub's archive for the tag. Update both together.
ARG YOURLS_VERSION=1.10.6
ARG YOURLS_SHA256=95549b8463c67a88eaa93f5dd8079599bc895f5ca047001ce385d1afdda26563

RUN mkdir -p ${APP_ROOT} \
  && apk add --no-cache curl nginx supervisor \
  && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
  && docker-php-ext-install pdo_mysql \
  && curl -fsSL -o /tmp/yourls.tar.gz https://github.com/YOURLS/YOURLS/archive/${YOURLS_VERSION}.tar.gz \
  && echo "${YOURLS_SHA256}  /tmp/yourls.tar.gz" | sha256sum -c - \
  && tar -zxf /tmp/yourls.tar.gz -C ${APP_ROOT} --strip-components=1 \
  && rm /tmp/yourls.tar.gz

COPY docker/start-yourls.sh /usr/bin/start-yourls.sh
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/zz-yourls.conf
COPY docker/config.php ${APP_ROOT}/user/config.php
COPY docker/migrate.php ${APP_ROOT}/migrate.php
COPY docker/index.php ${APP_ROOT}/index.php
COPY plugins ${APP_ROOT}/user/plugins

WORKDIR ${APP_ROOT}

RUN chown -R www-data:www-data ${APP_ROOT}

EXPOSE 80
CMD ["/usr/bin/supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]
