<?php
use Assert\Assertion ;
require dirname ( __FILE__ )  . "/../../lib/rules.php" ;

# 2+ players including winner must share the longest time in the match
# if two player game and only one player has times set, set for both

$tests = array (
  # pass: two players
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::true ( $result [ "isValid" ] ) ;
  },

  # pass: three players
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
      array ( "Jeran", 15, 0, 55, 55, "", 15/55 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::true ( $result [ "isValid" ] ) ;
  },

  # pass: four players
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
      array ( "Jeran", 15, 1, 5, 65, "", 15/65 ) ,
      array ( "JD", 25, 1, 5, 65, "on", 25/65 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::true ( $result [ "isValid" ] ) ;
  },
  
  # fail: two winners declared
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "There must be exactly one winner. 2 reported" ) ;

  },

  # fail: duplicate player name
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "on", 10/60 ) ,
      array ( "Dan", 20, 1, 0, 60, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "A player may only appear once in a match" ) ;

  }, 

  # fail: invalid value for lines
  function(){
    $fixture = array (
      array ( "Dan", -1, 1, 0, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "Lines value must be >= 0" ) ;

  }, 

  # fail: invalid value for minutes
  function(){
    $fixture = array (
      array ( "Dan", 10, -1, 0, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "Minutes value must be >= 0" ) ;

  }, 

  # fail: invalid value for seconds
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, -1, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "Seconds value must be >= 0" ) ;

  }, 

  # fail: invalid value for total seconds (minutes*60 + seconds)
  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 1, 60, "on", 10/60 ) ,
      array ( "Tom", 20, 1, 0, -1, "", 20/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "Time value must be >= 0" ) ;

  }, 

  # fail: winner's time was not equal to the longest in the match
  function(){
    $fixture = array (
      array ( "Dan", 10, 0, 45, 45, "on", 10/45 ) ,
      array ( "Tom", 20, 1, 0, 60, "", 20/60 ) ,
      array ( "Jeran", 30, 1, 0, 60, "", 30/60 ) ,
    );

    $result = Rules::validateMatch ( $fixture ) ;

    Assertion::false ( $result [ "isValid" ] ) ;
    Assertion::same ( $result [ "errMsg" ] , "Winner must have played the entire match" ) ;

  }
);

?>