<?php

require_once ( __DIR__ . "/lib/grade.php" ) ;
require_once ( __DIR__ . "/lib/points.inc.php" ) ;
require_once ( __DIR__ . "/lib/rankings.php" ) ;
require_once ( __DIR__ . "/lib/rules.php" ) ;
require_once ( __DIR__ . "/lib/statPower.php" ) ;
require_once ( __DIR__ . "/lib/redis.php" ) ;
require_once ( __DIR__  . "/lib/helper.php" ) ;
require_once ( __DIR__  . "/lib/match-console.php" ) ;

$prevSavedMatch = null ;

if ( array_key_exists('action', $_GET ) && $_GET['action'] === 'add' ) {
	$matchToSave = true;
} else {
	$matchToSave = false ;
}

if ( $matchToSave ) {
  unset ( $players ) ;
  $players = Helper::cleanPlayers ( $_POST ) ;

  unset ( $orderedPlayerNames ) ;
  $orderedPlayerNames = array ( ) ;
  foreach ( $players as $player ) {
    $orderedPlayerNames[] = $player [ 0 ] ;
  }

  $location = $_POST [ "location" ] ;
  $note = $_POST [ "note" ] ;

  $valid = Rules::validateMatch ( $players ) ;

  //reshow form with highlights if error is caught
  if( $valid [ "isValid" ] == false ) {
    $error = [
      "region" => true,
      "message" => $valid [ "errMsg" ]
    ];

    $current = [
      "players" => $players,
      "location" => $location,
      "note" => $note
    ];

    $previous = null;

    MatchConsole::render($error, $current, $previous);
    exit();
  }

  Helper::logMatch ( $players, $location ) ;

  $wrankedPlayers = Rankings::setWinRanks ( $players ) ;
  $erankedPlayers = Rankings::setEffRanks ( $wrankedPlayers ) ;

  $match = [
    "location" => $location,
    "note" => $note,
    "players" => $erankedPlayers
  ];

  $prevSavedMatch = Helper::saveMatch ( $match ) ;

  if ( !array_key_exists("session-match-id-inclusive", $_COOKIE) ) {
    setcookie(
      "session-match-id-inclusive",
      $prevSavedMatch["id"],
      null, // session expiration
      "/match.php"
    ) ;
  }

  $erankedInDisplayOrder = array ( ) ;
  foreach ($orderedPlayerNames as $orderedName) {
    foreach ($erankedPlayers as $erankedPlayer) {
      if ($erankedPlayer [ 0 ] === $orderedName) {
        $erankedInDisplayOrder[] = $erankedPlayer ;
      }
    }
  }

  foreach ( $erankedPlayers as $perf ) {
    $perf [ 8 ] = $matchId ;
     // Redis::publishPerformance ( $perf ) ;
  }
}

//this happens on every load
//clear post data for start of new match
for ($q = 1; $q <= 4; $q++) {
  unset ( $_POST [ "player" . $q ] ) ;
}

if ( $prevSavedMatch ) {
  $players = $erankedInDisplayOrder ;
} else {
  for ( $i = 0 ; $i < 4 ; $i++ ) {
    $players = array ( ) ;
    $players[] = array ( "", "", "", "", "", "" ) ;
  }
}

$current = [
  "players" => $players
];

$error = null ;

MatchConsole::render($error, $current, $prevSavedMatch);

?>
