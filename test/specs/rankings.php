<?php
use Assert\Assertion ;
require dirname ( __FILE__ ) . "/../../lib/rankings.inc.php" ;

/* 
Array ( [0] => JD [1] => 50 [2] => 1 [3] => 0 [4] => 60 [5] => on [6] => 0.83333333333333 ) 
0: string
1: string
2: string
3: integer
4: integer
5: string
6: double
*/ 

$tests = array (
  # two player
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) )
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;

  },
  
  # three player
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) )
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;
    $player3 = $results [ 2 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;
    Assertion::same ( $player3 [ 0 ], "Dan" ) ;
    Assertion::same ( $player3 [ 5 ], 3 ) ;
  },

  # three player w/ tie
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "1", 0, 60, null, (15/60) )
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;
    $player3 = $results [ 2 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;
    Assertion::same ( $player3 [ 0 ], "Dan" ) ;
    Assertion::same ( $player3 [ 5 ], 2 ) ;
  },

  # four player
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) ),
      array ( "Jeran", "10", "0", 30, 30, null, (10/30) ),
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;
    $player3 = $results [ 2 ] ;
    $player4 = $results [ 3 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;
    Assertion::same ( $player3 [ 0 ], "Dan" ) ;
    Assertion::same ( $player3 [ 5 ], 3 ) ;
    Assertion::same ( $player4 [ 0 ], "Jeran" ) ;
    Assertion::same ( $player4 [ 5 ], 4 ) ;
  },

  # four player. two-way tie for third
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "0", 45, 45, null, (15/45) ),
      array ( "Jeran", "10", "0", 45, 45, null, (10/45) ),
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;
    $player3 = $results [ 2 ] ;
    $player4 = $results [ 3 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;
    Assertion::same ( $player3 [ 0 ], "Dan" ) ;
    Assertion::same ( $player3 [ 5 ], 3 ) ;
    Assertion::same ( $player4 [ 0 ], "Jeran" ) ;
    Assertion::same ( $player4 [ 5 ], 3 ) ;
  },

  # four player. three-way tie for second
  function () {
    $players = array ( 
      array ( "JD", "50", "1", 0, 60, "on", (50/60) ) ,
      array ( "Tom", "35", "1", 0, 60, null, (35/60) ),
      array ( "Dan", "15", "1", 0, 60, null, (15/60) ),
      array ( "Jeran", "10", "1", 0, 60, null, (10/60) ),
    ) ;

    $results = getWinRanks ( $players ) ;
    
    $player1 = $results [ 0 ] ;
    $player2 = $results [ 1 ] ;
    $player3 = $results [ 2 ] ;
    $player4 = $results [ 3 ] ;

    Assertion::same ( $player1 [ 0 ], "JD" ) ;
    Assertion::same ( $player1 [ 5 ], 1 ) ;
    Assertion::same ( $player2 [ 0 ], "Tom" ) ;
    Assertion::same ( $player2 [ 5 ], 2 ) ;
    Assertion::same ( $player3 [ 0 ], "Dan" ) ;
    Assertion::same ( $player3 [ 5 ], 2 ) ;
    Assertion::same ( $player4 [ 0 ], "Jeran" ) ;
    Assertion::same ( $player4 [ 5 ], 2 ) ;
  }
) ;

?>