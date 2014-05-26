Tetris-DB

web scripts for collecting and viewing Tetris data from The New Tetris on Nintendo 64

[![build status](https://secure.travis-ci.org/tphummel/tetris-db.png)](http://travis-ci.org/tphummel/tetris-db)

## test

    ./bin/test

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
