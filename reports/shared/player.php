<?php

require_once dirname ( __FILE__ ) . "/db.php" ;

class Player {

  public static function getPlayer ( $id ) {
    $sql = "SELECT * FROM player WHERE playerid = $id" ;

    $result = DB::sqlToArray ( $sql ) ;

    return $result [ 0 ] ;
  }

  public static function getPlayers () {
    $sql = "SELECT * FROM player" ;

    $result = DB::sqlToArray ( $sql ) ;

    return $result ;

  }

}

?>
