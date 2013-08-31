<?php
$dir = dirname ( __FILE__ ) ;
use Assert\Assertion ;
require $dir . "/../../lib/grade.php" ;


# gradePerf ( $time, $lines )

$tests = array (
  function () {
    $gradeScore = gradePerf ( 100 , 100 ) ;
    Assertion::eq ( $gradeScore, "1S-" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 100 , 1 ) ;
    Assertion::eq ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 0 , 1 ) ;
    Assertion::eq ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 20 , 60 ) ;
    Assertion::eq ( $gradeScore, "XS" ) ;
  }
) ;

?>