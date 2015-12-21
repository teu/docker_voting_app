FROM phusion/baseimage:latest

RUN rm /bin/sh && ln -s /bin/bash /bin/sh

ENV HOME /root

RUN rm -rf /var/lib/apt/lists/* 

CMD ["/sbin/my_init"]

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_SERVERADMIN admin@localhost
ENV APACHE_SERVERNAME localhost
ENV APACHE_SERVERALIAS docker.localhost
ENV APACHE_DOCUMENTROOT /var/www

RUN apt-get update && \
    DEBIAN_FRONTEND=noninteractive apt-get -yq install \
        curl \
        apache2 \
        libapache2-mod-php5 \
        php5-mysql \
        php5-mcrypt \
        php5-gd \
        php5-curl \
        php-pear \
        php5-json && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get clean

ADD ./app.conf /etc/apache2/sites-available/app.conf

RUN a2dissite 000-default
RUN a2ensite app
RUN a2enmod rewrite 

RUN rm -f /etc/service/sshd/down

# Regenerate SSH host keys. baseimage-docker does not contain any, so you
# have to do that yourself. You may also comment out this instruction; the
# init system will auto-generate one during boot.
RUN /etc/my_init.d/00_regen_ssh_host_keys.sh

#enable insecure key
RUN /usr/sbin/enable_insecure_key

RUN mkdir /etc/service/apache2
ADD apache.sh /etc/service/apache2/run
RUN chmod 0755 /etc/service/apache2/run

EXPOSE 80

