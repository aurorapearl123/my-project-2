HOW TO INSTALL
=============================
1. install wampserver
2. create database iwash
3. import iwash.sql - with sample data
    import iwash.sql 	- clear database


https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-16-04
apt-get update
apt-get install apache2
apache2ctl configtest
systemctl status apache2
ufw app info "Apache Full"
ufw allow in "Apache Full"

apt-get install mysql-server
mysql_secure_installation

apt-get install php libapache2-mod-php php-mcrypt php-mysql
date.timezone = Asia/Manila