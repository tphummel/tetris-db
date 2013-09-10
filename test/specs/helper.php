<?php
use Assert\Assertion ;
require dirname ( __FILE__ )  . "/../../lib/helper.php" ;

$cleanPlayers = array (
  # two player
  function(){
    $fixture = array (
      "player1" => array ( "Dan", "10", "1", "0" ) ,
      "player2" => array ( "Tom", "20", "1", "0", "on" ) ,
      "player3" => array ( "VACANT", null, null, null ) , 
      "player4" => array ( "VACANT", null, null, null ) , 
    );

    $result = Helper::cleanPlayers ( $fixture ) ;

    Assertion::eq ( count ( $result ) , 2 ) ;

    $expected = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
    );

    foreach ( $result as $i => $player ) {
      $toMatch = $expected [ $i ] ;

      foreach ( $player as $j => $field ) {
        Assertion::same ( $toMatch [ $j ] , $field , "failed $i $j" ) ;
      }
    }
  },

  # three player
  function(){
    $fixture = array (
      "player1" => array ( "Dan", "10", "1", "0" ) ,
      "player2" => array ( "Tom", "20", "1", "0", "on" ) ,
      "player3" => array ( "Jeran", "30", "0", "45" ) , 
      "player4" => array ( "VACANT", null, null, null ) , 
    );

    $result = Helper::cleanPlayers ( $fixture ) ;

    Assertion::eq ( count ( $result ) , 3 ) ;

    $expected = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
      array ( "Jeran", 30, 0, 45, 45, "", 30/45 ) ,
    );

    foreach ( $result as $i => $player ) {
      $toMatch = $expected [ $i ] ;

      foreach ( $player as $j => $field ) {
        Assertion::same ( $toMatch [ $j ] , $field , "failed $i $j" ) ;
      }
    }
  },
  # four player
  function(){
    $fixture = array (
      "player1" => array ( "Dan", "10", "1", "0" ) ,
      "player2" => array ( "Tom", "20", "1", "0", "on" ) ,
      "player3" => array ( "Jeran", "30", "0", "45" ) , 
      "player4" => array ( "JD", "15", "0", "30" ) , 
    );

    $result = Helper::cleanPlayers ( $fixture ) ;

    Assertion::eq ( count ( $result ) , 4 ) ;

    $expected = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
      array ( "Jeran", 30, 0, 45, 45, "", 30/45 ) ,
      array ( "JD", 15, 0, 30, 30, "", 15/30 ) ,
    );

    foreach ( $result as $i => $player ) {
      $toMatch = $expected [ $i ] ;

      foreach ( $player as $j => $field ) {
        Assertion::same ( $toMatch [ $j ] , $field , "failed $i $j" ) ;
      }
    }
  }
);

$matchToString = array (

  function(){
    $fixture = array (
      array ( "Dan", 10, 1, 0, 60, "", 10/60 ) ,
      array ( "Tom", 20, 1, 0, 60, "on", 20/60 ) ,
      array ( "Jeran", 30, 0, 45, 45, "", 30/45 ) ,
      array ( "JD", 15, 0, 30, 30, "", 15/30 ) ,
    );

    $location = "Tom's House" ;

    $result = Helper::matchToString ( $fixture , $location ) ;

    $expected = "Tom's House,Dan,10,60,,Tom,20,60,on,Jeran,30,45,,JD,15,30,\n" ; 

    Assertion::same ( $result, $expected , "match to string failed" ) ;
  }
) ; 

$tests = array_merge ( $cleanPlayers, $matchToString ) ;

?>