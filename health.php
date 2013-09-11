<?php

$health = array ( 
  "ts" => date ( "Y-m-d H:i:s" ) ,
  "server" => $_SERVER [ "SERVER_ADDR" ],
  "status" => "ok"
) ;

$json = json_encode ( $health ) ;
$jsonLen = mb_strlen ( $json, '8bit' );

header ( 'Content-Type: application/json' ) ;
header ( "Content-Length: $jsonLen" ) ;

echo $json ; 

?>