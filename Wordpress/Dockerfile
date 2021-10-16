FROM alpine:latest

# application folder
ARG REGION=ap-southeast-2
ENV AWS_DEFAULT_REGION=${REGION}
ENV RDS_DNS=${RDS_DNS}
ENV RDS_DBNAME=${RDS_DBNAME}
ENV RDS_USERNAME=${RDS_USERNAME}
ENV RDS_PASSWORD=${RDS_PASSWORD}

# Build the Application
RUN \
apk update --no-cache && \
apk add --no-cache bash wget curl lighttpd php7 php7-common php7-session php7-iconv \
php7-json php7-gd php7-curl php7-xml php7-mysqli php7-imap php7-cgi fcgi \
php7-pdo php7-pdo_mysql php7-soap php7-xmlrpc php7-posix php7-mcrypt \
php7-gettext php7-ldap php7-ctype php7-dom php7-zlib php7-mysqlnd && \
sed -i '/mod_fastcgi.conf/s/^#//' /etc/lighttpd/lighttpd.conf && \
sed -i 's/htdocs/htdocs\/wordpress/g' /etc/lighttpd/lighttpd.conf && \
sed -i '/server.port/s/^#//' /etc/lighttpd/lighttpd.conf && \
sed -i 's/81/8000/g' etc/lighttpd/lighttpd.conf && \
mkdir -p /usr/share/webapps/ && \
cd /usr/share/webapps/ && \
wget http://wordpress.org/latest.tar.gz && \
tar -xzvf latest.tar.gz && \
rm latest.tar.gz && \
ln -s /usr/share/webapps/wordpress/ /var/www/localhost/htdocs/wordpress && \
mkdir /var/run/lighttpd && \
touch /var/run/lighttpd/php-fastcgi.socket /run/lighttpd.pid

COPY wp-config.php /var/www/localhost/htdocs/wordpress/wp-config.php
COPY start.sh start.sh

RUN \
chown -R lighttpd:lighttpd /var/www/localhost/htdocs/ /usr/share/webapps/ /var/run/lighttpd /etc/lighttpd/lighttpd.conf /var/log/lighttpd/ /var/run/lighttpd /run/lighttpd.pid && \
chown lighttpd:lighttpd start.sh && \
chmod 744 start.sh

EXPOSE 8000

USER lighttpd

# exectute start up script
#ENTRYPOINT ["lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]
ENTRYPOINT ["./start.sh"]