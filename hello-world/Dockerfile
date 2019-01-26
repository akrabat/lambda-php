# Test PHP action locally
#
# docker build -t lambda-php-test . && docker run lambda-php-test handler.hello '{"name": "world"}'

FROM lambda-php-runtime as function

COPY handler.php /var/task/src/handler.php
