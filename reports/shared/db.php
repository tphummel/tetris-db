<?php

class DB {
  public static function getConn ( ) {
    require dirname ( __FILE__ ) . "/../../config/db.php" ;

    $connection = mysql_connect ( $db_host, $db_username, $db_password ) ;
    if ( !$connection ) {
      die ( "Could not connect to the database: <br />". mysql_error ( ) ) ;
    }
    
    $db_select = mysql_select_db ( $db_database, $connection );
    if ( !$db_select ) {
      die ( "Could not select the database: <br />". mysql_error ( ) ) ;
    }
  
    return $connection ; 
  }

  public static function sqlToArray ( $sql ) {
    $conn = self::getConn ( ) ;

    $result = mysql_query ( $sql, $conn ) or die( mysql_error ( ) ) ;

    $resArr = array ( ) ;
    while ( $data = mysql_fetch_array ( $result ) ) {
      $resArr[] = $data ;
    }

    return $resArr ;
  }
}

?>