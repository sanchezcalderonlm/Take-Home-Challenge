ARG PHP_VERSION
FROM codepso/php:${PHP_VERSION}-fpm-mysql
RUN pecl install redis &&  docker-php-ext-enable redis
RUN apt-get update && apt-get -y install cron
COPY crontab /etc/cron.d/crontab
RUN chmod 0644 /etc/cron.d/crontab
RUN crontab /etc/cron.d/crontab
RUN touch /var/log/cron.log
CMD cron && tail -f /var/log/cron.log
CMD cron && docker-php-entrypoint php-fpm
