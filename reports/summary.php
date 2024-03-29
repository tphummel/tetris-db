<?php
$title = "Reports : Summary";

//left navbar / banner

$dir = dirname ( __FILE__ ) ;

require_once($dir . "/../templates/header.php");
require_once($dir . "/../config/db.php");
require_once($dir . "/../lib/points.inc.php");
require_once($dir . "/../lib/statPower.php");

$mysqli = mysqli_init();
$mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
$mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

?>
<div class="report">
<h1>Summary Reports</h1>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="get">
  Start Date:<input type="text" name="sDate" id="start"><br>
  End Date:<input type="text" name="eDate" id="end"><br>
  <br>
  <input type="submit" value="submit">

</form>

<?php


//check for params - if exist, fill report body using criteria
if (isset($_GET['sDate']) && isset($_GET['eDate']))
{
  $sDate = $_GET['sDate'];
  $sDate = new DateTime($sDate);
  $startDate = date_format($sDate, 'Y-m-d');

  $eDate = $_GET['eDate'];
  $eDate = new DateTime($eDate);
  $endDate = date_format($eDate, 'Y-m-d');

  echo "$startDate ==> $endDate";

//get overall numbers
?>
<hr><table border="1" cellpadding="4" cellspacing="0">
  <tr><td colspan="12" class="tablehead">Overall</td></tr>
  <tr><td class="colhead">Player</td><td class="colhead">Games</td>
  <td class="colhead">Time</td><td class="colhead">Lines</td><td class="colhead">Time/G</td>
  <td class="colhead">Lines/G</td><td class="colhead">Ratio</td>
  <td class="colhead">Wpts/G</td><td class="colhead">Epts/G</td>
  <td class="colhead">Power</td></tr>
<?php
//get data for each player
    $query = "SELECT playerid FROM player";
    $resultUser = $mysqli->query($query);
    while ($dataU = $resultUser->fetch_array())
      {
      $player = $dataU["playerid"];

      //playermatch OVERALL summary data for each player
      $queryR =
      "select pm.playerid as playerid, max(p.username) as username,
count(pm.matchid) as totgames, sum(pm.time) as tottime, sum(pm.lines) as totlines
from playermatch pm, player p, tntmatch m
where pm.playerid = " . $player . "
AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "'
and pm.playerid = p.playerid
and pm.matchid = m.matchid
group by pm.playerid";

    $resultSum = $mysqli->query($queryR);
    $dataR = $resultSum->fetch_array();

    if(empty($dataR)) // if a user has no records that match criteria
    {
    continue;
    }

    $uid = $dataR["playerid"];
    $uname = $dataR["username"];
    $totgames = $dataR["totgames"];
    $tottime = $dataR["tottime"];

    $tpg = $tottime / $totgames;
    $tpgmin = intval($tpg/60);
    $sec1 = intval($tpg - $tpgmin*60);
    $tpgsec = str_pad($sec1,2,"0", STR_PAD_LEFT);
    $tpgstr = $tpgmin . ":" . $tpgsec;

    $tothour = str_pad(intval($tottime/3600), 2, "0", STR_PAD_LEFT);
    $totmin = str_pad(intval(($tottime-$tothour*3600)/60), 2, "0", STR_PAD_LEFT);
    $totsec = str_pad($tottime - $tothour*3600 - $totmin*60, 2, "0", STR_PAD_LEFT);


    //use hours
    $timeStr = $tothour . ':' . $totmin . ':' . $totsec;

    $totlines = $dataR["totlines"];
    $totLps = number_format($totlines / $tottime,3);
    $totLpg = number_format($totlines / $totgames,2);

    //playermatch detail lines for calculating power
    $queryL = "SELECT pm.lines, pm.time, pm.wrank as wrank, pm.erank as erank,
    (select count(playerid) from playermatch where matchid = pm.matchid) as pCt
    FROM  playermatch pm, tntmatch m
    WHERE pm.matchid = m.matchid
    AND pm.playerid = " . $player . "
    AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "'";

    $resultLines = $mysqli->query($queryL);

    $epts = 0;
    $wpts = 0;

      while ($rec = $resultLines->fetch_array())
        {
        $i = $rec["pCt"];
        $win = $rec["wrank"];
        $pts1 = rankToPts($win, $i); //udf for getting pts in rank.php
        $wpts = $wpts + $pts1; //sum interval wpts

        $eff = $rec["erank"];
        $pts2 = rankToPts($eff, $i);
        $epts = $epts + $pts2; //sum interval epts
      }

    //power calculation
    $eptsg = number_format($epts/$totgames,3);
    $wptsg = number_format($wpts/$totgames,3);
    $power1 = computePower($wptsg, $eptsg, $totLps);
    //write player row
    echo '<tr>
    <td class="data"><a href="/playerinfo.php?playerid=' . $uid . '" target="_blank">' . $uname . '</a></td>
    <td class="data">' . $totgames . '</td><td class="data">' . $timeStr . '</td><td class="data">' . $totlines . '</td>
    <td class="data">' . $tpgstr . '</td><td class="data">' . $totLpg . '</td><td class="data">' . $totLps . '</td>
    <td class="data">' . $wptsg . '</td><td class="data">' . $eptsg . '</td>
    <td class="data">' . $power1 . '</td></tr>';
    }//end player while

  echo '</table>'; //CLOSE matchtype TABLE





//get three data groups 2p, 3p, 4p
for ($i = 4; $i >=2; $i--)
{



  //write header - open table for matchtype
  echo '<hr><table border="1" cellpadding="4" cellspacing="0">
  <tr><td colspan="12" class="tablehead">' . $i . ' Player</td></tr>
  <tr><td class="colhead">Player</td><td class="colhead">Games</td>
  <td class="colhead">Time</td><td class="colhead">Lines</td><td class="colhead">Time/G</td>
  <td class="colhead">Lines/G</td><td class="colhead">Ratio</td>
  <td class="colhead">Wpts/G</td><td class="colhead">Epts/G</td>
  <td class="colhead">Power</td></tr>';

    //get data for each player
    $query = "SELECT playerid FROM player";
    $resultUser = $mysqli->query($query);
    while ($dataU = $resultUser->fetch_array())
      {
      $player = $dataU["playerid"];

      //playermatch summary data for each player
      $queryR =
      "select pm.playerid as playerid, max(p.username) as username,
count(pm.matchid) as totgames, sum(pm.time) as tottime, sum(pm.lines) as totlines
from playermatch pm, player p, tntmatch m
where pm.playerid = " . $player . "
AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "'
and (select count(playerid) from playermatch where matchid = pm.matchid) = ".$i."
and pm.playerid = p.playerid
and pm.matchid = m.matchid
group by pm.playerid";

      $resultSum = $mysqli->query($queryR);
      $dataR = $resultSum->fetch_array();

      if(empty($dataR)) // if a user has no records that match criteria
      {
      continue;
      }

      $uid = $dataR["playerid"];
      $uname = $dataR["username"];
      $totgames = $dataR["totgames"];
      $tottime = $dataR["tottime"];

      $tpg = $tottime / $totgames;
      $tpgmin = intval($tpg/60);
      $sec1 = intval($tpg - $tpgmin*60);
      $tpgsec = str_pad($sec1,2,"0", STR_PAD_LEFT);
      $tpgstr = $tpgmin . ":" . $tpgsec;

      $tothour = str_pad(intval($tottime/3600), 2, "0", STR_PAD_LEFT);
      $totmin = str_pad(intval(($tottime-$tothour*3600)/60), 2, "0", STR_PAD_LEFT);
      $totsec = str_pad($tottime - $tothour*3600 - $totmin*60, 2, "0", STR_PAD_LEFT);


      //use hours
      $timeStr = $tothour . ':' . $totmin . ':' . $totsec;

      $totlines = $dataR["totlines"];
      $totLps = number_format($totlines / $tottime,3);
      $totLpg = number_format($totlines / $totgames,2);

      //playermatch detail lines for calculating power
      $queryL = "SELECT pm.lines, pm.time, pm.wrank as wrank, pm.erank as erank,
    (select count(playerid) from playermatch where matchid = pm.matchid) as pCt
    FROM  playermatch pm, tntmatch m
    WHERE pm.matchid = m.matchid
    and (select count(playerid) from playermatch where matchid = pm.matchid) = ".$i."
    AND pm.playerid = " . $player . "
    AND m.matchdate between '" . $startDate . "' AND '" . $endDate . "'";

      $resultLines = $mysqli->query($queryL);

      $epts = 0;
      $wpts = 0;
        while ($rec = $resultLines->fetch_array())
          {
        //$i = playerCount
        $win = $rec["wrank"];
        $pts1 = rankToPts($win, $i); //udf for getting pts in rank.php
        $wpts = $wpts + $pts1; //sum interval wpts

        $eff = $rec["erank"];
        $pts2 = rankToPts($eff, $i);
        $epts = $epts + $pts2; //sum interval epts
        }

      //power calculation
      $eptsg = number_format($epts/$totgames,3);
      $wptsg = number_format($wpts/$totgames,3);
      $power1 = number_format($wptsg + (.5 * $eptsg) + $totLps,3);

      //write player row
      $playerUrl = "/playerinfo.php?playerid=$uid" ;

      echo '<tr>
      <td class="data"><a href="'.$playerUrl.'">' . $uname . '</a></td>
      <td class="data">' . $totgames . '</td><td class="data">' . $timeStr . '</td><td class="data">' . $totlines . '</td>
      <td class="data">' . $tpgstr . '</td><td class="data">' . $totLpg . '</td><td class="data">' . $totLps . '</td>
      <td class="data">' . $wptsg . '</td><td class="data">' . $eptsg . '</td>
      <td class="data">' . $power1 . '</td></tr>';
    }//end player while

  echo '</table>'; //CLOSE matchtype TABLE

}//end for - matchtype loop

}//end report body if
$mysqli->close();
?>
</div>
<?php

require_once(dirname ( __FILE__ ) . "/../templates/footer.php");
?>
</body>
</html>
