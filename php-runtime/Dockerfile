# Build PHP in the Lambda container
FROM amazonlinux:2017.03.1.20170812 as builder

ARG php_version="7.3.1"

RUN sed -i 's;^releasever.*;releasever=2017.03;;' /etc/yum.conf && \
    yum clean all && \
    yum install -y autoconf \
                bison \
                gcc \
                gcc-c++ \
                make \
                libcurl-devel \
                libxml2-devel \
                openssl-devel \
                bzip2-devel \
                tar \
                gzip \
                zip \
                unzip \
                git

RUN curl -sL https://github.com/php/php-src/archive/php-${php_version}.tar.gz | tar -xvz && \
    cd php-src-php-${php_version} && \
    ./buildconf --force && \
    ./configure --prefix=/opt/php/ --with-openssl --with-curl --with-zlib --without-pear --enable-bcmath --with-bz2 --enable-mbstring && \
    make install && \
    /opt/php/bin/php -v && \
    curl -sS https://getcomposer.org/installer | /opt/php/bin/php -- --install-dir=/opt/php/bin/ --filename=composer

RUN mkdir -p /runtime/bin && \
    cp /opt/php/bin/php /runtime/bin/php

COPY src/* /runtime/


# Create runtime container for use with img2lambda
FROM lambci/lambda:provided as runtime

COPY --from=builder /runtime /opt/

