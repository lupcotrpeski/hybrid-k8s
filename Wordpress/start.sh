#!/bin/bash

# Insert the variables
sed -i "s/RDS_DNS/${RDS_DNS}/g" /var/www/localhost/htdocs/wordpress/wp-config.php
sed -i "s/RDS_USERNAME/${RDS_USERNAME}/g" /var/www/localhost/htdocs/wordpress/wp-config.php
sed -i "s/RDS_DBNAME/${RDS_DBNAME}/g" /var/www/localhost/htdocs/wordpress/wp-config.php
sed -i "s/RDS_PASSWORD/${RDS_PASSWORD}/g" /var/www/localhost/htdocs/wordpress/wp-config.php

# Start lighthttpd
lighttpd -D -f /etc/lighttpd/lighttpd.conf