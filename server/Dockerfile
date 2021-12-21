# Default Dockerfile

FROM qingfengzui/php-alpine-docker:swoole-prod-v7.4-v3.12-v4.6.7
#FROM qbhy/hyperf:latest

##
# ---------- env settings ----------
##
# --build-arg timezone=Asia/Shanghai
ARG timezone

ENV TIMEZONE=${timezone:-"Asia/Shanghai"} \
    APP_ENV=dev \
    SCAN_CACHEABLE=(false)

# update
RUN set -ex \
    # show php version and extensions
    && php -v \
    && php -m \
    && php --ri swoole \
    #  ---------- some config ----------
    && cd /etc/php7 \
    # - config PHP
    && { \
        echo "upload_max_filesize=1024M"; \
        echo "post_max_size=1024M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www
# Composer Cache
# COPY ./composer.* /opt/www/
# RUN composer install --no-dev --no-scripts

COPY . /opt/www

# 修改 composer 镜像源
 RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
#RUN composer config -g repo.packagist composer https://packagist.phpcomposer.com
#RUN composer config -g repos.packagist composer https://mirrors.tencent.com/composer/

RUN pwd && ls -l && composer install --no-dev -o  && php bin/hyperf.php

EXPOSE 9602

ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
