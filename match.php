<?php

//left navbar / banner

$title = "The New Tetris - Match Console";

require_once("templates/header.php");

require_once("config/db.php");
require_once("lib/grade.php");
require_once("lib/points.inc.php");
require_once("lib/rankings.inc.php");
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
		
/*
collect post data if it exists
assemble two arrays:
-players array of match data
-ogPlayers array which contains usernames in the original order
*/
	require_once dirname ( __FILE__ )  . "/lib/helper.php" ;

	if(array_key_exists('player1',$_POST)){
		unset($players);
		$players = Helper::cleanPlayers ( $_POST ) ;

		unset ( $ogPlayers ) ;
		$ogPlayers = array ( ) ;
		foreach ( $players as $player ) {
			$ogPlayers[] = $player [ 0 ] ;
		}

		$location = $_POST["location"];
		$note = $_POST["note"];
			
		/*
		0 - user
		1 - lines
		2 - min
		3 - sec
		4 - time
		5 - winner/wrank
		6 - lps
		7- erank
		*/
			
	}
$confirmStr = '';

if(array_key_exists('action', $_GET)){

switch ($_GET['action']) {
	case "add" :
		require_once dirname ( __FILE__ )  . "/lib/rules.php" ;

		$valid = Rules::validateMatch ( $players ) ;
		
		//reshow form with highlights if error is caught
		if( $valid [ "isValid" ] == false ) {
			$errorRegion = true ;
			showConsole ( $players, $connection, "", $valid [ "errMsg" ], $errorRegion, $location, $note ) ;
			exit();
		}
		
		//lib/rankings.inc.php
		$wrankedPlayers = getWinRanks ( $players ) ;
		$erankedPlayers = getEffRanks ( $wrankedPlayers ) ;
		
		//Create TNTMatch Record
		
		$nowdate = date ( "Y-m-d" ) ;
		$nowstamp = date ( "Y-m-d H:i:s" ) ;

    $insertTM = "
    	INSERT INTO tntmatch VALUES 
    	(NULL, '" . $nowdate . "', '" . $nowstamp . "', 4, 
				(SELECT locationid from location where locationname = '" . $location . "'), 
			'" . $note . "', 1)" ;
		
		mysql_query ( $insertTM, $connection ) or die ( mysql_error() ) ;
		
		//Create PlayerMatch Records
		$current = mysql_insert_id ( ) ; 
		
		$insertPM = "INSERT INTO playermatch VALUES ";
		foreach ( $erankedPlayers as $player ) {
			$insertPM = $insertPM . "(" . $current . ", (SELECT playerid from player where username = '" . $player[0] . "')," . 
                        $player[1] . ", " . $player[4] . ", " . $player[5] . ", " . $player[7] . "), "; 
		
		}
		$insertPM_trimmed = rtrim($insertPM, ", ");

		$confirmStr = "Match #" . $current . "<br>" . $nowstamp;
        
		
		mysql_query($insertPM_trimmed, $connection) or die(mysql_error());
	break;
} // END SWITCHCASE ADD
} // END check array_key_exists('action',$_GET)

//this happens on every load
//clear post data for start of new match
		$users = array();
		$temp = array();

		if(isset($ogPlayers))
		{
			foreach ($ogPlayers as $ogp)
			{
				foreach ($players as $p)
				{
					if($ogp == $p[0])
					{
						$users[] = $p;
					}
				}
			}
		}
		else
		{
			for($i=0; $i<4; $i++)
			{
				$user = array("", "", "", "", "", "");
				$users[$i] = $user;
			}
		}
		
		for ($q = 1; $q <= 4; $q++)
		{
			unset($_POST["player" . $q]);
		} 
		
		showConsole($users, $connection, $confirmStr, "", "", "", "");
		
		
/*
==========================================================================================
==========================================================================================
*/
function showConsole ( $users, $connection, $confirmStr, $errorMsg, $errorRegion, $location, $note) {

	$errorLocStr =  ' class="errorLocation"';
	if(!empty($errorMsg) AND !empty($errorRegion))
	{
		?>
		<div class="errortext">
			<?php 
				echo $errorMsg; 
			?>
		</div>
<?php
	}
?>
		<form action="match.php?action=add" method="post" name="match">
		<table border="1" cellpadding="5" cellspacing="10">
		
		<!--THIS MATCH - CREATES FOUR PLAYER BOXES-->
		<tr>
			<td valign="top">
				<table border="0">
					<tr>
						<th colspan="2">This Match</th>
					</tr>
					<tr>
						<td colspan="1">Location:</td>
						<td>
							<select name="location">
							<?php 
							// get location list
							$sel = " selected";
							$queryLoc = "SELECT locationname FROM location";
							$resultLoc = mysql_query($queryLoc, $connection) or die(mysql_error());
							while ($val = mysql_fetch_array($resultLoc))
							{
								$name = $val["locationname"];
								echo('<option value="' . $name .'"');
								if(!empty($location))
								{
									//if not first match of session, use location of last match.
									if ($name == $location)
									{
										
										echo $sel;
									}
								}
								else
								{
									//if first match of session, get last used location.
									$queryLastLocation = "SELECT l.locationname FROM location l, tntmatch tm where tm.location = l.locationid and tm.matchid = 	 (SELECT max(matchid) from tntmatch)";
									$lastLocResult = mysql_query($queryLastLocation, $connection) or die(mysql_error());
									$lastLocArr = mysql_fetch_array($lastLocResult);
									$lastLoc = $lastLocArr[0];
									if($name == $lastLoc)
									{
										echo $sel;
									}
								}
									
								echo ('>' . $name . '</option>');
							}
							?>
							</select></td></tr>
					<tr>
						<td>Note:</td>
						
						<td colspan="4"><textarea rows="2" cols="10" name="note" value="<?php echo $note; ?>"></textarea></td>
					</tr>
				</table>
			</td>
		<?php 
		$selected = " selected";
		
		$query = "SELECT username from player";
		$result = mysql_query($query, $connection) or die(mysql_error());
		//query DB once for name list and then put in array
		while ($row = mysql_fetch_array($result))
		{
			$names[] = $row['username'];
		}
		//var_dump($names);
		for ($i = 0; $i <= 3; $i++) //do 4 times, one for each player
		{
		?>
			<td><table>
			<tr><td>Username:</td>
			<td>
			<!-- USERNAME DROP DOWN LIST -->
			<select name="player<?php echo $i+1; ?>[]"
			<?php
			if($errorRegion == 1 + (4*$i) or $errorRegion == 17 or $errorRegion == 21 + $i)
			{
				echo $errorLocStr;
			}
			?>
			> 
			<option value="VACANT">VACANT</option>
				<?php 
				$query = "SELECT username from player";
				$result = mysql_query($query, $connection) or die(mysql_error());
				foreach ($names as $name)
				{
				?>
					<option value="<?php echo $name; ?>"
					<?php
					if(array_key_exists($i, $users)){
						if ($users[$i][0] == $name)
						{
							echo $selected;
						}
					}
					?>
					><?php echo $name; ?></option>
				<?php
				}
				?>
				</select></td></tr>
				<tr><td>Lines:</td><td><input type="text" size="4" maxlength="4" name="player<?php echo $i+1; ?>[]" value="
				<?php 
					//only show last round's values if there is an error and a value exists
					if(!empty($users[$i][1]) AND !empty($errorMsg))
					{
						echo $users[$i][1];
					}
					else
					{
						echo "";
					}
				?>"
				<?php
					if($errorRegion == 2 + (4*$i) or $errorRegion == 18 or $errorRegion == 21 + $i)
					{
						echo $errorLocStr;
					}
				?>
				></td></tr>
				<tr><td>Minutes:</td><td><input type="text" size="4" maxlength="4" name="player<?php echo $i+1; ?>[]" value="
				<?php 
					if(!empty($users[$i][2]) AND !empty($errorMsg))
					{
						echo $users[$i][2];
					}
					else
					{
						echo "";
					}
				?>"
				<?php
					if($errorRegion == 3 + (4*$i) or $errorRegion == 19 or $errorRegion == 21 + $i or ($errorRegion == 25 and $i < 2) )
					{
						echo $errorLocStr;
					}
				?>
				></td></tr>
				<tr><td>Seconds:</td><td><input type="text" size="4" maxlength="4" name="player<?php echo $i+1; ?>[]" value="
				<?php 
					if(!empty($users[$i][3]) AND !empty($errorMsg))
					{
						echo $users[$i][3];
					}
					else
					{
						echo "";
					}
				?>" 
				<?php
					if($errorRegion == 3 + (4*$i) or $errorRegion == 19 or $errorRegion == 21 + $i or ($errorRegion == 25 and $i < 2))
					{
						echo $errorLocStr;
					}
				?>
				></td></tr>
				<tr><td>Winner:</td><td
				<?php
					if($errorRegion == 4 + (4*$i) or $errorRegion == 20 or $errorRegion == 21 + $i)
					{
						echo $errorLocStr;
					}
				?>
				><input type="checkbox" name="player<?php echo $i+1; ?>[]"
				<?php
					if(array_key_exists($i, $users)){
						if($users[$i][5] == "on" AND !empty($errorMsg))
						{
							echo " checked";
						}
					}
				?>
				></td></tr>
				</table>
				</td>
		<?php
		} //end "this match" for loop 
		
/*
==========================================================================================
==========================================================================================
*/
		?>
		</tr>
		<!--LAST MATCH -- RUNS 4 TIMES-->
		<tr>
			<td align="center">
				<table border="0">
					<tr>
						<th>Last Match</th>
					</tr>
					<tr>
						<td align="center">
						<?php 
							if(!empty($confirmStr))
							{
								echo $confirmStr; 
							}
						?></td>
					</tr>
					<tr>
						<th>
							<?php
								//show edit button only if there is a last match
								// 12/10/10 - doesn't hide button
								if(!empty($users))
								{
								?>
									<form>
										<input type="submit" value="Edit" disabled>
									</form>
								<?php
								}
							?>
						</th>
					</tr>
				</table>
			</td>
			
		<?php
		for ($j = 0; $j <= 3; $j++) //do 4 times, one for each player
		{
		?>
		<td valign="middle">
		<?php
		// LAST MATCH
		if (isset($users[$j][0]) && $users[$j][0] != "VACANT")
		{
			// hideous finding last match by max(matchid)
			$query = "select pm.lines, pm.time, pm.wrank, pm.erank, 
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt
			FROM playermatch pm
			WHERE playerid = (SELECT playerid FROM player WHERE username = '" . $users[$j][0] . "')
			AND matchid = (SELECT MAX(matchid) FROM tntmatch)";
			$result = mysql_query($query, $connection) or die(mysql_error());
			$data = mysql_fetch_array($result);

			$pCount = $data["pCt"]; //get player count to use with rankToPts udf.
			
			$wrank = $data["wrank"];
			$wpts = rankToPts($wrank, $pCount);
			
			$erank = $data["erank"];
			$epts = rankToPts($erank, $pCount); //udf converts rank to points for power ranking
			
			$lines = $data["lines"];
			$time = $data["time"];
			$min = intval($time/60);
			$sec = str_pad($time - $min*60,2,"0", STR_PAD_LEFT);
			if($time != 0){
				$eff = $lines/$time;
			}else{
				$eff = 0;
			}
			
			$timeStr = $min . ":" . $sec;
			$effStr = number_format($eff,3);
			$grade = gradePerf($time, $lines);
			
			//lib/statPower.php
			$power = computePower($wpts, $epts, $eff);
			// already formatting value in computePower() - don't format again
			//$pwrstr = number_format($power, 3);
			$pwrstr = $power;
			echo '<table align="center"';
			echo '>';
			echo '
			<tr><td>W-Rank:</td><td>' . $wrank . '</td></tr>
			<tr><td>E-Rank:</td><td>' . $erank . '</td></tr>
			<tr><td>Lines:</td><td>' . $lines . '</td></tr>
			<tr><td>Time:</td><td>' . $timeStr . '</td></tr>
			<tr><td>LPS:</td><td>' . $effStr . '</td></tr>
			<tr><td>POWER:</td><td>' . $pwrstr . '</td></tr>
			<tr><td>Grade: </td><td><h2>' . $grade . '</h2></td></tr>
			</table>
			</td>';
		} //end if
		
	} //end "last match" for loop
	
/*
==========================================================================================
==========================================================================================
*/
		$today = date("Y-m-d");

		$now = date("Y-m-d H:i:s");
		$oneDayAgo = date("Y-m-d H:i:s", strtotime($now) - 60 * 60 * 24) ;
		?>
		</tr>
		
		<!--DAY SUMMARY -- RUNS 4 TIMES-->
		<tr>
		<td align="center">
			<table border="0">
				<tr>
					<th>Last 24 Hrs</th>
				</tr>
				<tr>
					<td>
						<?php
								//show edit button only if there is day match data
								if(!empty($users))
								{
								?>
									<form>
										<input type="submit" value="Edit" disabled>
									</form>
								<?php
								}
						?>
					</td>
				</tr>
			</table>
		</td>

		<?php
		for ($k = 0; $k <= 3; $k++) //do 4 times, one for each player
		{
			echo '<td valign="middle">';
		?>
		
		<?php 
		//query for day sum

		if (isset($users[$k][0]) && $users[$k][0] != "VACANT")
		{
			$query = 
			"SELECT count(today.mid) as totgames, sum(today.time) as tottime, sum(today.score) as totlines
			FROM (SELECT m.matchid as mid, p.username as name, pm.lines as score, pm.time as time
					FROM playermatch pm, tntmatch m, player p
					WHERE m.matchid = pm.matchid and pm.playerid = p.playerid and m.inputstamp >= '" . $oneDayAgo . "') today
			WHERE today.name = '" . $users[$k][0] . "'";
			
			$result = mysql_query($query, $connection) or die(mysql_error());
			$data = mysql_fetch_array($result);
			
			$totgames = $data["totgames"];
			$tottime = $data["tottime"];
			
			$totmin = intval($tottime/60);
			$totsec = str_pad($tottime - $totmin*60,2,"0", STR_PAD_LEFT);
			
			$timestr = $totmin . ":" . $totsec;
			$totlines = $data["totlines"];
			$dayLpsVal = ($tottime > 0 ? $totlines / $tottime : 0);
			$dayLps = number_format($dayLpsVal,3);
			
			$dayLpgVal = ($tottime > 0 ? $totlines / $totgames : 0);
			$dayLpg = number_format($dayLpgVal,2);
			$query = "select pm.lines, pm.time, pm.wrank, pm.erank, 
(select count(playerid) from playermatch where matchid = pm.matchid) as pCt
from playermatch pm, tntmatch m, player p
where pm.matchid = m.matchid 
	and p.playerid = pm.playerid 
	and p.username = '" . $users[$k][0] . "' 
	and m.matchdate = '" . $today . "'";
	
			// old query using view
			/*$query = "SELECT pm.lines, pm.time, pm.wrank as wrank, pm.erank as erank, pm.pCt as pCt
			FROM	pmext pm, tmext m, player p 
			WHERE   pm.matchid = m.matchid and p.playerid = pm.playerid and p.username = '" . $users[$k][0] . "' and m.matchdate = '" . $today . "'"; */
			
			$resultDaySum = mysql_query($query, $connection) or die(mysql_error());
			
			//this should be sproc eventually - or an effen aggregate query???
			$epts = 0;
			$wpts = 0;
			// why dont just sum in db query? looks like i'm getting win pts here, only reason.
			while ($rec = mysql_fetch_array($resultDaySum))
	    	{
				$pCount = $rec["pCt"];
				$win = $rec["wrank"]; 
				$pts1 = rankToPts($win, $pCount); //udf for getting pts
				$wpts = $wpts + $pts1; //sum day's wpts
				
				$eff = $rec["erank"]; 
				$pts2 = rankToPts($eff, $pCount);
				$epts = $epts + $pts2; //sum day's epts
			}
			
			//power calculation
			$eptsg = number_format(($totgames > 0 ? $epts/$totgames : 0),3);
			$wptsg = number_format(($totgames > 0 ? $wpts/$totgames : 0),3);
			
			//lib/statPower.php
			$dayPower = computePower($wptsg, $eptsg, $dayLps);
			
			?>
			<table align="center">
			<tr><td>G(Time)</td><td><?php echo $totgames . "(" . $timestr . ")"; ?></td></tr>
			<tr><td>LPS</td><td><?php echo $dayLps; ?></td></tr>
			<tr><td>LPG</td><td><?php echo $dayLpg; ?></td></tr>
			<tr><td>W-PTS/G</td><td><?php echo $wptsg; ?></td></tr>
			<tr><td>E-PTS/G</td><td><?php echo $eptsg; ?></td></tr>
			<tr><td>POWER</td><td><?php echo $dayPower; ?></td></tr>
			</table>
			<?php 
		} //end if
	} //end "day sum" for loop
	
		?>
		</td>
		
		</tr>
		
		<tr><td align="center" colspan="5"><input type="submit" value="Submit"></td></tr>
		</table>
		</form>

			
	<?php
	include_once("templates/footer.php");
	?>
	</body>
	</html>
<?php
}//close showConsole function
?>