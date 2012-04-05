<html>
<head>
<title>Player Day Summaries</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>

<body>

<?php 

require_once("db_login.php");

$connection = mysql_connect($db_host, $db_username, $db_password);
		if(!$connection)
        {
            die ("Could not connect to the database: <br />". mysql_error());
        }
        
        
        $db_select = mysql_select_db($db_database, $connection);
        if (!$db_select)
        {
            die ("Could not select the database: <br />". mysql_error());
        }
		
		
if (isset($_GET["playerid"]))
{
	$player = $_GET["playerid"];
}


$select1 = "SELECT * FROM player where playerid=" . $player;

$result1 = mysql_query($select1, $connection) or die(mysql_error());

$playerdata = mysql_fetch_array($result1);

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

$result2 = mysql_query($select2, $connection) or die(mysql_error());

while($row = mysql_fetch_array($result2))
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
</body>
</html>
