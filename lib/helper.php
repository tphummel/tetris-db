<?php

class Helper {

  private static function isPlayer ( $key ) {
    return strpos ( $key , "player" ) !== false ;
  }

  private static function cleanInt ( $intString ) {
    if ( empty ( $intString ) ) {
      $val = 0 ;
    } else {
      $val = intval ( $intString , 10 ) ;
    }
    return $val ;
  }

  private static function extractPlayers ( $data ) {
    $players = array ( ) ;
    $keys = array_keys ( $data ) ;

    $playerKeys = array_filter ( $keys, "self::isPlayer" ) ;

    foreach ( $playerKeys as $key ) {
      $players[] = $data [ $key ] ;
    }

    return $players ;
  }

  private static function cleanPlayer ( $player ) {

    foreach ( $player as &$field ) {
      $field = trim ( $field ) ;
    }

    if ( $player [ 0 ] == "VACANT" ) {
      return null ;
    }

    $lines = self::cleanInt ( $player [ 1 ] ) ;
    $player [ 1 ] = $lines ;

    $minutes = self::cleanInt ( $player [ 2 ] ) ;
    $player [ 2 ] = $minutes ;

    $seconds = self::cleanInt ( $player [ 3 ] ) ;
    $player [ 3 ] = $seconds ;

    if (array_key_exists ( 4, $player ) ) {
      $isWinner = $player [ 4 ] ;  
    }
    if ( empty ( $isWinner ) ) { $isWinner = "" ; }
    $player [ 5 ] = $isWinner ; 

    $totalSeconds = $minutes * 60 + $seconds ; 
    $player [ 4 ] = $totalSeconds ;

    if ( $totalSeconds > 0 ) { 
      $ratio = $lines / $totalSeconds ;
    }else{
      $ratio = 0 ;
    }
    $player [ 6 ] = $ratio ; 

    return $player ;

  }

  public static function cleanPlayers ($data) {
    $players = self::extractPlayers ( $data ) ;

    foreach ( $players as &$player ) {
      $player = self::cleanPlayer ( $player ) ;
    }
    return array_filter ( $players ) ; 
  }

  public static function matchToString ( $players, $location ) {
    $values = array ( ) ;

    $values[] = $location ;

    foreach ( $players as $player ) {
      array_push ( $values , 
        $player [ 0 ] , $player [ 1 ] , $player [ 4 ] , $player [ 5 ]
      ) ; 

    }

    # location, [name, lines, time, winner] per player
    $line = implode ( $values , "," ) . "\n" ;

    return $line ;

  }

  public static function logMatch ($players, $location) {
    $logFile = dirname ( __FILE__ ) . "/../log/match.log" ;

    $line = self::matchToString ( $players, $location ) ;

    $line = date ( "Y-m-d H:i:s" ) . $line;

    file_put_contents ( $logFile , $line, FILE_APPEND ) ;

  }
}


?>