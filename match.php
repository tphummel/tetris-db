<?php

require_once ( __DIR__ . "/config/db.php" ) ;
require_once ( __DIR__ . "/lib/grade.php" ) ;
require_once ( __DIR__ . "/lib/points.inc.php" ) ;
require_once ( __DIR__ . "/lib/rankings.php" ) ;
require_once ( __DIR__ . "/lib/rules.php" ) ;
require_once ( __DIR__ . "/lib/statPower.php" ) ;
require_once ( __DIR__ . "/lib/redis.php" ) ;

$connection = mysql_connect ( $db_host, $db_username, $db_password ) ;
if ( !$connection ) {
  die ( "Could not connect to the database: <br />" . mysql_error ( ) ) ;
}

$db_select = mysql_select_db ( $db_database, $connection );
if ( !$db_select ) {
  die ( "Could not select the database: <br />". mysql_error ( ) );
}

require_once ( __DIR__  . "/lib/helper.php" ) ;

if ( array_key_exists ( 'player1', $_POST ) ) {
  unset ( $players ) ;
  $players = Helper::cleanPlayers ( $_POST ) ;

  unset ( $orderedPlayerNames ) ;
  $orderedPlayerNames = array ( ) ;
  foreach ( $players as $player ) {
    $orderedPlayerNames[] = $player [ 0 ] ;
  }

  $location = $_POST [ "location" ] ;
  $note = $_POST [ "note" ] ;

}
$prevSavedMatch = null ;

if ( array_key_exists('action', $_GET ) && $_GET['action'] === 'add' ) {
	$matchToSave = true;
} else {
	$matchToSave = false ;
}

if ( $matchToSave ) {

  $valid = Rules::validateMatch ( $players ) ;

  //reshow form with highlights if error is caught
  if( $valid [ "isValid" ] == false ) {
    $errorRegion = true ;
    showConsole ( $players, $connection, null, $valid [ "errMsg" ], $errorRegion, $location, $note ) ;
    exit();
  }

  Helper::logMatch ( $players, $location ) ;

  $wrankedPlayers = Rankings::setWinRanks ( $players ) ;
  $erankedPlayers = Rankings::setEffRanks ( $wrankedPlayers ) ;

  //Create TNTMatch Record

  $nowdate = date ( "Y-m-d" ) ;
  $nowstamp = date ( "Y-m-d H:i:s" ) ;

  $insertTM = "
    INSERT INTO tntmatch VALUES
    (NULL, '" . $nowdate . "', '" . $nowstamp . "', 4,
      (SELECT locationid from location where locationname = '" . $location . "'),
    '" . $note . "', 1)" ;

  mysql_query ( $insertTM, $connection ) or die ( mysql_error() ) ;

  //Create PlayerMatch Records
  $matchId = mysql_insert_id ( ) ;

  $insertPM = "INSERT INTO playermatch VALUES ";
  foreach ( $erankedPlayers as $player ) {
    $insertPM = $insertPM . "(" . $matchId . ", (SELECT playerid from player where username = '" . $player[0] . "')," .
                      $player[1] . ", " . $player[4] . ", " . $player[5] . ", " . $player[7] . "), ";

  }
  $insertPM_trimmed = rtrim($insertPM, ", ");

  mysql_query($insertPM_trimmed, $connection) or die(mysql_error());

  $erankedInDisplayOrder = array ( ) ;
  foreach ($orderedPlayerNames as $orderedName) {
    foreach ($erankedPlayers as $erankedPlayer) {
      if ($erankedPlayer [ 0 ] === $orderedName) {
        $erankedInDisplayOrder[] = $erankedPlayer ;
      }
    }
  }
  $prevSavedMatch = [
    "id" => $matchId,
    "ts" => $nowstamp,
    "players" => $erankedInDisplayOrder
  ] ;

  if ( !array_key_exists("session-match-id-inclusive", $_COOKIE) ) {
    setcookie(
      "session-match-id-inclusive",
      $matchId,
      null, // session expiration
      "/match.php"
    ) ;
  }

  foreach ( $erankedPlayers as $perf ) {
    $perf [ 8 ] = $matchId ;
     // Redis::publishPerformance ( $perf ) ;
  }
}

//this happens on every load
//clear post data for start of new match
$tempPlayers = array();

// put $players in display order as $users
if ( isset ( $orderedPlayerNames ) ) {
  foreach ( $orderedPlayerNames as $ogp ) {
    foreach ( $players as $p ) {
      if ( $ogp == $p [ 0 ] ) {
        $tempPlayers[] = $p;
      }
    }
  }
} else {
  for ( $i = 0 ; $i < 4 ; $i++ ) {
    $user = array ( "", "", "", "", "", "" ) ;
    $tempPlayers [ $i ] = $user ;
  }
}

$players = $tempPlayers;

for ($q = 1; $q <= 4; $q++) {
  unset ( $_POST [ "player" . $q ] ) ;
}

showConsole( $players, $connection, $prevSavedMatch, "", "", "", "" ) ;

/*
==========================================================================================
==========================================================================================
*/
function showConsole ( $users, $connection, $prevSavedMatch, $errorMsg, $errorRegion, $location, $note) {
  $title = "The New Tetris - Match Console" ;
  require_once ( __DIR__ . "/templates/header.php" ) ;

  $errCssClass =  ' class="errorLocation"' ;

  if ( !empty ( $errorMsg ) AND !empty ( $errorRegion ) ) {
    ?>
    <div class="errortext">
      <?php
        echo $errorMsg ;
      ?>
    </div>
<?php
  }
?>
    <form action="match.php?action=add" method="post" name="match">
    <table border="1" cellpadding="5" cellspacing="10">

    <!--THIS MATCH - CREATES FOUR PLAYER BOXES-->
    <tr>
      <td valign="top">
        <table border="0">
          <tr>
            <th colspan="2">This Match</th>
          </tr>
          <tr>
            <td colspan="1">Location:</td>
            <td>
              <select name="location">
              <?php
              $queryLoc = "
                SELECT locationname
                FROM   location
              ";
              $resultLoc = mysql_query($queryLoc, $connection) or die(mysql_error());

              if (!empty($location)) {
                $selectedLocName = $location ;
              } else {
                $queryLastLocation = "
                  SELECT l.locationname
                  FROM   location l,
                       tntmatch tm
                  WHERE  tm.location = l.locationid
                       AND tm.matchid = (SELECT MAX(matchid)
                                         FROM   tntmatch)
                ";
                $lastLocResult = mysql_query($queryLastLocation, $connection) or die(mysql_error());
                $lastLoc = mysql_fetch_array($lastLocResult);
                $selectedLocName = $lastLoc[ "locationname" ];
              }


              while ($loc = mysql_fetch_array($resultLoc)) {
                $locName = $loc [ "locationname" ] ;
                ?>
                <option value="<?=$locName?>" <?=($selectedLocName === $locName ? "selected" : "")?>><?= $locName ?></option>
                <?php
              }
              ?>
              </select></td></tr>
          <tr>
            <td>Note:</td>

            <td colspan="4"><textarea rows="2" cols="10" name="note" value="<?= $note ?>"></textarea></td>
          </tr>
        </table>
      </td>
    <?php
    $query = "
      SELECT username
      FROM   player
    ";
    $result = mysql_query($query, $connection) or die(mysql_error());
    //query DB once for name list and then put in array
    while ($row = mysql_fetch_array($result)) {
      $names[] = $row['username'];
    }

    for ($i = 0; $i <= 3; $i++) { //do 4 times, one for each player
      $playerIsActive = array_key_exists($i, $users) ;
      $playerHasError = false;

      if($errorRegion == 1 + (4*$i)
        or $errorRegion == 17
        or $errorRegion == 21 + $i
      ) {
        $playerHasError = true;
      }
    ?>
      <td><table>
      <tr><td>Username:</td>
      <td>
        <select name="player<?= $i+1 ?>[]" <?= ($playerHasError ? $errCssClass : "") ?>>
          <option value="VACANT">VACANT</option>
          <?php
          foreach ($names as $name) {
            $userIsSelected = false;
            if ($playerIsActive && $users[$i][0] == $name) {
              $userIsSelected = true;
            }
            ?>
            <option
              value="<?= $name ?>"
              <?= ($userIsSelected ? " selected" : "")?>
            ><?= $name ?></option>
            <?php
          }
          ?>
        </select>
      </td></tr>
      <?php
        $existingPlayerLines = $playerIsActive && $playerHasError ? $users[$i][1] : "" ;

        $linesHasError = false;
        if($errorRegion == 2 + (4*$i)
          or $errorRegion == 18
          or $errorRegion == 21 + $i
        ) {
          $LinesHasError = true;
        }
      ?>
      <tr>
        <td>Lines:</td>
        <td>
          <input
            type="text"
            size="4"
            maxlength="4"
            name="player<?= $i+1 ?>[]"
            value="<?= $existingPlayerLines ?>"
            <?= ($playerHasError ? $errCssClass : "") ?>
          \>
        </td>
      </tr>
      <tr>
        <td>Minutes:</td>
        <?php
        $existingPlayerMinutes = $playerIsActive && $playerHasError ? $users[$i][2] : "";

        $minHasError = false;
        if($errorRegion == 3 + (4*$i)
          or $errorRegion == 19
          or $errorRegion == 21 + $i
          or ($errorRegion == 25 and $i < 2)
        ) {
          $minHasError = true;
        }
        ?>
        <td>
          <input
            type="text"
            size="4"
            maxlength="4"
            name="player<?= $i+1 ?>[]"
            <?= ($minHasError ? $errCssClass : "") ?>
            value="<?= $existingPlayerMinutes ?>"
          />
        </td>
      </tr>
      <tr>
        <td>Seconds:</td>
        <?php
          $existingPlayerSeconds = $playerIsActive && $playerHasError ? $users[$i][3] : "" ;

          $secHasError = false;
          if($errorRegion == 3 + (4*$i)
            or $errorRegion == 19
            or $errorRegion == 21 + $i
            or ($errorRegion == 25 and $i < 2)
          ) {
            $secHasError = true;
          }
        ?>
        <td>
          <input
            type="text"
            size="4"
            maxlength="4"
            name="player<?= $i+1 ?>[]"
            <?= ($secHasError ? $errCssClass : "") ?>
            value="<?= $existingPlayerSeconds ?>"
          \>
        </td>
      </tr>
      <tr>
        <td>Winner:</td>
        <?php
          $winHasError = false ;
          if ($errorRegion == 4 + (4*$i)
            or $errorRegion == 20
            or $errorRegion == 21 + $i
          ) {
            $winHasError = true ;
          }

          if ($playerIsActive && $playerHasError && $users[$i][5] == "on") {
            $existingPlayerWinner = " checked" ;
          } else {
            $existingPlayerWinner = "" ;
          }
        ?>
        <td <?= ($winHasError ? $errCssClass : "")?>>
          <input
            type="checkbox"
            name="player<?= $i+1 ?>[]"
            <?= $existingPlayerWinner ?>
          />
        </td>
      </tr>
    </table>
  </td>
    <?php
    } //end "this match" for loop

/*
==========================================================================================
==========================================================================================
*/
    ?>
    </tr>
    <!--LAST MATCH -- RUNS 4 TIMES-->
    <tr>
      <td align="center">
        <table border="0">
          <tr>
            <th>Last Match</th>
          </tr>
          <?php
            if ($prevSavedMatch !== null) {
          ?>
          <tr>
            <td align="center">
              Match #<?= $prevSavedMatch["id"] ?><br />
              <?= $prevSavedMatch["ts"] ?>
            </td>
          </tr>
          <tr>
            <th>
              <form>
                <input type="submit" value="Edit" disabled>
              </form>
            </th>
          </tr>
          <?php } ?>
        </table>
      </td>

    <?php
    $playerCount = count ( $prevSavedMatch['players'] ) ;
    $prevMatchSums = array (
      "time" => 0,
      "lines" => 0
    ) ;

    for ($j = 0; $j <= 3; $j++) { //do 4 times, one for each player
    ?>
    <td valign="middle">
    <?php
    if ($prevSavedMatch ['players'] &&
      array_key_exists($j, $prevSavedMatch ['players'] )) {

      $prevMatchPlayer = $prevSavedMatch ['players'] [$j] ;
      $wrank = $prevMatchPlayer[5];
      $wpts = rankToPts($wrank, $playerCount);

      $erank = $prevMatchPlayer[7];
      $epts = rankToPts($erank, $playerCount);

      $lines = $prevMatchPlayer[1];
      $prevMatchSums [ "lines" ] += $lines ;

      $time = $prevMatchPlayer[4];
      $prevMatchSums [ "time" ] += $time ;

      $min = intval($time/60);
      $sec = str_pad($time - $min*60,2,"0", STR_PAD_LEFT);
      if ($time != 0) {
        $eff = $lines/$time;
      } else {
        $eff = 0;
      }

      $timeStr = $min . ":" . $sec;
      $effStr = number_format($eff,3);
      $grade = gradePerf($time, $lines);

      //lib/statPower.php
      $power = computePower($wpts, $epts, $eff);
      echo '<table align="center"';
      echo '>';
      echo '
      <tr><td>W-Rank:</td><td>' . $wrank . '</td></tr>
      <tr><td>E-Rank:</td><td>' . $erank . '</td></tr>
      <tr><td>Lines:</td><td>' . $lines . '</td></tr>
      <tr><td>Time:</td><td>' . $timeStr . '</td></tr>
      <tr><td>LPS:</td><td>' . $effStr . '</td></tr>
      <tr><td>POWER:</td><td>' . $power . '</td></tr>
      <tr><td>Grade: </td><td><h2>' . $grade . '</h2></td></tr>
      </table>
      </td>';
    } //end if

  } //end "last match" for loop

    if ($prevSavedMatch) {
    ?>
    <td>
      <?php
      $time = $prevMatchSums [ "time" ] ;
      $lines = $prevMatchSums [ "lines" ] ;

      $min = intval($time/60);
      $sec = str_pad($time - $min*60,2,"0", STR_PAD_LEFT);
      if ($time != 0) {
        $eff = $lines/$time;
      } else {
        $eff = 0;
      }

      $timeStr = $min . ":" . $sec;
      $effStr = number_format($eff,3);
      $grade = gradePerf($time, $lines);
      ?>
      <table align="center">
        <caption><h3>Combined</h3></caption>
        <tr><td>Lines:</td><td><?= $lines ?></td></tr>
        <tr><td>Time:</td><td><?= $timeStr ?></td></tr>
        <tr><td>LPS:</td><td><?= $effStr ?></td></tr>
        <tr><td>Grade:</td><td><h2><?= $grade ?></h2></td></tr>
      </table>
    </td>
    <?php
    }
    ?>
    </tr>
    <?php
    $sessionCutoff = $_COOKIE["session-match-id-inclusive"] ?: $prevSavedMatch ["id"] ;
    ?>
    <tr>
    <td align="center">
      <table border="0">
        <tr><th>Current Session</th></tr>
        <?php if (isset($sessionCutoff)) { ?>
        <tr><td>Since #<?= $sessionCutoff ?></td></tr>
        <?php } ?>
      </table>
    </td>

    <?php
    for ($k = 0; $k <= 3; $k++) { //do 4 times, one for each player
      $playerIsActive = array_key_exists($k, $users) ;
      $playerName = $playerIsActive ? $users[$k][0] : null ;
      echo '<td valign="middle">';

    if ($playerIsActive && $playerName && $playerName !== "VACANT") {
      $query = "
        SELECT count(a.matchid) as games,
          sum(a.type) as exp,
          sum(a.time) as `time`,
          sum(a.lines) as `lines`,
          sum(a.wrank) as wrank,
          sum(if(a.wrank = 1, 1, 0)) as w1,
          sum(if(a.wrank = 2, 1, 0)) as w2,
          sum(if(a.wrank = 3, 1, 0)) as w3,
          sum(if(a.wrank = 4, 1, 0)) as w4,
          sum(a.type)+count(a.matchid)-sum(a.wrank) as wpts,
          sum(a.erank) as erank,
          sum(if(a.erank = 1, 1, 0)) as e1,
          sum(if(a.erank = 2, 1, 0)) as e2,
          sum(if(a.erank = 3, 1, 0)) as e3,
          sum(if(a.erank = 4, 1, 0)) as e4,
          sum(a.type)+count(a.matchid)-sum(a.erank) as epts
        FROM (SELECT m.matchid, p.username, pm.lines, pm.time,
        (select count(playerid) from playermatch where matchid = m.matchid) as type,
        pm.wrank, pm.erank
          FROM playermatch pm, tntmatch m, player p
          WHERE m.matchid = pm.matchid
          and pm.playerid = p.playerid
          and m.matchid >= " . $sessionCutoff . "
          and p.username = '" . $playerName . "'
        ) a" ;

      $result = mysql_query($query, $connection) or die(mysql_error());
      $data = mysql_fetch_array($result);

      $games = $data["games"];
      $time = $data["time"];
      $lines = $data["lines"];
      $winPoints = $data["wpts"];
      $effPoints = $data["epts"];

      $minutes = intval($time/60);
      $seconds = str_pad($time - $minutes*60,2,"0", STR_PAD_LEFT);

      $timeDisp = $minutes . ":" . $seconds;

      $lpsRaw = ($time > 0 ? $lines / $time : 0);
      $lps = number_format($lpsRaw,3) . " (" .$lines." / ". $time . ")";

      $lpgRaw = ($time > 0 ? $lines / $games : 0);
      $lpg = number_format($lpgRaw,2) . " (" .$lines." / ".$games.")";

      $eptsg = number_format(($games > 0 ? $effPoints/$games : 0),3);
      // $eptsg .= " (" .$data["e1"].")"
      $wptsg = number_format(($games > 0 ? $winPoints/$games : 0),3);

      //lib/statPower.php
      $power = computePower($wptsg, $eptsg, $lps);

      ?>
      <table align="center">
      <tr><td>G (Time)</td><td><?php echo $games . " (" . $timeDisp . ")"; ?></td></tr>
      <tr><td>Li / Sec</td><td><?php echo $lps; ?></td></tr>
      <tr><td>Li / Game</td><td><?php echo $lpg; ?></td></tr>
      <tr><td>W-PTS/G</td><td><?php echo $wptsg; ?></td></tr>
      <tr><td>E-PTS/G</td><td><?php echo $eptsg; ?></td></tr>
      <tr><td>POWER</td><td><?php echo $power; ?></td></tr>
      </table>
      <?php
    } //end if
  } //end "day sum" for loop

    ?>
    </td>

    </tr>

    <tr><td align="center" colspan="5"><input type="submit" value="Submit"></td></tr>
    </table>
    </form>


  <?php
  include_once( __DIR__ . "/templates/footer.php");
  ?>
  </body>
  </html>
<?php
}//close showConsole function
?>
