<?php

use Assert\Assertion ;

$tests = array (
  function () {
    $test = true ;
    Assertion::boolean ( $test ) ;
    Assertion::true ( $test ) ;
  }
) ;

?>