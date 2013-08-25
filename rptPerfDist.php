<?php
$title = "Report : Win Expectancy";
//left navbar / banner
include_once("templates/header.php");
require_once("config/db.php");
//create connection obj
		$connection = mysql_connect($db_host, $db_username, $db_password);
		if(!$connection){
            die ("Could not connect to the database: <br />". mysql_error());
        }
        $db_select = mysql_select_db($db_database, $connection);
        if (!$db_select){
            die ("Could not select the database: <br />". mysql_error());
        }
		// show form
		if(array_key_exists('type',$_GET)){
			$matchtype = $_GET['type'];
		}else{
			$matchtype = "";
		}
		?>
		<div class="report">
		<h1>Win Expectancy Report</h1>
			<form action="rptPerfDist.php" method="GET">
			<select name="type">
				<option value="2" <?php echo ($matchtype=="2")?" SELECTED":"" ?>>2P</option>
				<option value="3" <?php echo ($matchtype=="3")?" SELECTED":"" ?>>3P</option>
				<option value="4" <?php echo ($matchtype=="4")?" SELECTED":"" ?>>4P</option>
				<option value="ALL" <?php echo ($matchtype=="ALL")?" SELECTED":"" ?>>ALL</option>
			</select>
			<input type="submit" value="submit">
			</form>
		<?php
		if(array_key_exists('type',$_GET)){
			if($matchtype == 'ALL'){
				$title = "All Match Types";
				$query = "
				SELECT b.lines, SUM(IF(c.wrank=1,1,0)) AS wins, COUNT(c.matchid) AS ct, 
					FORMAT((SUM(IF(c.wrank=1,1,0))/COUNT(c.matchid))*100,2) AS pct 
				FROM (SELECT a.lines, COUNT(a.matchid) 
						FROM playermatch a 
						GROUP BY a.lines 
						ORDER BY a.lines DESC) b, 
					playermatch c 
				WHERE c.lines >= b.lines 
				GROUP BY b.lines 
				ORDER BY b.lines ";
			}else{
				$title = $matchtype ."P Matches Only"; 
				$query = "
				SELECT b.lines, SUM(IF(c.wrank=1,1,0)) AS wins, COUNT(c.matchid) AS ct, 
					FORMAT((SUM(IF(c.wrank=1,1,0))/COUNT(c.matchid))*100,2) AS pct 
				FROM (SELECT a.lines, COUNT(a.matchid) 
						FROM playermatch a 
						WHERE (SELECT COUNT(matchid) FROM playermatch WHERE matchid = a.matchid) = ".$matchtype."
						GROUP BY a.lines 
						ORDER BY a.lines DESC) b, 
					playermatch c 
				WHERE c.lines >= b.lines 
					AND (SELECT COUNT(matchid) FROM playermatch WHERE matchid = c.matchid) = ".$matchtype."
				GROUP BY b.lines 
				ORDER BY b.lines ";
			}
			$result = mysql_query($query, $connection) or die(mysql_error());
			?>
			<table style="margin: 0 auto;">
			<tr>
				<td class="tablehead" colspan="12"><?php echo $title; ?></td>
			</tr>
			<tr>
				<td class="colhead">Lines+</td>
				<td class="colhead">Wins</td>
				<td class="colhead">Total</td>
				<td class="colhead">Pct</td>
			</tr>
			<?php
			while ($row = mysql_fetch_object($result)){
				?>
				<tr>
					<td class="data"><?php echo $row->lines; ?></td>
					<td class="data"><?php echo $row->wins; ?></td>
					<td class="data"><?php echo $row->ct; ?></td>
					<td class="data"><?php echo $row->pct; ?></td>
				</tr>
				<?php
			}
			?>
			</table>
			<?php
		}
		?>
		<!-- close report div -->
		</div>
		<?php
include_once("templates/footer.php");
?>
</body>
</html>