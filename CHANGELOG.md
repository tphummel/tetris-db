
0.10.1 / 2016-04-23
==================

  * Merge pull request #9 from tphummel/match-console-updates
  * Merge pull request #8 from tphummel/fix-tabs

0.10.0 / 2016-04-17
==================

  * Merge pull request #7 from tphummel/https-footer-link
  * Merge pull request #6 from tphummel/calendar-collection
  * travis: add php 5.6 to build matrix
  * remove data/ directory

0.9.0 / 2015-05-30
==================

  * disable match results redis publishing
  * GET /health.php: added version to json
  * readme: test section + travis badge
  * travis.yml
  * test harness + tests

0.8.0 / 2014-05-24
==================

 * added performance-rarity report
 * footer: reorg links

0.7.1 / 2013-11-03
==================
  - match pub/sub:
    - added 'matchid' to each perf

0.7.0 / 2013-09-29
==================
  - match pub/sub:
    - after match save, publish performances to redis channel "tetrisdb:performances"

0.6.5 / 2013-09-12
==================
  - reports/player-profile:
    - use shared/player helper for loading list
    - now does "lines" and "time" collection reports

0.6.4 / 2013-09-11
==================
  - created /health.php - json health info

0.6.3 / 2013-09-09
==================
  - bug: added location to match logging

0.6.2 / 2013-09-09
==================
  - added match logging
  - cap task to manage shared log file

0.6.1 / 2013-09-04
==================
  - moved report files into reports/
  - new helpers: shared/db, shared/player
  - reports/player-profile: added player name label

0.6.0 / 2013-09-04
==================
  - new report:
    - /reports/player-profile (with single option: collection)
  - templates: absolute paths in header/footer

0.5.0 / 2013-09-03
==================
  - lib/rules:
    - created static class for validating matches
    - wrote tests
  - lib/rankings:
    - renamed file
    - wrapped functions in static class

0.4.0 / 2013-09-01
==================
  - lib/rankings:
    - wrapped eff 2nd place in if count > 0 to fix warning
  - rolled a unit test harness
  - wrote tests for lib/grade, lib/rankings

0.3.1 / 2013-08-25
==================
  - match console:
    - changed stats to show trailing 24 hrs instead of current day

0.3.0 / 2013-08-25
==================
  - reorganized project:
    - subfolders: dev/ assets/ templates/ lib/
    - assets/ -> css/ img/ js/
  - html5bp: added touch icons, favicon, updated header
  - moved <html><head><body>... to templates/header.php
  - matchinfo: added location detail

0.2.0 / 2013-08-25
==================
  - all prior code
