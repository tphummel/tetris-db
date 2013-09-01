<?php
$dir = dirname ( __FILE__  ) ;

require "assert.php" ;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$specFiles = array (
  "control",
  "grade"
) ;

$passCount = 0 ;
$errors = array ( ) ;
$fileCount = count ( $specFiles ) ;
$testCount = 0 ; 

foreach ($specFiles as $file) {
  $path = $dir . "/specs/" . $file . ".php" ; 
  
  include_once $path ;

  foreach ($tests as $test) {
    $testCount ++ ;
    try {
      $test ( ) ;
      $passCount++ ;
    }catch(Assert\AssertionFailedException $e) {
      array_push ( $errors, $e ) ;
    }

  }

}

echo "<div>files: $fileCount</div>" ;
echo "<div>tests: $testCount</div>" ;
echo "<div>pass: $passCount</div>" ;
$failCount = count ( $errors ) ;
echo "<div>fail: $failCount </div>" ;
foreach ($errors as $error) {
  echo "<div>$error</div>" ;
}


?>