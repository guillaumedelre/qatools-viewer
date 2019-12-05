FROM composer as src

RUN set -xe; \
    mkdir -p /usr/local/src; \
    cd /usr/local/src; \
        composer init --no-interaction && \
        composer require --optimize-autoloader \
            bamarni/composer-bin-plugin && \
        composer bin phpunit-guy require --optimize-autoloader \
            phploc/phploc \
            sebastian/phpcpd \
            sebastian/phpdcd && \
        composer bin phpmetrics require --optimize-autoloader \
            phpmetrics/phpmetrics && \
        composer bin phpstan require --optimize-autoloader \
            phpstan/phpstan \
            phpstan/phpstan-dibi \
            phpstan/phpstan-doctrine \
            phpstan/phpstan-phpunit \
            phpstan/phpstan-shim \
            phpstan/phpstan-beberlei-assert \
            phpstan/phpstan-deprecation-rules \
            phpstan/phpstan-symfony \
            phpstan/phpstan-strict-rules && \
        composer bin symfony-friendly require --optimize-autoloader \
            friendsoftwig/twigcs \
            friendsofphp/php-cs-fixer \
            heahdude/yaml-linter \
            pdepend/pdepend \
            phan/phan \
            phpmd/phpmd \
            sensiolabs/security-checker \
            squizlabs/php_codesniffer \
            wapmorgan/php-code-fixer && \
        composer bin simple-phpunit require --optimize-autoloader \
            symfony/phpunit-bridge

    ENV PATH /usr/local/src/vendor/bin:$PATH

    RUN simple-phpunit --version

    COPY .php_cs.dist .phpmd.xml phpstan.neon /usr/local/src/

FROM php:7.3-fpm-alpine

COPY --from=src /usr/local/src/ /usr/local/src/
ENV PATH /usr/local/src/vendor/bin:$PATH

RUN set -xe; \
    mkdir -p /home/foo/; \
    apk add --no-cache \
        bash \
        graphviz \
        su-exec; \
    addgroup bar; \
    adduser -D -H -G bar foo

COPY .bashrc /home/foo/.bashrc

WORKDIR /project
VOLUME  /project

ADD entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/

ENTRYPOINT ["entrypoint.sh"]
