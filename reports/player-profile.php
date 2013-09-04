<?php
$title = "Report : Player Profile";

include_once( dirname ( __FILE__ ) . "/../templates/header.php");

if ( array_key_exists ( "player", $_GET ) ) {
  
  $playerId = $_GET [ "player" ] ;
  
  if ( array_key_exists ( "report" , $_GET ) ) {
    $report = $_GET [ "report" ] ;  
  } else {
    $report = "collection" ;
  }

  if ( $report === "collection" ) {
    doCollect ( $playerId ) ;  
  }
} else {
  doForm ( ) ;
}

function getPlayers ( ) {
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

  $sql = "SELECT playerid, username FROM player" ;
  $result = mysql_query($sql, $connection) or die(mysql_error());

  $players = array ( ) ;
  while ( $row = mysql_fetch_array ( $result, MYSQL_ASSOC ) ) {
    $players[] = $row ;
  }

  return $players ;
}

function doForm ( ) {
  ?>
  <h1>Player Profile Reports</h1>
    <form action="player-profile.php" method="GET">
    <select name="player">
      <?php
      $players = getPlayers ( ) ;

      foreach ( $players as $player ) {
        $id = $player [ "playerid" ] ;
        $name = $player [ "username"] ;
        echo '<option value="'.$id.'">'.$name.'</option>' ;
      } 

      ?>
    </select>

    <select name="report">
      <option value="collection">Performance Collection</option>
    </select>
    <br />
    <input type="submit" value="submit">
    </form>
    <?php
}

function organizeData ( $data ) {

  $final = array ( ) ;

  while ( $row = mysql_fetch_array ( $data, MYSQL_ASSOC ) ) {
    $final [ $row [ "lines" ] ] = array ( ) ;
    $li =& $final[ $row [ "lines" ] ] ;

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

  for ( $i = 1; $i <= 299; $i ++ ) {
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

function doCollect ( $player ) {
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
SELECT a.lines, 
       SUM(IF(a.type = 2, a.ct, 0))   AS ct2, 
       MAX(IF(a.type = 2, a.last, 0)) AS last2, 
       SUM(IF(a.type = 3, a.ct, 0))   AS ct3, 
       MAX(IF(a.type = 3, a.last, 0)) AS last3, 
       SUM(IF(a.type = 4, a.ct, 0))   AS ct4, 
       MAX(IF(a.type = 4, a.last, 0)) AS last4 
FROM   (SELECT p.lines, 
               (SELECT COUNT(playerid) 
                FROM   playermatch 
                WHERE  matchid = t.matchid) AS type, 
               COUNT(p.matchid)             AS ct, 
               MAX(t.matchdate)             AS last 
        FROM   playermatch p, 
               tntmatch t 
        WHERE  t.matchid = p.matchid 
               AND p.playerid = $player
        GROUP  BY p.lines, 
                  (SELECT COUNT(playerid) 
                   FROM   playermatch 
                   WHERE  matchid = t.matchid)) a 
GROUP  BY a.lines 
    " ;

  echo '
  <style type="text/css">
    .table-container table { border-left: 1px solid black ;}
    .table-container { margin: 0 auto; float: left; font-family: monospace; }
    .owned { background: green; }
    .unowned { background: red ; }
  </style>' ;
  $result = mysql_query($sql, $connection) or die(mysql_error());

  $organized = organizeData ( $result ) ;
  $filled = fillBlanks ( $organized ) ;

  $comp = getCompletionPct ( $filled ) ;

  ksort ( $filled ) ;

  $rowsPerTable = 30 ;
  $tables = array ( ) ;

  echo "<h3>Player Collection Report</h3>" ;
  echo "<h4>Player: $player</h5>" ;

  echo "<h4>" ;

  $strs = array ( ) ;

  foreach ( array ( "total", "2", "3", "4") as $mode ) {
    $ct = $comp [ $mode ] [ "count" ] ;
    $poss = $comp [ $mode ] [ "possible" ] ;
    $pct = round ( ( $ct / $poss ) * 100, 1 ) ;
  
    $strs[] = "$mode: $ct / $poss ( $pct %)" ; 
  }

  echo implode ( "; " , $strs ) ;

  echo "</h4>" ;


  $tableHeader = "<div class=table-container><table><tbody><tr><th>Lines</th><th>2P</th><th>3P</th><th>4P</th>" ;
  $tableFoot = "</tbody></table></div>" ;

  foreach ( $filled as $lines => $row ) {
    // $tableNo = 0 ;
    $tableNo = floor ( $lines / $rowsPerTable );

    if ( empty ( $tables [ $tableNo ] ) ) {
      $tables [ $tableNo ] = $tableHeader ;
    }

    $rowStr = "<tr>" . "<td>" . $lines . "</td>" ;
    foreach ( $row as $mode => $detail ) { 
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