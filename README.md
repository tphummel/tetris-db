Tetris-DB

web scripts for collecting and viewing Tetris data from The New Tetris on Nintendo 64

- 2 player, only require entire time once
future report ideas:
  - head to head breakdown
    - player vs player
      - summary by game type
    - single player
      - vs each other player

"lines won"

- move reports to sub dir
  rpPerf: - add date range option
    - refactor queries to reduce duplication
    - move queries to own file
    - field list always the same?
    - add date range to all if set
- match console
  - zero initial power values
  - more rows for trailing averages
    - should be able to abstract each of these rows and make them take less space
    - last 10 games
    - last 10 vs each opp
  - test functionality in lib/ and match/. biz logic only. game state, game scoring, tie breaking
    - so we might be able to refactor someday. split apart match console into directory w/ files

- eff rank. tie goes to the longer round. if still tied, then tied




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