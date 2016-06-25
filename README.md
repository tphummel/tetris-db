Tetris-DB

web scripts for collecting and viewing Tetris data from The New Tetris on Nintendo 64

[![build status](https://secure.travis-ci.org/tphummel/tetris-db.png)](http://travis-ci.org/tphummel/tetris-db)

## test

    ./bin/test

## Screenshots

![Index](https://i.imgur.com/25KInQR.png)
![Match Entry Console](https://i.imgur.com/7UcwfxT.png)
![Summary Report](https://i.imgur.com/1HoJnlz.png)
![Individual Report Options](http://i.imgur.com/u4GFseg.png)
![Individual Report](http://i.imgur.com/zaIx17V.png)
![Calendar Collection Report](http://i.imgur.com/wbKtR6o.png)
![Lines Collection Report](http://i.imgur.com/vAGWbyQ.png)
![Match Strength report](http://i.imgur.com/3ZYUojk.png)
![Performance Rarity Report](http://i.imgur.com/pQsmI4V.png)
![Win Expectancy Report](http://i.imgur.com/mp4ip0M.png)



### Routes/Views
- index.php
- match.php
- rptSummary.php

rptCollab.php
rptPerfDist.php
rptPerformance.php

matchinfo.php
playerinfo.php

### helpers
lib/statPower.php
comprank.php
lib/points.inc.php
lib/grade.php
lib/rankings.inc.php

### templates
header.php
footer.php

### old/archived/dev
rptCareer.php
rptCollabOld.php
editmatch.php
manageplayer.php
include/location, match, player, playermatch


---


## provision

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
