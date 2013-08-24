Tetris-DB

web scripts for collecting and viewing Tetris data from The New Tetris on Nintendo 64



provision

/usr/bin/mysql_secure_installation

https://www.digitalocean.com/community/articles/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-12-04

git clone tnt
create db, create user, load data

create database tnt;
create user 'tnt'@'localhost' identified by 'tnt';
grant all on tnt.* to 'tnt'@'localhost';

write db_config.php
write nginx config
remove default nginx