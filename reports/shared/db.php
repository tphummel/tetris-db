<?php

class DB {
  public static function getConn ( ) {
    require dirname ( __FILE__ ) . "/../../config/db.php" ;

    $mysqli = mysqli_init();
    $mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
    $mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

    return $mysqli ;
  }

  public static function sqlToArray ( $sql ) {
    $conn = self::getConn ( ) ;

    $result = $conn->query ( $sql );

    $resArr = array ( ) ;
    while ( $data = $result->fetch_array() ) {
      $resArr[] = $data ;
    }

    return $resArr ;
  }
}

?>
