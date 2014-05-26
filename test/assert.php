<?php

Class Assert {
  public static function ok ($test, $desc) {
    if($test == false) {
      $msg = 'Assert::ok failed: '.$desc. ' , value: '.$test;
      throw new Exception($msg);
    }
  }
  public static function notOk ($test, $desc) {
    if($test == true) {
      $msg = 'Assert::notOk failed: '.$desc. ' , value: '.$test;
      throw new Exception($msg);
    }
  }
  public static function equal ($value, $expected, $desc) {
    if($value != $expected) {
      $msg = 'Assert::equal failed: '.$desc. ' , value: '.$value.' , expected: '.$expected;
      throw new Exception($msg);
    }
  }
}
?>
