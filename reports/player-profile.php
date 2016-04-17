<?php
$title = "Report : Player Profile";

include_once( dirname ( __FILE__ ) . "/../templates/header.php");

if ( array_key_exists ( "player", $_GET ) ) {

  $playerId = $_GET [ "player" ] ;

  if ( array_key_exists ( "report" , $_GET ) ) {
    $report = $_GET [ "report" ] ;
  } else {
    $report = "lines-collect" ;
  }

  if ( $report === "lines-collect" ) {
    printCollectionReport ( $playerId, "lines" ) ;
  } else if ( $report === "time-collect" ) {
    printCollectionReport ( $playerId, "time" ) ;
  } else if ( $report === "calendar-collect" ) {
    printCalendarReport ( $playerId ) ;
  }
} else {
  printForm ( ) ;
}

function printForm ( ) {
  ?>
  <h1>Player Profile Reports</h1>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
    <select name="player">
      <?php
      require ( dirname ( __FILE__ ) . "/shared/player.php");
      $players = Player::getPlayers ( ) ;

      foreach ( $players as $player ) {
        $id = $player [ "playerid" ] ;
        $name = $player [ "username"] ;
        echo '<option value="'.$id.'">'.$name.'</option>' ;
      }

      ?>
    </select>

    <select name="report">
      <option value="lines-collect">Lines Collection</option>
      <option value="time-collect">Time Collection</option>
      <option value="calendar-collect">Calendar Collection</option>
    </select>
    <input type="submit" value="submit">
    </form>
    <?php
}

function organizeData ( $data, $mode ) {

  $final = array ( ) ;

  while ( $row = mysql_fetch_array ( $data, MYSQL_ASSOC ) ) {
    $final [ $row [ $mode ] ] = array ( ) ;
    $li =& $final[ $row [ $mode ] ] ;

    for ( $i = 2; $i <= 4; $i++ ) {
      if ( isset ( $row [ "ct$i" ] ) ) {
        $li [ "$i" ] = array (
          "count" => $row [ "ct$i" ],
          "last" => $row [ "last$i" ]
        ) ;
      }
    }
  }

  return $final ;
}

function fillBlanks ( $data ) {
  $empty = array ( "count" => 0, "last" => null ) ;

  for ( $i = 0; $i <= 299; $i ++ ) {
    if ( empty ( $data [ "$i" ] ) ) {
      $data [ $i ] = array (
        "2" => $empty,
        "3" => $empty,
        "4" => $empty
      ) ;
    }
  }

  return $data ;
}

function getCompletionPct ( $data ) {
  $empty = array ( "count" => 0, "possible" => 0 ) ;

  $result = array (
    "total" => $empty,
    "2" => $empty,
    "3" => $empty,
    "4" => $empty
  ) ;

  foreach ( $data as $lines => $row ) {
    foreach ( $row as $mode => $detail ) {
      $result [ $mode ] [ "possible" ] ++ ;
      $result [ "total" ] [ "possible" ] ++ ;

      if ( $detail [ "count" ] > 0 ) {
        $result [ $mode ] [ "count" ] ++ ;
        $result [ "total" ] [ "count" ] ++ ;
      }
    }
  }

  return $result ;
}

function getCalendarCompletionPct ( $calendar ) {
  $result = array ( ) ;
  foreach ( array ("total", "2", "3", "4" ) as $y ) {
    $result [ $y ] = array ( "count" => 0, "possible" => 0 ) ;
  }

  foreach ($calendar as $month => $days) {
    foreach ($days as $day => $counts ) {
      foreach ($counts as $matchtype => $count) {

        $result [ $matchtype ] [ "possible" ] ++ ;
        $result [ "total" ] [ "possible" ] ++ ;

        if ( $count > 0 ) {
          $result [ $matchtype ] [ "count" ] ++ ;
          $result [ "total" ] [ "count" ] ++ ;
        }
      }
    }
  }

  return $result ;
}

function printHeader ( $player, $mode ) {
  echo '
  <style type="text/css">
    .table-container table { border-left: 1px solid black ;}
    .table-container {
      margin: 0 auto;
      float: left;
      font-family: monospace;
      padding: 10px;
    }
    .owned { background: green; }
    .unowned { background: red ; }
  </style>' ;

  echo "<h3>Player Collection Report - $mode</h3>" ;

  require ( dirname ( __FILE__ ) . "/shared/player.php");
  $playerData = Player::getPlayer ( $player ) ;

  echo "<h4>Player: "
    . $playerData["firstname"]
    . " " . $playerData["lastname"]
    . "</h4>";
}

function getEmptyCalendar ( ) {
  $dates = array ( ) ;

  // 2016 is a leap year, so we'll get feb 29
  $start = "2016-01-01" ;
  $end = "2016-12-31" ;
  $current = strtotime ( $start ) ;
  $end = strtotime ( $end ) ;

  while ( $current <= $end ) {
    $mon = date ( "n", $current ) ;
    $day = date ( "j", $current ) ;

    if ( array_key_exists ( $mon , $dates ) === false ) {
      $dates [ $mon ] = array ( ) ;
    }

    if ( array_key_exists ( $day , $dates [ $mon ] ) === false ) {
      $dates [ $mon ] [ $day ] = array (
        "2" => 0,
        "3" => 0,
        "4" => 0
      ) ;
    }

    $current = strtotime ( "+1 day", $current ) ;
  }

  return $dates ;
}

function printCompletion ( $rawCompletions ) {
  echo "<h4>" ;

  $strs = array ( ) ;

  foreach ( array ( "total", "2", "3", "4") as $matchType ) {
    $ct = $rawCompletions [ $matchType ] [ "count" ] ;
    $poss = $rawCompletions [ $matchType ] [ "possible" ] ;
    $pct = round ( ( $ct / $poss ) * 100, 1 ) ;

    $strs[] = "$matchType: $ct / $poss ( $pct %)" ;
  }

  echo implode ( "; " , $strs ) ;

  echo "</h4>" ;
}

function printCalendarReport ( $player ) {
  require ( dirname ( __FILE__ ) . "/../config/db.php");

  $connection = mysql_connect($db_host, $db_username, $db_password);
  if(!$connection){
    die ("Could not connect to the database: <br />". mysql_error());
  }
  $db_select = mysql_select_db($db_database, $connection);
  if (!$db_select){
    die ("Could not select the database: <br />". mysql_error());
  }

  $sql = "
    SELECT MONTH(matchdate)          AS mon,
           DAYOFMONTH(matchdate)     AS day,
           SUM(IF(a.type = 2, 1, 0)) AS ct2,
           SUM(IF(a.type = 3, 1, 0)) AS ct3,
           SUM(IF(a.type = 4, 1, 0)) AS ct4
    FROM   (SELECT t.matchdate,
                   (SELECT COUNT(playerid)
                    FROM   playermatch
                    WHERE  matchid = p.matchid) AS type,
                   p.*
            FROM   tntmatch t,
                   playermatch p
            WHERE  p.matchid = t.matchid
                   AND p.playerid = $player) a
    GROUP  BY mon, day" ;

  $result = mysql_query($sql, $connection) or die(mysql_error());

  $calendar = getEmptyCalendar ( ) ;

  while ( $row = mysql_fetch_array ( $result, MYSQL_ASSOC ) ) {
    foreach ( array ( "2", "3", "4") as $i ) {
      $calendar [ $row [ "mon"] ][ $row ["day"] ] [ "$i" ]  = $row [ "ct$i" ] ;
    }
  }

  printHeader ( $player, "calendar") ;

  $completion = getCalendarCompletionPct ( $calendar ) ;
  printCompletion ( $completion ) ;

  foreach ($calendar as $month => $days) {
    echo "
      <div class=table-container>
        <table>
          <thead>
            <caption>$month</caption>
          </thead>
          <tbody>
            <tr><th>Day</th><th>2P</th><th>3P</th><th>4P</th></tr>" ;

    foreach ($days as $day => $counts ) {
      echo "
        <tr>
          <td>$day</td>" ;

      foreach ($counts as $matchtype => $count) {
        if ( $count > 0 ) {
          $tdclass = "class=owned" ;
        } else {
          $tdclass = "class=unowned";
        }

        echo "<td $tdclass>$count</td>" ;
      }
      echo "</tr>" ;
    }
    echo "</tbody></table></div>" ;
  }
}

function printCollectionReport ( $player, $mode="lines" ) {
  require ( dirname ( __FILE__ ) . "/../config/db.php");

  //create connection obj
  $connection = mysql_connect($db_host, $db_username, $db_password);
  if(!$connection){
    die ("Could not connect to the database: <br />". mysql_error());
  }
  $db_select = mysql_select_db($db_database, $connection);
  if (!$db_select){
    die ("Could not select the database: <br />". mysql_error());
  }

  $sql = "
SELECT a.$mode,
       SUM(IF(a.type = 2, a.ct, 0))   AS ct2,
       MAX(IF(a.type = 2, a.last, 0)) AS last2,
       SUM(IF(a.type = 3, a.ct, 0))   AS ct3,
       MAX(IF(a.type = 3, a.last, 0)) AS last3,
       SUM(IF(a.type = 4, a.ct, 0))   AS ct4,
       MAX(IF(a.type = 4, a.last, 0)) AS last4
FROM   (SELECT p.$mode,
               (SELECT COUNT(playerid)
                FROM   playermatch
                WHERE  matchid = t.matchid) AS type,
               COUNT(p.matchid)             AS ct,
               MAX(t.matchdate)             AS last
        FROM   playermatch p,
               tntmatch t
        WHERE  t.matchid = p.matchid
               AND p.playerid = $player
               AND p.$mode < 300
        GROUP  BY p.$mode,
                  (SELECT COUNT(playerid)
                   FROM   playermatch
                   WHERE  matchid = t.matchid)) a
GROUP  BY a.$mode
    " ;

  $result = mysql_query($sql, $connection) or die(mysql_error());

  $organized = organizeData ( $result, $mode ) ;
  $filled = fillBlanks ( $organized ) ;

  $comp = getCompletionPct ( $filled ) ;

  ksort ( $filled ) ;

  $rowsPerTable = 30 ;
  $tables = array ( ) ;

  printHeader ( $player, $mode ) ;

  printCompletion ( $comp ) ;

  $tableHeader = "<div class=table-container><table><tbody><tr><th>$mode</th><th>2P</th><th>3P</th><th>4P</th>" ;
  $tableFoot = "</tbody></table></div>" ;

  foreach ( $filled as $lines => $row ) {
    // $tableNo = 0 ;
    $tableNo = floor ( $lines / $rowsPerTable );

    if ( empty ( $tables [ $tableNo ] ) ) {
      $tables [ $tableNo ] = $tableHeader ;
    }

    $rowStr = "<tr>" . "<td>" . $lines . "</td>" ;
    foreach ( $row as $mt => $detail ) {
      $ct = $detail [ "count" ] ;

      if ( $ct > 0 ) {
        $class = "class=owned" ;
      } else {
        $class = "class=unowned";
      }

      $displayCt = ($ct > 9 ? "*" : $ct ) ;
      $rowStr .= "<td ".$class.">" . $displayCt . "</td>" ;
    }

    $rowStr .= "</tr>" ;
    $tables [ $tableNo ] .= $rowStr ;

  }

  foreach ( $tables as $table ) {
    $table .= $tableFoot ;
    echo $table ;
  }
}

include_once( dirname ( __FILE__ ) . "/../templates/footer.php");

?>
