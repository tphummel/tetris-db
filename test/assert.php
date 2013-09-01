<?php
$dir = dirname ( __FILE__  ) ;

$assertRoot = $dir . "/vendor/assert/lib/Assert/" ;
require $assertRoot . "Assertion.php" ;
require $assertRoot . "AssertionFailedException.php" ;
require $assertRoot . "InvalidArgumentException.php" ;

use Assert\Assertion ;

?>