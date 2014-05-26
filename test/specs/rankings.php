<?php
require_once dirname ( __FILE__ )  . '/../assert.php' ;
require_once dirname ( __FILE__ ) . "/../../lib/rankings.php" ;

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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
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

    $results = Rankings::setWinRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 5 ] , $expected [ $i ][ "wrank" ] ) ;
    }
  }
) ;

$erank = array (

  # two player
  function () {
    $fixture = array (
      array ( "JD",  null, null, null, null, null, 0.500 ) ,
      array ( "Tom", null, null, null, null, null, 0.700 ),
    ) ;

    $expected = array (
      array ( "name" => "Tom", "erank" => 1 ),
      array ( "name" => "JD", "erank" => 2 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # two player - tied
  function () {
    $p1Eff = 20 / 40 ;
    $p2Eff = 10 / 20 ;
    $fixture = array (
      array ( "JD",  null, null, null, null, null, $p1Eff ) ,
      array ( "Tom", null, null, null, null, null, $p2Eff ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 1 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },
  // null, null, null, null, null
  # three player
  function () {
    $fixture = array (
      array ( "JD",   null, null, null, null, null, 0.700 ) ,
      array ( "Tom",  null, null, null, null, null, 0.500 ),
      array ( "Jeran",null, null, null, null, null,  0.200 ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 2 ),
      array ( "name" => "Jeran", "erank" => 3 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # three player - two way tie for second
  function () {
    $fixture = array (
      array ( "JD",    null, null, null, null, null, 0.700 ) ,
      array ( "Tom",   null, null, null, null, null, 0.500 ),
      array ( "Jeran", null, null, null, null, null,  0.500 ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 2 ),
      array ( "name" => "Jeran", "erank" => 2 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # three player - two way tie for first
  function () {
    $fixture = array (
      array ( "JD",    null, null, null, null, null, 0.700 ) ,
      array ( "Tom",   null, null, null, null, null, 0.700 ),
      array ( "Jeran", null, null, null, null, null,  0.500 ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 3 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # three player - three way tie for first
  function () {
    $fixture = array (
      array ( "JD",    null, null, null, null, null, 0.700 ) ,
      array ( "Tom",   null, null, null, null, null, 0.700 ),
      array ( "Jeran", null, null, null, null, null, 0.700 ),
    ) ;

    $expected = array (
      array ( "name" => "JD", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 1 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # four player
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.500 ),
      array ( "Tom",   null, null, null, null, null, 1.400 ),
      array ( "JD",    null, null, null, null, null, 1.300 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 2 ),
      array ( "name" => "Tom", "erank" => 3 ),
      array ( "name" => "JD", "erank" => 4 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # four player - two way tie for third
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.500 ),
      array ( "Tom",   null, null, null, null, null, 1.400 ),
      array ( "JD",    null, null, null, null, null, 1.400 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 2 ),
      array ( "name" => "Tom", "erank" => 3 ),
      array ( "name" => "JD", "erank" => 3 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # four player - two way tie for second
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.500 ),
      array ( "Tom",   null, null, null, null, null, 1.500 ),
      array ( "JD",    null, null, null, null, null, 1.400 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 2 ),
      array ( "name" => "Tom", "erank" => 2 ),
      array ( "name" => "JD", "erank" => 4 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # four player - two way tie for first
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.600 ),
      array ( "Tom",   null, null, null, null, null, 1.500 ),
      array ( "JD",    null, null, null, null, null, 1.400 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 3 ),
      array ( "name" => "JD", "erank" => 4 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;

    foreach ($results as $i => $player) {
      Assert::equal ( $player [ 0 ] , $expected [ $i ][ "name" ] ) ;
      Assert::equal ( $player [ 7 ] , $expected [ $i ][ "erank" ] ) ;
    }
  },

  # four player - three way tie for second
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.500 ),
      array ( "Tom",   null, null, null, null, null, 1.500 ),
      array ( "JD",    null, null, null, null, null, 1.500 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 2 ),
      array ( "name" => "Tom", "erank" => 2 ),
      array ( "name" => "JD", "erank" => 2 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;
    // echo "<pre>";print_r ($results);echo "</pre>";
    # something is going on here. 6 results come back
    # looped over expected to be sure we only check four records
    foreach ($expected as $i => $player) {
      Assert::equal ( $results [ $i ] [ 0 ] , $player[ "name" ] ) ;
      Assert::equal ( $results [ $i ] [ 7 ] , $player[ "erank" ] ) ;
    }
  },

  # four player - three way tie for first
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.600 ),
      array ( "Tom",   null, null, null, null, null, 1.600 ),
      array ( "JD",    null, null, null, null, null, 1.500 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 1 ),
      array ( "name" => "JD", "erank" => 4 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;
    foreach ($expected as $i => $player) {
      Assert::equal ( $results [ $i ] [ 0 ] , $player[ "name" ] ) ;
      Assert::equal ( $results [ $i ] [ 7 ] , $player[ "erank" ] ) ;
    }
  },

  # four player - four way tie for first
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.600 ),
      array ( "Tom",   null, null, null, null, null, 1.600 ),
      array ( "JD",    null, null, null, null, null, 1.600 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 1 ),
      array ( "name" => "Tom", "erank" => 1 ),
      array ( "name" => "JD", "erank" => 1 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;
    foreach ($expected as $i => $player) {
      Assert::equal ( $results [ $i ] [ 0 ] , $player[ "name" ] ) ;
      Assert::equal ( $results [ $i ] [ 7 ] , $player[ "erank" ] ) ;
    }
  },

  # four player - ranks zero lps properly
  function () {
    $fixture = array (
      array ( "Dan",   null, null, null, null, null, 1.600 ),
      array ( "Jeran", null, null, null, null, null, 1.200 ),
      array ( "Tom",   null, null, null, null, null, 1.000 ),
      array ( "JD",    null, null, null, null, null, 0.000 ),
    ) ;

    $expected = array (
      array ( "name" => "Dan", "erank" => 1 ),
      array ( "name" => "Jeran", "erank" => 2 ),
      array ( "name" => "Tom", "erank" => 3 ),
      array ( "name" => "JD", "erank" => 4 ),
    ) ;

    $results = Rankings::setEffRanks ( $fixture ) ;
    foreach ($expected as $i => $player) {
      Assert::equal ( $results [ $i ] [ 0 ] , $player[ "name" ] ) ;
      Assert::equal ( $results [ $i ] [ 7 ] , $player[ "erank" ] ) ;
    }
  },
) ;


$tests = array_merge ( $wrank, $erank ) ;

?>
