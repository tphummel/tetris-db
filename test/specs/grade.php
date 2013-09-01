<?php
use Assert\Assertion ;
require dirname ( __FILE__ )  . "/../../lib/grade.php" ;


# gradePerf ( $time, $lines )

$tests = array (
  function () {
    $gradeScore = gradePerf ( 100 , 100 ) ;
    Assertion::same ( $gradeScore, "1S-" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 100 , 1 ) ;
    Assertion::same ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 0 , 1 ) ;
    Assertion::same ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 20 , 60 ) ;
    Assertion::same ( $gradeScore, "XS" ) ;
  }
) ;

?>