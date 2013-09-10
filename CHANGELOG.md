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
