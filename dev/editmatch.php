<?php
ini_set('display_errors', true);

require_once 'include/playermatch.inc.php';
require_once 'include/match.inc.php';
require_once 'include/location.inc.php';
require_once 'include/player.inc.php';
require_once 'include/DB_Mysql.inc.php';

if($_GET["test"])
{
	$match  			= new TntMatch();
	$match->matchdate 	= date('Y-m-d');
	$match->inputstamp 	= date('Y-m-d H:i:s');
	$match->enteredby 	= 4; //tom
	$match->location 	= 1;
	$match->note 		= "noty mcnote!";
	$match->universe 	= 1; //default original universe
	
	$pm 			= new PlayerMatch();
	$pm->playerid 	= 1;
	$pm->lines 		= 83;
	$pm->time		= 110;
	$pm->wrank		= null;
	array_push($match->playermatches, $pm);
	
	$pm 			= new PlayerMatch();
	$pm->playerid 	= 4;
	$pm->lines 		= 85;
	$pm->time		= 110;
	$pm->wrank		= 1;
	array_push($match->playermatches, $pm);
	
	$pm 			= new PlayerMatch();
	$pm->playerid 	= 2;
	$pm->lines 		= 120;
	$pm->time		= 109;
	$pm->wrank		= null;
	array_push($match->playermatches, $pm);
	
	$pm 			= new PlayerMatch();
	$pm->playerid 	= 3;
	$pm->lines 		= 120;
	$pm->time		= 109;
	$pm->wrank		= null;
	array_push($match->playermatches, $pm);
	
	$match->crunchWinRanks();
	
	$match->crunchEffRanks();
	
	foreach($match->playermatches as $pm)
	{
	?>
	<table>
	<tr>
		<td>playerid</td><td><?php echo $pm->playerid; ?></td>
	</tr>
	<tr>
		<td>lines</td><td><?php echo $pm->lines; ?></td>
	</tr>
	<tr>
		<td>time</td><td><?php echo $pm->time; ?></td>
	</tr>
	<tr>
		<td>wrank</td><td><?php echo $pm->wrank; ?></td>
	</tr>
	<tr>
		<td>erank</td><td><?php echo $pm->erank; ?></td>
	</tr>
	<tr>
		<td>lps</td><td><?php echo $pm->lps; ?></td>
	</tr>
	</table><hr>
	<?php
	}
	$match->insert();
	exit;
}


if($_POST["submitNew"])
{
	$match = new TntMatch();
	$match->matchdate = date('Y-m-d');
	$match->inputstamp = date('Y-m-d H:i:s');
	$match->enteredby = 4; //tom
	$match->location = $_POST["location"];
	$match->note = $_POST["note"];
	$match->universe = 1; //default original universe
	
	for($i=1; $i<=4; $i++)
	{
		$post_player 	= $_POST["player" . $i];
		$pm 			= new PlayerMatch();
		$pm->playerid 	= trim($post_player[0]);
		$pm->lines 		= trim($post_player[1]);
		$pm->time		= trim($post_player[2])*60+trim($post_player[3]);
		$pm->wrank		= (trim($post_player[4]) == "on" ? 1 : 0);
		
		array_push($match->playermatches, $pm);
	}
	
	// this might work best as Match object behavior.  Absolutely.  Checking itself is perfect behavior for an object.  
	$match->validate($match->playermatches);
	
	$match->crunchWinRanks();
	$match->crunchEffRanks();
	
}

function validate($pms)
{
//validate POST data
	$errorStatus = false;
	$errorMsg = "";
	$errorRegion = -1;
	
	//error checks, if any fails $errorStatus changed 
	//error regions 1-16 are individual controls.  17-20 are entire rows of controls.  21-24 are entire columns of controls.  
	
	//two times must match, top two times must match - find highest time, see if another matches it
	
	
	$winnerIndex = 0;
	$winnerCt = 0;
	foreach($pms as $pm)
	{
		if($pm->wrank == 1)
		{
			$winnerCt++;
		}
	}
	//1 winner only
	//problem when the winner leaves time blank.  It gets the error for wrong number of winners even if only 1 is selected.  
	if($winnerCt != 1)
	{
		$errorStatus = TRUE;
		$errorRegion = 20;
		$errorMsg = "There can only be one Winner";
	}
	
	//no name duplicates
	$start = 1;
	for($i=0;$i<4;$i++)
	{
		for($j=$start; $j<4; $j++)
		{
			if($ogPlayers[$i]==$ogPlayers[$j] and !empty($ogPlayers[$j]))
			{
				$errorStatus = TRUE;
				//highlights the repeated(2nd) name field
				$errorRegion = 1 + (4*$j);
				//$errorRegion = 17; //this gets whole row
				$errorMsg = "A player can only appear once in a match";
			}
		}
		$start++;
	}
	
	//lines numeric, integer and >= 0
	for($i=0; $i<3; $i++)
	{
		if(isset($players[$i]))
		{
			$p = $players[$i];
			
			//LINES
			if(is_numeric($p[1]))
			{
				$lines = intval($p[1]);
			}
			else
			{
				$errorStatus = TRUE;
				$errorRegion = 2 + (4*$i);
				$errorMsg = "Lines value must be an integer";
			}
			
			if($lines < 0)
			{
				$errorStatus = TRUE;
				$errorRegion = 2 + (4*$i);
				$errorMsg = "Lines value must be greater than zero";
			}
			
			//MIN
			if(is_numeric($p[2]))
			{
				$min = intval($p[2]);
			}
			else
			{
				$errorStatus = TRUE;
				$errorRegion = 3 + (4*$i);
				$errorMsg = "Minutes value must be an integer";
			}
			
			if($min < 0)
			{
				$errorStatus = TRUE;
				$errorRegion = 3 + (4*$i);
				$errorMsg = "Minutes value must be greater than zero";
			}
			
			//SEC
			if(is_numeric($p[3]))
			{
				$sec = intval($p[3]);
			}
			else
			{
				$errorStatus = TRUE;
				$errorRegion = 3 + (4*$i);
				$errorMsg = "Seconds value must be an integer";
			}
			
			if(isset($sec) and $sec < 0)
			{
				$errorStatus = TRUE;
				$errorRegion = 3 + (4*$i);
				$errorMsg = "Seconds value must be greater than zero";
			}
			
			//time greater than 0
			if($p[4] < 1)
			{
				$errorStatus = TRUE;
				$errorRegion = 3 + (4*$i);
				$errorMsg = "Time value must be greater than zero";
			}
		}
	}
	
	//check that winner was in the match longest
	$max = -1;
	for($k=0; $k<4; $k++)
	{
		$p = $players[$k];
		if($p[4] >= $max)
		{
			$max = $p[4];
		}
	}
	
	$firstIsMaxTime = FALSE;
	for($k=0; $k<4; $k++)
	{
		$p = $players[$k];
		if($p[5] == "on" AND $p[4] == $max)
		{
			$firstIsMaxTime = TRUE;
		}
	}
	
	if(!$firstIsMaxTime AND $errorStatus != FALSE)
	{
		$errorStatus = TRUE;
		$errorRegion = 21 + $winnerIndex;
		$errorMsg = "Winner must have played the entire match";
	}
	
	//reshow form with highlights if error is caught
	if($errorStatus)
	{
		showConsole($players, $connection, "", $errorMsg, $errorRegion, $location, $note);
		exit();
	}
}

?>