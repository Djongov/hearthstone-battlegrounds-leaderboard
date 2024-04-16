FROM php:8.3-apache

# Set build-time arguments for secrets
ARG DB_MODE
ARG MYSQL_SSL
ARG DB_HOST
ARG DB_NAME
ARG DB_USER
ARG DB_PASS
ARG JWT_PUBLIC_KEY
ARG JWT_PRIVATE_KEY
ARG AZURE_AD_CLIENT_ID
ARG AZURE_AD_CLIENT_SECRET

#Delete existing .env file, if it exists
RUN rm -f ".env"

# Create and populate the .env file with secrets
RUN echo "DB_MODE=${DB_MODE}" > .env
RUN echo "MYSQL_SSL=${MYSQL_SSL}" >> .env
RUN echo "DB_HOST=${DB_HOST}" >> .env
RUN echo "DB_NAME=${DB_NAME}" >> .env
RUN echo "DB_USER=${DB_USER}" >> .env
RUN echo "DB_PASS=${DB_PASS}" >> .env
RUN echo "AZURE_AD_CLIENT_ID=${AZURE_AD_CLIENT_ID}" >> .env
RUN echo "AZURE_AD_CLIENT_SECRET=${AZURE_AD_CLIENT_SECRET}" >> .env
RUN echo "JWT_PUBLIC_KEY=${JWT_PUBLIC_KEY}" >> .env
RUN echo "JWT_PRIVATE_KEY=${JWT_PRIVATE_KEY}" >> .env

RUN apt-get -y update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
COPY . /var/www/html/
COPY /config/default.conf /etc/apache2/sites-available/000-default.conf
ARG phpIniPath=/usr/local/etc/php/php.ini
#RUN cp /usr/local/etc/php/php.ini-production $phpIniPath
COPY /config/php.ini $phpIniPath
RUN touch /var/log/php_errors.log
RUN chown www-data:www-data /var/log/php_errors.log
RUN chmod 644 /var/log/php_errors.log
RUN chown www-data:www-data /var/tmp
RUN chmod 1733 /var/tmp
RUN docker-php-ext-install mysqli
#RUN docker-php-ext-install mysqli pdo pdo_mysql zip mbstring
#RUN apt-get remove linux headers -y
RUN a2enmod rewrite
RUN a2enmod headers
RUN service apache2 restart
RUN apt-get clean
# Start Apache
#CMD ["/usr/sbin/httpd","-D","FOREGROUND"]