<html>
<head>
<title>Performance Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>

<body>

<?php
/*
rptPerformance.php
reports greatest/worst single game performances in a number of categories
*/


//left navbar / banner
include_once("header.php");
require_once("config/db.php");
require_once("lib/points.inc.php");
require_once("lib/grade.php");
require_once("lib/statPower.php");

 
//create connection obj
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
		

		
$rptOptions = array(
"Most Lines", 		
"Most Time",  		
"Least Time", 		
"Highest LPS", 		
"Highest LPS > 99",
"Highest LPS > 199",
"Worst LPS > 0", 	
//"Highest Power", 	
"Longest Shutout",  
"Shortest Oh-Oh",
"Shortest S",
"Longest S",
"Exactly S",
"High LPS - Last Wrank",
"High LPS - Last Erank",
"Low LPS - 1st Wrank",
"01-59 Naturals",
"100-159 Naturals",
"200-259 Naturals",
"Exact Naturals",
"Lines Under 1 Min",
"Lines Under 2 Min",
"Lines Under 3 Min"
); 

$rptNumRecs = array(
10,
25,
50,
100, 'ALL');

$rptPlyrs = array(
'ALL', 4, 3, 2);
?>

<div class="report">
<h1>Individual Performance Report</h1>
<form name="criteria" method="get" action="rptPerformance.php">
<table align="center">
<tr><td>Report Options:</td><td>
<select name="rptOption">

<?php

foreach ($rptOptions as $i)
{	
	if (isset($_GET["rptOption"]) && $i == $_GET["rptOption"])
	{
		echo '<option value="' . $i . '" selected>' . $i . '</option>';
	}
	else
	{
		echo '<option value="' . $i . '">' . $i . '</option>';
	}
}

?>

</select></td></tr>
<tr><td>Match Type(s):</td><td>
<select name="rptPlyrs">

<?php

foreach ($rptPlyrs as $n)
{
	if(isset($_GET["rptPlyrs"]) && $n == $_GET["rptPlyrs"])
	{
		echo '<option values="' . $n . '" selected>' . $n . '</option>';
	}
	else
	{
		echo '<option values="' . $n . '">' . $n . '</option>';
	}
}

?>

</select></td></tr>
<tr><td># of Records:</td><td>
<select name="rptNumRecs">

<?php

foreach ($rptNumRecs as $n)
{
	if(isset($_GET["rptNumRecs"]) && $n == $_GET["rptNumRecs"])
	{
		echo '<option values="' . $n . '" selected>' . $n . '</option>';
	}
	else
	{
		echo '<option values="' . $n . '">' . $n . '</option>';
	}
}

?>

</select></td></tr>

<tr><td colspan="2" align="center"><input type="submit" value="Generate"></form></td></tr>
</table>
</div>


<!-- REPORT BODY -->
<div class="report">
<?php

if (isset($_GET["rptOption"])){
$rptOption = $_GET["rptOption"];
$rptRecs = $_GET["rptNumRecs"];
if($rptRecs == 'ALL'){
	$rptRecs = 1000;
}
if(array_key_exists("rptPlyrs",$_GET)){
	$rptPlyrs = $_GET["rptPlyrs"];
}else{
	$rptPlyrs = 'ALL';
}
if($rptPlyrs == 'ALL'){
	$rptPlyrs = '2,3,4';
}

if(array_key_exists("rptBeg",$_GET)){
	$rptBeg = $_GET["rptBeg"];
}
if(array_key_exists("rptEnd",$_GET)){
	$rptBeg = $_GET["rptBeg"];
}

	//get query string for each repoPower2	Power3 power2	power3 power2	power3rt
	switch ($rptOption)
	{
		case "Most Lines" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC 
			LIMIT " . $rptRecs;
		break;
	
		case "Most Time" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time DESC 
			LIMIT " . $rptRecs;
		break;
		
		case "Least Time" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time ASC 
			LIMIT " . $rptRecs;
		break;
		
		case "Highest LPS" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid 
			AND m.location = l.locationid
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time DESC 
			LIMIT " . $rptRecs;
		break;
		
		case "Highest LPS > 99" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines > 99
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time DESC  
			LIMIT " . $rptRecs;
		break;
		
		case "Highest LPS > 199" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines > 199
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time DESC   
			LIMIT " . $rptRecs;
		break;
		
		case "Worst LPS > 0" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines > 0
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time ASC  
			LIMIT " . $rptRecs;
		break;
		
		case "Highest Power" :
		
		break;
		
		case "Longest Shutout" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines = 0
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time DESC 
			LIMIT " . $rptRecs;
		break;
		
		case "Shortest Oh-Oh" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines = 0
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time ASC 
			LIMIT " . $rptRecs;
		break;
		
		case "Shortest S" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines/pm.time >= 1
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time ASC, pm.lines/pm.time DESC
			LIMIT " . $rptRecs;
		break;
		
		case "Longest S" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines/pm.time >= 1
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time DESC, pm.lines/pm.time DESC
			LIMIT " . $rptRecs;
		break;
		
		case "Exactly S" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.lines/pm.time = 1
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.time DESC
			LIMIT " . $rptRecs;
		break;
		
		case "High LPS - Last Wrank":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.wrank = (select count(playerid) from playermatch where matchid = pm.matchid)
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time DESC
			LIMIT " . $rptRecs;
		break;
		
		case "High LPS - Last Erank":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.erank = (select count(playerid) from playermatch where matchid = pm.matchid)
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time DESC
			LIMIT " . $rptRecs;
		
		break;
		
		case "Low LPS - 1st Wrank":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.wrank = 1
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines/pm.time ASC
			LIMIT " . $rptRecs;
		break;
		
		case "01-59 Naturals":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time < 60
			AND pm.lines >= pm.time
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC
			LIMIT " . $rptRecs;
		break;
		
		case "100-159 Naturals":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time BETWEEN 60 AND 119
			AND pm.lines-40 >= pm.time
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC
			LIMIT " . $rptRecs;
		break;
		
		case "200-259 Naturals":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time BETWEEN 120 AND 179
			AND pm.lines-80 >= pm.time
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC
			LIMIT " . $rptRecs;
		break;
		
		case "Exact Naturals":
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND ((pm.time < 60 AND pm.lines = pm.time) OR
			((pm.time BETWEEN 60 AND 119) AND pm.lines-40 = pm.time) OR
			((pm.time BETWEEN 120 AND 179) AND pm.lines-80 = pm.time)) 
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC
			LIMIT " . $rptRecs;
		break;
		
		case "Lines Under 1 Min" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time < 60
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC 
			LIMIT " . $rptRecs;
		break;
		
		case "Lines Under 2 Min" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time < 120
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC 
			LIMIT " . $rptRecs;
		break;
		
		case "Lines Under 3 Min" :
			$select = "
			SELECT m.matchdate, p.username, pm.*, l.locationname,
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt 
			FROM playermatch pm, player p, tntmatch m, location l
			WHERE pm.matchid = m.matchid
			AND p.playerid = pm.playerid
			AND m.location = l.locationid
			AND pm.time < 180
			AND (select count(playerid) from playermatch where matchid = pm.matchid) IN (".$rptPlyrs.")
			ORDER BY pm.lines DESC 
			LIMIT " . $rptRecs;
		break;
}
$result = mysql_query($select, $connection) or die(mysql_error());
$colspan = 13;
?>

<table>
<tr>
<td class="tablehead" colspan="<?php echo $colspan; ?>"><?php echo $rptOption; ?></td>
</tr>
<tr>
<td class="colhead">Rank</td>
<td class="colhead">MatchID</td>
<td class="colhead">Date</td>
<td class="colhead">Site</td>


<td class="colhead">Player</td>
<td class="colhead"># Plyrs</td>
<td class="colhead">Time</td>
<td class="colhead">Lines</td>
<td class="colhead">LPS</td>
<td class="colhead">Grade</td>
<td class="colhead">WPTS</td>
<td class="colhead">EPTS</td>
<td class="colhead">POWER</td></tr>

<?php
$ct = 1;
while ($row = mysql_fetch_array($result))
{
	$mid = $row["matchid"];
	$date = date("m/d/y", strtotime($row["matchdate"]));
	$player = $row["username"];
	$location = $row["locationname"];
	$pid = $row["playerid"];
	$pCount = $row["pCt"];
	$lines = $row["lines"];
	$time = $row["time"];
	
	$wrank = $row["wrank"];
	$erank = $row["erank"];
	$pCt = $row["pCt"];
	
	$wpts = rankToPts($wrank, $pCt);
	$epts = rankToPts($erank, $pCt);
	$lps = number_format($lines/$time,3);
	$grade = gradePerf($time, $lines);
	
	$min = intval($time/60);
	$sec = str_pad($time - $min*60, 2, "0", STR_PAD_LEFT);
	$timeStr = $min . ":" . $sec;
	
	$power = computePower($wpts, $epts, $lps);
	$rowArr = array($ct, $mid, $date, $location, $player, $pCount, $timeStr, $lines);
	array_push($rowArr, $lps, $grade, $wpts, $epts, $power);
	
	echo '<tr>';
	foreach ($rowArr as $ra)
	{
		if($ra == $player)
		{
			echo '<td class="data"><a href="playerinfo.php?playerid=' . $pid . '" target="_blank">' . $ra . '</a></td>';
		}
		elseif($ra == $mid)
		{
			echo '<td class="data"><a href="matchinfo.php?matchid=' . $mid . '">' . $ra . '</a></td>';
		}
		else
		{
			echo '<td class="data">' . $ra . '</td>';
		}
	}
	echo '</tr>';
	$ct++;
}

?>

</table>
</div>

<?php
} //end report body if -above this doesnt show unless $_GET["rptOption"] isset.

include_once("footer.php");
?>
</body>
</html>