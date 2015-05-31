<?php

$version = file_get_contents ( "VERSION" ) ;

$health = array (
  "ts" => date ( "Y-m-d H:i:s" ) ,
  "version" => str_replace ( "\n", "", $version ) ,
  "server" => $_SERVER [ "SERVER_ADDR" ] ,
  "status" => "ok"
) ;

$json = json_encode ( $health ) ;
$jsonLen = mb_strlen ( $json, '8bit' );

header ( 'Content-Type: application/json' ) ;
header ( "Content-Length: $jsonLen" ) ;

echo $json ;

?>
