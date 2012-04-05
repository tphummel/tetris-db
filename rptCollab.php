<html>
<head>
<title>Match Strength</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>

<body>

<?php

//left navbar / banner

include_once("header.php");
require_once("db_login.php");
require_once("points.inc.php");
require_once("grade.php");
require_once("statPower.php");
require_once("points.inc.php");

 
//create connection obj
		$connection = mysql_connect($db_host, $db_username, $db_password);
		if(!$connection){
            die ("Could not connect to the database: <br />". mysql_error());
        }
        
        
        $db_select = mysql_select_db($db_database, $connection);
        if (!$db_select){
            die ("Could not select the database: <br />". mysql_error());
        }
		// initializing to null, will return false from isset below if not set
		$startDate = NULL;
		$endDate = NULL;
		if (isset($_GET['sDate'],$_GET['eDate'])){
			$sDate = $_GET['sDate'];
			$sDate = new DateTime($sDate);
			$startDate = date_format($sDate, 'Y-m-d');
			
			$eDate = $_GET['eDate'];
			$eDate = new DateTime($eDate);
			$endDate = date_format($eDate, 'Y-m-d');
			
			//echo "$startDate ==> $endDate";
		}else{
			$startDate = '2000-01-01';
			$endDate = '2020-12-31';
		}
		?>
	<div class="report">
		<h1>Match Strength Report</h1>
			<form action="rptCollab.php" method="GET">
			Start Date:<input type="text" name="sDate" id="start" value="<?php echo $startDate; ?>" />
			<br />
			End Date:<input type="text" name="eDate" id="end" value="<?php echo $endDate; ?>" /><br />
			<select name="type">
				<option value="2">2P</option>
				<option value="3">3P</option>
				<option value="4">4P</option>
			</select>
			<select name="sort">
				<optgroup label="Good">
					<option value="mostlines">Most Lines</option>
					<option value="mosttime">Most Time</option>
					<option value="highratio">Highest Ratio</option>
				</optgroup>
				<optgroup label="Bad">
					<option value="fewlines">Fewest Lines</option>
					<option value="fewtime">Fewest Time</option>
					<option value="lowratio">Lowest Ratio</option>
				</optgroup>
			</select>
			<br />
			<input type="submit" value="submit">
			</form>
			<?php
		if(array_key_exists('sort', $_GET)){
			if(!empty($_GET['sort'])){
				$superCols = 7;
				$plyrCols = 3;
				$totCols = $superCols+($_GET['type']*$plyrCols);
				$chartTitle = "";
				$query = "
						SELECT m.*, 
							p1.playerid AS p1id, (SELECT username FROM player WHERE playerid = p1.playerid) as p1name, 
								p1.lines AS p1lines, p1.time AS p1time, p1.wrank AS p1wrank, p1.erank AS p1erank,
							p2.playerid AS p2id, (SELECT username FROM player WHERE playerid = p2.playerid) as p2name, 
								p2.lines AS p2lines, p2.time AS p2time, p2.wrank AS p2wrank, p2.erank AS p2erank";
							if($_GET['type']>2){
								$query = $query."	
							,p3.playerid AS p3id, (SELECT username FROM player WHERE playerid = p3.playerid) as p3name, 
								p3.lines AS p3lines, p3.time AS p3time, p3.wrank AS p3wrank, p3.erank AS p3erank";
							}
							if($_GET['type']==4){
								$query = $query." 
							,p4.playerid AS p4id, (SELECT username FROM player WHERE playerid = p4.playerid) as p4name, 
								p4.lines AS p4lines, p4.time AS p4time, p4.wrank AS p4wrank, p4.erank AS p4erank";
							}
						$query = $query."
						FROM (
							SELECT t.matchid, MAX(t.matchdate) AS rawdate, DATE_FORMAT(MAX(t.matchdate),'%m/%d/%Y') AS stringdate, SUM(p.lines) AS totallines,
								SUM(p.time) AS totalseconds, FLOOR(SUM(p.time)/60) AS floorminutes, MOD(SUM(p.time),60) AS remainingseconds,
								ROUND(SUM(p.lines)/SUM(p.time),3) AS ratio
							FROM tntmatch t, playermatch p
							WHERE t.matchid = p.matchid
								AND t.matchdate BETWEEN '".$startDate."' AND '".$endDate."'
								AND (SELECT COUNT(playerid) FROM playermatch WHERE matchid = t.matchid) = ".$_GET['type']."
							GROUP BY t.matchid) m,
							playermatch p1, playermatch p2";
							if($_GET['type']>2){
								$query = $query.", playermatch p3";
							}
							if($_GET['type']==4){
								$query = $query.", playermatch p4";
							}
							$query = $query." 
						WHERE m.matchid = p1.matchid 
							AND m.matchid = p2.matchid
							AND p1.wrank = 1
							AND p2.wrank = 2";
							if($_GET['type']>2){
								$query = $query." 
								AND m.matchid = p3.matchid
								AND p3.wrank = 3";
							}
							if($_GET['type']==4){
								$query = $query." 
								AND m.matchid = p4.matchid
								AND p4.wrank = 4";
							}
				//check for params - if exist, fill report body using criteria
				switch($_GET['sort']){
					case 'mostlines':
						$title = "Most Combined Lines in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totallines DESC, m.rawdate DESC LIMIT 100";
					break;
					case 'mosttime':
						$title = "Most Combined Time in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totalseconds DESC, m.rawdate DESC LIMIT 100";
					break;
					case 'highratio':
						$title = "Highest Combined Ratio in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totallines/m.totalseconds DESC, m.rawdate DESC LIMIT 100";
					break;
					
					case 'fewlines':
						$title = "Fewest Combined Lines in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totallines ASC, m.rawdate DESC LIMIT 100";
					break;
					case 'fewtime':
						$title = "Fewest Combined Time in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totalseconds ASC, m.rawdate DESC LIMIT 100";
					break;
					case 'lowratio':
						$title = "Lowest Combined Ratio in a ".$_GET['type']."P Match, $startDate => $endDate";
						$query = $query." ORDER BY m.totallines/m.totalseconds ASC, m.rawdate DESC LIMIT 100";
					break;
					
				}//close switch sort
				//added to appease error on byethost.com
				mysql_query("SET OPTION SQL_BIG_SELECTS=1");  
				$result = mysql_query($query, $connection) or die(mysql_error());
				$data = mysql_fetch_array($result);
				//echo $query;
				?>
				<table border="1" cellpadding="4" cellspacing="0">
				<caption><?php echo $title?></caption>
				<tr>
					<td colspan="<?php echo $superCols; ?>" class="tablehead">Aggregate</td>
					<?php
						for($i=1;$i<=$_GET['type'];$i++){
							$plyrhead = "";
							if($i == 1){
								$plyrhead = "First";
							}else if($i==2){
								$plyrhead = "Second";
							}else if($i==3){
								$plyrhead = "Third";
							}else if($i==4){
								$plyrhead = "Fourth";
							}
						?>
							<td colspan="<?php echo $plyrCols; ?>" class="tablehead"><?php echo $plyrhead; ?></td>
						<?php
						}
					?>
				</tr> 
				<tr>
					<td class="colhead">Rk</td>
					<td class="colhead">Match</td><td class="colhead">Date</td>
					<td class="colhead">Time</td><td class="colhead">Lines</td><td class="colhead">LPS</td>
					<td class="colhead">Grade</td>
					<?php
						for($i=1;$i<=$_GET['type'];$i++){
						?>
							<td class="colhead">Name</td>
							<td class="colhead">Eff</td>
							<td class="colhead">Power</td>
						<?php
						}
					?>
			
				</tr>
				<?php
				
				$rowCount = 1;
				while ($row = mysql_fetch_array($result)){
					?>
					<td class="data"><?php echo $rowCount; ?></td>
					<td class="data"><a href="matchinfo.php?matchid=<?php echo $row['matchid']; ?>" ><?php echo $row['matchid']; ?></a></td>
					<td class="data"><a href="rptSummary.php?sDate=<?php echo $row['rawdate']; ?>&eDate=<?php echo $row['rawdate']; ?>" ><?php echo $row['stringdate']; ?></a></td>
					<td class="data"><?php echo $row['floorminutes'].":".str_pad($row['remainingseconds'], 2, "0", STR_PAD_LEFT); ?></td>
					<td class="data"><?php echo $row['totallines'] ?></td>
					<td class="data"><?php echo number_format($row['totallines'] / $row['totalseconds'],3) ?></td>
					<td class="data"><?php echo gradePerf($row['totalseconds'], $row['totallines']); ?></td>
					
					<?php
					for($i=1;$i<=$_GET['type'];$i++){
					?>
						<td class="data"><a href="playerinfo.php?playerid=<?php echo $row['p'.$i.'id']; ?>" ><?php echo $row['p'.$i.'name']; ?></a></td>
						<td class="data"><?php echo gradePerf($row['p'.$i.'time'], $row['p'.$i.'lines']); ?></td>
						<td class="data"><?php echo computePower(rankToPts($row['p'.$i.'wrank'], $_GET['type']), rankToPts($row['p'.$i.'erank'], $_GET['type']), $row['p'.$i.'lines']/$row['p'.$i.'time']); ?></td>
					<?php
					}
					?>
					</tr>
					<?php
					$rowCount++;
				}
				?>
				</table>
				<?php
			} //close not empty sort
		}//close array key exists
		
	?>
	<!--close report div -->
	</div>
<?php
include_once("footer.php");
?>
</body>
</html>