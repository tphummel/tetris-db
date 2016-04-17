<?php
class Rankings {
  public static function setWinRanks ( $players ) {
    $first = null; //has one and only one player
    $second = array(); //has 1 for sure but can have up to 3
    $third = array(); //can have between 0 and 2
    $fourth = null; //can have between 0 and 1

    if (count($players) == 2)
    {  //2p match - airtight

      foreach ($players as $player)
        {
          if ($player[5] == "on")
          {
            $first = $player;
          }
          else
          {
            $second[] = $player;
          }
        }
    }

    elseif (count($players) == 3)
    {  //3p match
      //find winner
      $counter = 0;
        foreach ($players as $player)
        {
          if ($player[5] == "on")
          {
            $first = $player;
            //echo "first added<br>";
            unset($players[$counter]);
          }
          $counter++;

        }
      //find 2&3
      //two players left in array
        foreach ($players as $player)
        {
          if ($player[4] == $first[4]) //same time as winner?
          {
            $second[] = $player;
            //echo "second added<br>";
          }
          else
          {
            $third[] = $player;
            //echo "third added<br>";
          }
        }
    }

    elseif (count($players) == 4)
    {  //4p match
        //find winner
      $counter1 = 0;
      foreach ($players as $player)
      {
          if ($player[5] == "on")
          {
            $first = $player;
            unset($players[$counter1]);
          }
        $counter1++;
      }

        //make new array with 3 records the indexes as 0,1,2.
        $nonWinners = array_merge($players);

        //find second
        //everyone that is not marked winner but has the same time as the winner is second place
        $counter2 = 0;

        foreach ($nonWinners as $player) //3 left
        {
          if ($player[4] == $first[4])  //same time?
          {
            $second[] = $player;
            unset($nonWinners[$counter2]);
          }
          $counter2++;
        }

        //find third & fourth if necessary

        $nonTopTwo = array_merge($nonWinners);
        $playersLeft = count($nonTopTwo);

        switch($playersLeft)
        {
          case 0: //0 players left - 3 way tie for second
            //skip out
          break;

          case 1: //1 player left - 2 way tie for second
            //remaining player is 4th
            $fourth = $nonTopTwo[0];
          break;

          case 2: //2 players left - 1 player alone in second
            //assign 3rd and 4th or potentially two 3rds if times are the same
            $one = $nonTopTwo[0];
            $two = $nonTopTwo[1];

            if($one[4] == $two[4])
            {
              //tie for third
              $third[] = $one;
              $third[] = $two;
            }
            elseif($one[4] > $two[4])
            {
              //one is third, two is fourth
              $third[] = $one;
              $fourth = $two;
            }
            else
            {
              //two is third, one is fourth
              $third[] = $two;
              $fourth = $one;
            }

          break;

          default: //error

          break;
        } //end switch for last two players
    } //end 4 player match

    unset($players);

    //assign wranks
    $first[5] = 1;
    $wrankedPlayers = array($first);

    foreach($second as $p2)
    {
      $p2[5] = 2;
      $wrankedPlayers[] = $p2;
    }

    if(isset($third))
    {
      foreach($third as $p3)
      {
        $p3[5] = 3;
        $wrankedPlayers[] = $p3;
      }
    }


    if(isset($fourth))
    {
    $fourth[5] = 4;
    $wrankedPlayers[] = $fourth;
    }
    //echo var_dump($wrankedPlayers);
    return $wrankedPlayers;
  }

  public static function setEffRanks ( $wrankedPlayers ) {
    //generate erank

      $lpsArr = array(); //array of player lps values
      $finishedPlayers = array();
      foreach ($wrankedPlayers as $player)
      {
        $lpsArr[] = $player[6];
      }

      //find top lps - could be multiple players

      $erank1 = array(); //between 1-4 players
      $erank2 = array(); //between 1-3 players
      $erank3 = array(); //between 0-2 players
      $erank4 = array(); //between 0-1 players

  #################
  # 11111111111111111
      //get max lps in array
      $e1 = max($lpsArr);
      $total = count($lpsArr);
      //remove all lps' that match max, may be >1 in event of tie.
      for($i = 0; $i <= $total; $i++)
      {
        if(array_key_exists($i, $lpsArr)){
          if($lpsArr[$i] == $e1)
          {
            unset($lpsArr[$i]);
          }
        }
      }

      //if player's lps matches max, put in first place array
      foreach($wrankedPlayers as $p)
      {
        if($e1 == $p[6])
        {
          $erank1[] = $p;
        }
      }


  ##################
  # 2222222222222222
      //get next highest lps after max

      $lpsArr = array_merge($lpsArr);

      if ( count ( $lpsArr ) > 0) {
        $e2 = max($lpsArr);
        //remove all lps' that match max, may be >1 in event of tie.
        for($i = 0; $i <= count($lpsArr); $i++)
        {
          if($lpsArr[$i] == $e2)
          {
            unset($lpsArr[$i]);
          }
        }
        //if player's lps matches max, put in second place array
        foreach($wrankedPlayers as $p)
        {
          if($e2 == $p[6])
          {
            $erank2[] = $p;
          }
        }
      }

  ##################
  # 3333333333333333333
    if(count($lpsArr) > 0)
    {
      //get next highest lps after max
      $lpsArr = array_merge($lpsArr);
      $e3 = max($lpsArr);
      //remove all lps' that match max, may be >1 in event of tie.
      for($i = 0; $i <= count($lpsArr); $i++)
      {
        if($lpsArr[$i] == $e3)
        {
          unset($lpsArr[$i]);
        }
      }
      //if player's lps matches max, put in third place array
      foreach($wrankedPlayers as $p)
      {
        if($e3 == $p[6])
        {
          $erank3[] = $p;
        }
      }
    }

  ##################
  # 4444444444444444444
    if(count($lpsArr) > 0)
    {
      //get next highest lps after max
      $lpsArr = array_merge($lpsArr);
      $e4 = max($lpsArr);
      //remove all lps' that match max, may be >1 in event of tie.
      for($i = 0; $i <= count($lpsArr); $i++)
      {
        if($lpsArr[$i] == $e4)
        {
          unset($lpsArr[$i]);
        }
      }
      //if player's lps matches max, put in fourth place array
      foreach($wrankedPlayers as $p)
      {
        if($e4 == $p[6])
        {
          $erank4[] = $p;
        }
      }
    }

  ############################

    //assign eranks
    $erankedPlayers = array();
    foreach($erank1 as $p)
    {

      $p[7] = 1;
      //var_dump($p);

      $erankedPlayers[] = $p;


    }


    if(count($erank2) > 0)
    {

      foreach($erank2 as $p)
      {
        switch(count($erank1))
        {
          case 1:
            // if 1 person alone in first - second place
            $p[7] = 2;
          break;

          case 2:
            // if 2 people tied to first - third place
            $p[7] = 3;
          break;

          case 3:
            //if 3 people tied for first - fourth place
            $p[7] = 4;
        }
      $erankedPlayers[] = $p;
      }
    }

    if(count($erank3) > 0)
    {
      foreach($erank3 as $p)
      {
        switch(count($erank1) + count($erank2))
        {
          case 2:
            //two people in first two places - third place
            $p[7] = 3;
          break;

          case 3:
            //three people in first two places - fourth place
            $p[7] = 4;
          break;
        }
      $erankedPlayers[] = $p;
      }
    }

    //fourth place - one player max, always fourth
    if(count($erank4) > 0)
    {
      foreach($erank4 as $p)
      {
        $p[7] = 4;

        $erankedPlayers[] = $p;
      }
    }

    return $erankedPlayers;
  }
}
?>
