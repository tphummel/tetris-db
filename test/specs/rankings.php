<?php
use Assert\Assertion ;
require dirname ( __FILE__ ) . "/../../lib/rankings.inc.php" ;

/* 
wrank product
Array ( [0] => JD [1] => 50 [2] => 1 [3] => 0 [4] => 60 [5] => on [6] => 0.83333333333333 ) 
0: string
1: string
2: string
3: integer
4: integer
5: string
6: double

erank product = wrank product + [7] = erank (integer)
*/ 

$wrank = array (
  # two player
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) )
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }

  },

  # three player
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) )
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
      array ( "name" => "Dan", "wrank" => 3 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;
    
    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  },

  # three player w/ tie
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "1", 0, 60, null, (15/60) )
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
      array ( "name" => "Dan", "wrank" => 2 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;
    
    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  },

  # four player
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) ),
      array ( "Jeran", "10", "0", 30, 30, null, (10/30) ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
      array ( "name" => "Dan", "wrank" => 3 ),
      array ( "name" => "Jeran", "wrank" => 4 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;
    
    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  },

  # four player. two-way tie for third
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) ),
      array ( "Jeran", "10", "0", 45, 45, null, (10/45) ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
      array ( "name" => "Dan", "wrank" => 3 ),
      array ( "name" => "Jeran", "wrank" => 3 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;
    
    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  },

  # four player. three-way tie for second
  function () {
    $fixture = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "1", 0, 60, null, (15/60) ),
      array ( "Jeran", "10", "1", 0, 60, null, (10/60) ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "wrank" => 1 ),
      array ( "name" => "Tom", "wrank" => 2 ),
      array ( "name" => "Dan", "wrank" => 2 ),
      array ( "name" => "Jeran", "wrank" => 2 ),
    ) ; 

    $results = getWinRanks ( $fixture ) ;
    
    foreach ($results as $i => $player) {
      Assertion::same ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assertion::same ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  }
) ;

?>