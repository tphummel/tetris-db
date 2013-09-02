<?php

class Rules {
  public static function validateMatch ( $match ) {
    $isValid = true ;
    $errMsg = "" ;

    try {
      self::isExactlyOneWinner ( $match ) ;
      self::isUniquePlayerNames ( $match ) ;
      self::isLinesValid ( $match ) ;
      self::isMinutesValid ( $match ) ;
      self::isSecondsValid ( $match ) ;
      self::isTimeValid ( $match ) ;
      self::isWinnerTimeMax ( $match ) ;
        
    } catch ( Exception $e ) {
      $isValid = false ;
      $errMsg = $e->getMessage ( ) ;
    }

    return compact ( "isValid", "errMsg" ) ;
    
  }

  private static function isExactlyOneWinner ( $match ) {
    $winnerIx = 5 ;
    $winnerCt = 0 ;
    foreach ( $match as $i => $player ) {
      if ( $player [ $winnerIx ] == "on" ) {
        $winnerCt ++ ;
      }
    }

    if ( $winnerCt !== 1 ) {
      throw new Exception ( "There must be exactly one winner. $winnerCt reported" ) ;
    }
  }

  private static function isUniquePlayerNames ( $match ) {
    $nameIx = 0 ;
    $nameCts = array ( ) ;

    foreach ( $match as $player ) {
      $name = $player [ $nameIx ] ;

      if ( empty ($nameCts [ $name ] ) ) {
        $nameCts [ $name ] = 0 ;
      }
      $nameCts [ $name ] ++ ;
    }

    foreach ( $nameCts as $ct ) {
      if ( $ct > 1 ) {
        throw new Exception ( "A player may only appear once in a match" ) ;
      }
    }
  }

  private static function isLinesValid ( $match ) {
    $linesIx = 1 ;

    foreach ( $match as $player ) {
      if ( $player [ $linesIx ] < 0 ) {
        throw new Exception ( "Lines value must be >= 0" ) ;
      }
    }
  }

  private static function isMinutesValid ( $match ) {
    $minutesIx = 2 ;

    foreach ( $match as $player ) {
      if ( $player [ $minutesIx ] < 0 ) {
        throw new Exception ( "Minutes value must be >= 0" ) ;
      }
    }
  }

  private static function isSecondsValid ( $match ) {
    $secondsIx = 3 ;

    foreach ( $match as $player ) {
      if ( $player [ $secondsIx ] < 0 ) {
        throw new Exception ( "Seconds value must be >= 0" ) ;
      }
    }
  }

  private static function isTimeValid ( $match ) {
    $timeIx = 4 ;

    foreach ( $match as $player ) {
      if ( $player [ $timeIx ] < 0 ) {
        throw new Exception ( "Time value must be >= 0" ) ;
      }
    }
  }

  private static function isWinnerTimeMax ( $match ) {
    $maxTime = 0 ;
    $timeIx = 4 ;
    $winnerIx = 5 ;

    foreach ( $match as $player ) {
      $time = $player [ $timeIx ] ;
      if ( $time > $maxTime ) {
        $maxTime = $time ;
      }
    }

    foreach ( $match as $player ) {
      if ( $player [ $winnerIx ] === "on" ) {
        if ( $player [ $timeIx ] !== $maxTime ) {
          throw new Exception ( "Winner must have played the entire match" ) ;
        }
      }
    }
  } 
}

?>