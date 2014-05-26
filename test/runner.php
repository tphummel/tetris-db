<?php
$dir = dirname ( __FILE__  ) ;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$specFiles = array (
  "sample",
  "grade",
  "rankings",
  "helper",
  "rules"
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
    }catch(Exception $e) {
      array_push ( $errors, $e->getTrace() ) ;
    }

  }

}

echo "<div>files: $fileCount</div>" ;
echo "<div>tests: $testCount</div>" ;
echo "<div>pass: $passCount</div>" ;
$failCount = count ( $errors ) ;

if($failCount == 0) {
  echo "pass. exit 0";
  exit(0);

}else{
  echo "<div>fail: $failCount </div>" ;
  foreach ($errors as $error) {
    $errFile = $error[0]["file"] ;
    $errLine = $error[0]["line"] ;
    $args = $error[0]["args"] ;
    $fn = $error[0]["function"] ;
    $errText = $errFile . " : " . $errLine . " : " . $fn . " : (" . implode ( $args, ", " ) . ")";

    echo "<div>$errText</div>" ;
  }

  echo "fail. exit 1";
  exit(1);
}

?>
