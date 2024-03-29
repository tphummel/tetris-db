<?php
$title  = "Reports : Player Day Summaries";
include_once "templates/header.php" ;

require_once("config/db.php");

$mysqli = mysqli_init();
$mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
$mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

if (isset($_GET["playerid"])){
  $player = $_GET["playerid"];
}


$select1 = "SELECT * FROM player where playerid=" . $player;

$result1 = $mysqli->query($select1);

$playerdata = $result1->fetch_array();

$first = $playerdata["firstname"];
$last = $playerdata["lastname"];

?>

<h1><?php echo $first . " " . $last; ?></h1>
<table>
<tr>
<td class="tablehead" colspan="11">Player Day Summaries</td>
</tr>
<tr>
<td class="colhead">Date</td>

<td class="colhead">Games</td>
<td class="colhead">Time</td>
<td class="colhead">Lines</td>

<td class="colhead">LPS</td>
<td class="colhead">LPG</td>
</tr>

<?php
$select2 = "SELECT m.matchdate, count(pm.matchid) as games,
sum(pm.lines) as li, sum(pm.time) as ti, sum(pm.lines)/sum(pm.time) as lps, sum(pm.lines)/count(*) as lpg
FROM playermatch pm, tntmatch m
WHERE pm.playerid = " . $player . "
and m.matchid = pm.matchid
GROUP BY m.matchdate
ORDER BY m.matchdate DESC";

$result2 = $mysqli->query($select2);
$mysqli->close();

while($row = $result2->fetch_array())
{
  $date = $row["matchdate"];
  $games = $row["games"];
  $time = $row["ti"];
  $lines = $row["li"];
  $lps = $row["lps"];
  $lpg = $row["lpg"];
  $rowArr = array($date, $games, $time, $lines, $lps, $lpg);

  echo '<tr>';
  foreach ($rowArr as $ra)
  {
    echo '<td class="data">' . $ra . '</td>';
  }
  echo '</tr>';

}

?>
</table>
</div>
<?php


include_once "templates/footer.php";
?>
