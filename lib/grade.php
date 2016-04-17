<?php

function gradePerf($time, $lines)
{
  if($time > 0){
    $eRate = $lines/$time;
  }else{
    $eRate = 0;
  }
  $grade;

  if ($eRate < .6)
  {
    $grade = "F";

  }
  elseif ($eRate < .63)
  {
    $grade = "D-";
  }
  elseif ($eRate < .67)
  {
    $grade = "D";
  }
  elseif ($eRate < .7)
  {
    $grade = "D+";
  }
  elseif ($eRate < .73)
  {
    $grade = "C-";
  }
  elseif ($eRate < .77)
  {
    $grade = "C";
  }
  elseif ($eRate < .8)
  {
    $grade = "C+";
  }
  elseif ($eRate < .83)
  {
    $grade = "B-";
  }
  elseif ($eRate < .87)
  {
    $grade = "B";
  }
  elseif ($eRate < .9)
  {
    $grade = "B+";
  }
  elseif ($eRate < .93)
  {
    $grade = "A-";
  }
  elseif ($eRate < .97)
  {
    $grade = "A";
  }
  elseif ($eRate < 1.0)
  {
    $grade = "A+";
  }
  elseif ($eRate < 1.05)
  {
    $grade = "1S-";
  }
  elseif ($eRate < 1.15)
  {
    $grade = "1S";
  }
  elseif ($eRate < 1.2)
  {
    $grade = "1S+";
  }
  elseif ($eRate < 1.25)
  {
    $grade = "2S-";
  }
  elseif ($eRate < 1.35)
  {
    $grade = "2S";
  }
  elseif ($eRate < 1.4)
  {
    $grade = "2S+";
  }
  elseif ($eRate < 1.45)
  {
    $grade = "3S-";
  }
  elseif ($eRate < 1.55)
  {
    $grade = "3S";
  }
  elseif ($eRate < 1.6)
  {
    $grade = "3S+";
  }
  elseif ($eRate < 1.65)
  {
    $grade = "4S-";
  }
  elseif ($eRate < 1.75)
  {
    $grade = "4S";
  }
  elseif ($eRate < 1.8)
  {
    $grade = "4S+";
  }
  elseif ($eRate < 1.85)
  {
    $grade = "5S-";
  }
  elseif ($eRate < 1.95)
  {
    $grade = "5S";
  }
  elseif ($eRate < 2)
  {
    $grade = "5S+";
  }
  elseif ($eRate >= 2)
  {
    $grade = "XS";
  }
  else
  {
    $grade = "NA";
  }

  return $grade;

}

?>
