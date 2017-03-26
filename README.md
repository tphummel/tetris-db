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

### run locally with docker

```
docker run --rm -it -e -p 9000:80 dokku/tetris-db:0.12.0
```
browse: http://localhost:9000

### app/db initial installation

```
# create app
ssh tom@dokku.dev "dokku apps:create tetris-db"

# create db
ssh tom@dokku.dev "sudo dokku plugin:install https://github.com/dokku/dokku-mariadb.git mariadb"
ssh tom@dokku.dev "dokku mariadb:create tetris-db"

# load data snapshot into db
aws --profile personal s3 cp s3://tph-etc/tetris-data/tnt.sql .
scp tnt-data.sql tom@dokku.dev:~/
ssh tom@dokku.dev "dokku mariadb:import tetris-db < tnt-data.sql"

# link db to app
dokku mariadb:link tetris-db tetris-db
```

### build/release

```
docker build . -t dokku/tetris-db:0.12.0
docker save dokku/tetris-db:0.12.0 | bzip2 | ssh tom@dokku.dev "bunzip2 | sudo docker load"
ssh tom@dokku.dev "dokku tags:deploy tetris-db 0.12.0"
```

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
