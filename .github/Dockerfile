FROM drupal:9-fpm-buster

# Update all the packages - to solve the security vulnerabilities.
RUN apt-get update \
    && apt-get -y upgrade \
    && apt-get install -y libpq-dev \
      mariadb-client \
      libmemcached-dev \
      libicu-dev \
      zlib1g-dev \
      curl

# This is required for backup & migrate (Drupal Contrib Module).
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli && docker-php-ext-enable mysqli \
    && apt-get remove libicu-dev icu-devtools -y

# Install composer - we want to have it because we have some dependencies on it.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Drush
#RUN composer global require drush/drush && \
#    composer global update && \
#    echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> $HOME/.bashrc

# Update the damn path.
ENV PATH="$HOME/.composer/vendor/bin:${PATH}"

# Project-specific ini settings
COPY .github/z-php-overrides.ini /usr/local/etc/php/conf.d

# Replace template Drupal with ours.
WORKDIR /var/www/html
RUN rm -rf /var/www/html
COPY --chown=www-data:www-data . /var/www/html


EXPOSE 9000
CMD ["php-fpm"]
