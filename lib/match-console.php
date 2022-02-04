<?php
class MatchConsole {
  public static function render ($error, $current, $previous) {
    $errorMsg = $error["message"] ;
    $errorRegion = $error["region"] ;

    $users = $current["players"] ;
    $location = $current["location"] ;
    $note = $current["note"];

    $prevSavedMatch = $previous ;

    $title = "The New Tetris - Match Console" ;

    require_once ( __DIR__ . "/../templates/header.php" ) ;
    require ( __DIR__ . "/../config/db.php" ) ;

    $mysqli = mysqli_init();
    $mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
    $mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

    $errCssClass =  ' class="errorLocation"' ;

    if ( !empty ( $errorMsg ) AND !empty ( $errorRegion ) ) {
    ?>
      <div class="errortext"><?= $errorMsg ?></div>
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
                $resultLoc = $mysqli->query($queryLoc);

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
                  $lastLocResult = $mysqli->query($queryLastLocation);
                  $lastLoc = $lastLocResult->fetch_array();
                  $selectedLocName = $lastLoc[ "locationname" ];
                }


                while ($loc = $resultLoc->fetch_array()) {
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
      $result = $mysqli->query($query);
      //query DB once for name list and then put in array
      while ($row = $result->fetch_array()) {
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

        $result = $mysqli->query($query);
        $mysqli->close();
        $data = $result->fetch_array();

        $exp = $data["exp"];
        $games = $data["games"];
        $time = $data["time"];
        $lines = $data["lines"];

        $winPoints = $data["wpts"];
        $wrank = $data["wrank"];
        $effPoints = $data["epts"];
        $erank = $data["erank"];

        $minutes = intval($time/60);
        $seconds = str_pad($time - $minutes*60,2,"0", STR_PAD_LEFT);

        $timeDisp = $minutes . ":" . $seconds;

        $lpsRaw = ($time > 0 ? $lines / $time : 0);
        $lps = number_format($lpsRaw,3);

        $lpgRaw = ($time > 0 ? $lines / $games : 0);
        $lpg = number_format($lpgRaw,2);

        $eRec = implode("-", array($data["e1"],$data["e2"],$data["e3"],$data["e4"]));
        $ePct = number_format(($exp > 0 ? $effPoints/$exp : 0),3);

        $wRec = implode("-", array($data["w1"],$data["w2"],$data["w3"],$data["w4"]));
        $wPct = number_format(($exp > 0 ? $winPoints/$exp : 0),3);

        //lib/statPower.php
        $power = computePower($winPoints/$games, $effPoints/$games, $lps);

        ?>
        <table align="center">
        <tr><td>G (Time)</td><td><?= $games ?></td><td><?=" (" . $timeDisp . ")" ?></td></tr>
        <tr><td>Li / Sec</td><td><?= $lps; ?></td><td><?=" (" .$lines." / ". $time . ")" ?></td></tr>
        <tr><td>Li / G</td><td><?= $lpg; ?></td><td><?= " (" .$lines." / ".$games.")" ?></td></tr>
        <tr><td>Win Pct</td><td><?= $wPct; ?></td><td><?= " (" . $wRec . ")" ?></td></tr>
        <tr><td>Eff Pct</td><td><?= $ePct; ?></td><td><?= " (" . $eRec . ")" ?></td></tr>
        <tr>
          <td>
            <a href="https://github.com/tphummel/tetris-db/blob/master/lib/statPower.php" target="_blank">Power</a>
          </td>
          <td><?= $power; ?></td>
        </tr>
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
    include_once( __DIR__ . "/../templates/footer.php");
    ?>
    </body>
    </html>
  <?php
  }//close showConsole function
}
?>
