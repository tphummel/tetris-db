<?php
require_once dirname ( __FILE__ )  . '/../assert.php' ;
require_once dirname ( __FILE__ )  . "/../../lib/grade.php" ;

# gradePerf ( $time, $lines )

$tests = array (
  function () {
    $gradeScore = gradePerf ( 100 , 100 ) ;
    Assert::equal ( $gradeScore, "1S-" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 100 , 1 ) ;
    Assert::equal ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 0 , 1 ) ;
    Assert::equal ( $gradeScore, "F" ) ;
  },
  function () {
    $gradeScore = gradePerf ( 20 , 60 ) ;
    Assert::equal ( $gradeScore, "XS" ) ;
  }
) ;

?>
