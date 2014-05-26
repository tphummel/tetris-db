<?php

require dirname ( __FILE__ )  . '/../assert.php' ;

$tests = array (
  function () {
    $test = true ;
    Assert::ok (is_bool($test), 'test should be a boolean');
    Assert::equal ($test, true, 'test should be true');
  }
) ;

?>
