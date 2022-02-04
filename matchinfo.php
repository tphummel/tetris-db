<?php
$title = "Reports : Match Detail";
include_once("templates/header.php");

require_once("config/db.php");
require_once("lib/points.inc.php");
require_once("lib/grade.php");
require_once("lib/statPower.php");

$mysqli = mysqli_init();
$mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
$mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

$match = -1;
if (isset($_GET["matchid"]))
{
  $match = $_GET["matchid"];
}

$select1 = "
  SELECT m.*,
  l.locationname, l.address, l.city,
  (select count(playerid)
    from playermatch where matchid = m.matchid) as pCt
  FROM tntmatch m, location l
  WHERE matchid=" . $match .
  " AND l.locationid = m.location";
$result1 = $mysqli->query($select1);
$sumData = $result1->fetch_array();
$matchid = $sumData["matchid"];
$matchdate = new DateTime($sumData["matchdate"]);
$pCt = $sumData["pCt"];

$select2 = "select pm.lines, pm.time, pm.wrank, pm.erank, p.username, pm.playerid,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt
FROM playermatch pm, player p where p.playerid = pm.playerid and matchid=" . $match . " order by wrank asc";

$result2 = $mysqli->query($select2);
$mysqli->close();

?>
<div class="report">
<?php echo "#" . $matchid . "<br />" . date_format($matchdate, 'm/d/Y') . "<br />" . $sumData["address"] . " - " . $sumData["city"]; ?>

<table align="center">
<tr>
<td class="tablehead" colspan="8">Match Detail</td>
</tr>
<tr>
<td class="colhead">Player</td>
<td class="colhead">Time</td>
<td class="colhead">Lines</td>
<td class="colhead">LPS</td>
<td class="colhead">Grade</td>
<td class="colhead">WRank</td>
<td class="colhead">ERank</td>
<td class="colhead">Power</td>
</tr>

<?php //each player in match
while ($row = $result2->fetch_array())
{
  $playerid = $row["playerid"];
  $name = $row["username"];
  $lines = $row["lines"];
  $time = $row["time"];
  $wrank = $row["wrank"];
  $erank = $row["erank"];

  $wpts = rankToPts($wrank, $pCt);
  $epts = rankToPts($erank, $pCt);
  $lps = number_format($lines/$time,3);
  $grade = gradePerf($time, $lines);

  $min = intval($time/60);
  $sec = str_pad($time - $min*60, 2, "0", STR_PAD_LEFT);
  $timeStr = $min . ":" . $sec;

  $power = computePower($wpts, $epts, $lines/$time);
  $dataArr = array($name, $timeStr, $lines, $lps, $grade, $wrank, $erank, $power);

  echo '<tr>';


  foreach ($dataArr as $ra)
  {
    if($ra == $name)
    {
      echo '<td class="data"><a href="playerinfo.php?playerid=' . $playerid . '">' . $ra . '</a></td>';
    }
    else
    {
      echo '<td class="data">' . $ra . '</td>';
    }
  }
  echo '</tr>';
}//end while

?>
</table>
</div>
<?php
include_once("templates/footer.php");
?>

</body>
</html>
