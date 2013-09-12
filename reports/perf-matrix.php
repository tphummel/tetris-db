<?php
$title = "Report : Performance Matrix";

include_once( dirname ( __FILE__ ) . "/../templates/header.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ( array_key_exists ( "type", $_GET ) ) {
  
  $type = $_GET [ "type" ] ;
  doMatrix ( $type ) ;  

} else {
  doForm ( ) ;
}

function doForm ( ) {
  ?>
  <h1>Performance Matrix Report</h1>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
      <select name="type">
        <option value="2">2P</option>
        <option value="3">3P</option>
        <option value="4">4P</option>
        <option value="ALL">ALL</option>
      </select>
      <input type="submit" value="submit">
    </form>
    <?php
}

function organizeData ( $data, $ceiling ) {

  $final = array ( ) ;

  for ( $i = $ceiling ; $i >= 0 ; $i -- ) {
    $final [ "$i" ] = array ( ) ;
    for ( $j = 0 ; $j <= $ceiling ; $j ++ ) {
      $final [ "$i" ] [ "$j" ] = 0 ;
    }
  }

  foreach ( $data as $val ) {
    $final [ $val [ "lines" ] ] [ $val [ "time" ] ] = $val [ "ct" ] ;
  }

  return $final ;
}

function doMatrix ( $type ) {
  $ceiling = 200 ;

  require ( dirname ( __FILE__ ) . "/shared/db.php");
  
  $typeStr = ( $type == "ALL" ? "(2,3,4)" : "($type)" ) ;

  $sql = "
    SELECT p.lines, 
           p.time, 
           COUNT(p.matchid) AS ct 
    FROM   playermatch p 
    WHERE  p.lines <= $ceiling 
           AND p.time <= $ceiling " ;

  
  $sql .= "AND (SELECT COUNT(matchid) FROM playermatch WHERE matchid = p.matchid) IN $typeStr" ;

  $sql .= "
    GROUP  BY p.lines, 
              p.time 
    ORDER  BY p.lines DESC, 
              p.time ASC
  " ;


  echo '
  <style type="text/css">
    .table-container { font-family: monospace; }
    .owned { background: green; }
    .unowned { background: red ; }
  </style>' ;

  $result = DB::sqlToArray ( $sql ) ;

  $organized = organizeData ( $result, $ceiling ) ;

  echo "<h3>Performance Matrix Report - $type</h3>" ;


  ?>


  <?php


  $tableHeader = "<div class=table-container><table><tbody>" ;


  $tableFoot = "</tbody></table></div>" ;

  $table = "" . $tableHeader ;

  foreach ( $organized as $lines => $row ) {

    $rowStr = "<tr>" . "<th>" . $lines . "</th>" ;
    foreach ( $row as $time => $ct ) { 

      if ( $ct > 0 ) {
        $class = "class=owned" ;
      } else {
        $class = "class=unowned";
      }

      $tooltip = "title=\"$lines lines, $time sec\"" ;

      $displayCt = ($ct > 9 ? "*" : $ct ) ;
      $rowStr .= "<td ".$class." $tooltip >" . $displayCt . "</td>" ;
    }

    $rowStr .= "</tr>" ;
    $table .= $rowStr ;
  }

  $table .= "<tr><th>&nbsp;</th>" ; 
    for ( $i = 0 ; $i <= $ceiling ; $i ++ ) {
      $table .= "<th>$i</th>" ;
    }
  $table .= "</tr>" ; 

  $table .= $tableFoot ;
  echo $table ;
}

include_once( dirname ( __FILE__ ) . "/../templates/footer.php");

?>