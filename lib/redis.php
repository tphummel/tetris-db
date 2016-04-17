<?php
require_once dirname ( __FILE__ ) . "/../vendor/redisent-master/src/redisent/Redis.php" ;

class Redis {
  public static function getConn ( ) {
    $redis = new redisent\Redis ( 'redis://localhost' ) ;
    return $redis ;
  }

  public static function publishPerformance ( $perf ) {
    $channel = "tetris:performances" ;

    $conn = self::getConn ( ) ;
    $str = self::stringifyPerformance ( $perf ) ;
    $conn -> publish ( $channel , $str ) ;

  }

  public static function stringifyPerformance ( $perf ) {
    $arrayMap = array (
      0 => "name",
      1 => "lines",
      4 => "time",
      5 => "wrank",
      7 => "erank",
      8 => "matchid"
    ) ;

    $mapped = array ( ) ;

    foreach ( $arrayMap as $i => $field ) {
      $mapped [ $field ] = $perf [ $i ] ;
    }

    return json_encode ( $mapped ) ;
  }

}

?>
