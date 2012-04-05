<?php
require_once "DB_Mysql.inc.php";
require_once "playermatch.inc.php";

Class TntMatch
{
	public $matchid;
	public $matchdate;
	public $inputstamp;
	public $enteredby;
	public $location;
	public $note;
	public $universe;
	public $playermatches = array();

	public function __construct($matchid = false, $userid = false)
	{
		if(!$matchid)
		{
			return;
		}
		else
		{
			// connect to db 
			$dbc = dbi_connect();
			// Get match Attributes
			$query = "SELECT * FROM tntmatch WHERE matchid = $matchid";
			$result = $dbc->query($query);
			$row = $result->fetch_assoc();
			foreach($row as $attr => $val)
			{
				$this->$attr = $val;
			}
			
			// Get ShlogShlogees
			$query = "SELECT playerid FROM playermatch WHERE matchid = $matchid";
			$result = $dbc->query($query);
			while($row = $result->fetch_object())
			{
				$pm = new PlayerMatch($matchid, $row->playerid);
				array_push($this->playermatches, $pm);
			}
			$dbc->close();
		}
	}
	
	public function update()
	{
		if(!$this->matchid)
		{
			throw new Exception("match needs matchid to call update()");
		}
		$query = "UPDATE tntmatch
				  SET location = $this->location, 
					  matchdate = '$this->matchdate', 
					  inputstamp = '$this->inputstamp',
					  enteredby = $this->enteredby, 
					  note = '$this->note',
					  universe = $this->universe
				  WHERE matchid = $this->matchid";
				  //echo $query; exit;
		$dbc = dbi_connect();
		$dbc->query($query);
		
		$this->_savePlayerMatches();
	}
	
	public function insert()
	{
		if($this->matchid)
		{
			throw new Exception("match object has a matchid, can't insert");
		}
		$query = "INSERT INTO tntmatch
					(matchdate, 
					inputstamp, 
					enteredby, 
					location, 
					note, 
					universe)
				  VALUES (
				  '$this->matchdate', 
				  '$this->inputstamp', 
				  $this->enteredby, 
				  $this->location, 
				  '$this->note', 
				  $this->universe)";
				  //echo $query;
				 
		$dbc = dbi_connect();
		$dbc->query($query);
		
		// get assigned user id and put in object
		$this->matchid = $dbc->insert_id;
		$dbc->close();
		
		$this->_savePlayerMatches();
	}
	
	public function delete()
	{
		if(!$this->matchid)
		{
			throw new Exception("match object needs a matchid to delete");
		}
		$query = "DELETE FROM tntmatch
				  WHERE matchid = $this->matchid";
		$dbc = dbi_connect();
		$dbc->query($query);
		
		foreach($this->playermatches as $pm)
		{
			$pm->delete();
		}
	}
	private function _savePlayerMatches()
	{
		
		$selPM = "select * from playermatch where matchid = $this->matchid";
		$dbc = dbi_connect();
		$pmRes = $dbc->query($selPM);
		$dbc->close();
		$dbplayermatches = array();
		
		// get objects for all pm in db.  put in array
		while($row = $pmRes->fetch_assoc())
		{
			$pm = new PlayerMatch();
			foreach($row as $attr => $val)
			{
				$pm->$attr = $val;
			}
			array_push($dbplayermatches, $pm);
		}
		
		$objPMids = array();
		// put all playermatchids that are currently in object into an array  
		foreach($this->playermatches as $pm)
		{
			// Add matchid to playermatch
			$pm->matchid = $this->matchid;
			array_push($objPMids, $pm->playerid);
		}
		//print_r($objPMids);exit;
		
		// CLEAN UP shlogshlogees in DB for this Shlog
		// iterate through shlog shlogees with this shlog that are currently in database
		foreach($dbplayermatches as $dbpm)
		{
			// Check each db playermatch if its still in object
			if(!in_array($dbpm->playerid, $objPMids))
			{
				// IF not in object, delete
				$dbpm->delete();
			}
		}
		
		// Database is now in sync with object 
		foreach($this->playermatches as $pm)
		{
			// Save
			 $pm->upsert();
		}
	}
	
	public function crunchWinRanks()
	{
		//generating wrank
		$first = null; //has one and only one player
		$second = array(); //has 1 for sure but can have up to 3
		$third = array(); //can have between 0 and 2
		$fourth = null; //can have between 0 and 1
		
		$players = $this->playermatches;
		
		if (count($players) == 2)
		{	//2p match - airtight
			foreach ($players as $player)
				{
					if ($player->wrank == 1)
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
		{	//3p match
			//find winner
			$counter = 0;
			foreach ($players as $player)
			{
				if ($player->wrank == 1)
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
				if ($player->time == $first->time) //same time as winner?
				{
					$second[] = $player;
				}
				else
				{
					$third[] = $player;
				}
			}				
		}
		
		elseif (count($players) == 4)
		{	//4p match
				//find winner
			$counter1 = 0;
			foreach ($players as $player)
			{
					if ($player->wrank == 1)
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
					if ($player->time == $first->time)  //same time?
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
						
						if($one->time == $two->time)
						{
							//tie for third
							$third[] = $one;
							$third[] = $two;
						}
						elseif($one->time > $two->time)
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
		foreach($this->playermatches as $pm)
		{
			if($pm->playerid == $first->playerid)
			{
				$pm->wrank = 1;
			}
			foreach($second as $p2)
			{
				if($pm->playerid == $p2->playerid)
				{
					$pm->wrank = 2;
				}
			}
			foreach($third as $p3)
			{
				if($pm->playerid == $p3->playerid)
				{
					$pm->wrank = 3;
				}
			}
			if($pm->playerid == $fourth->playerid)
			{
				$pm->wrank = 4;
			}
		}
	}

	public function crunchEffRanks()
	{
		//generate erank
			
			$lpsArr = array(); //array of player lps values
			$finishedPlayers = array();
			$players = $this->playermatches;
			foreach ($players as $pm)
			{
				$pm->lps = $pm->lines/$pm->time;
				$lpsArr[] = $pm->lps;
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
	
			//remove all lps' that match max, may be >1 in event of tie.
			for($i = 0; $i <= count($lpsArr); $i++)
			{
				if($lpsArr[$i] == $e1)
				{
					unset($lpsArr[$i]);				
				}
			}
			
			//if player's lps matches max, put in first place array
			foreach($players as $pm)
			{
				if($e1 == $pm->lps)
				{
					$erank1[] = $pm;
				}
			}
			

##################
# 2222222222222222
			//get next highest lps after max
			
			$lpsArr = array_merge($lpsArr);

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
			foreach($players as $p)
			{
				if($e2 == $p->lps)
				{
					$erank2[] = $p;
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
			foreach($players as $p)
			{
				if($e3 == $p->lps)
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
			foreach($players as $p)
			{
				if($e4 == $p->lps)
				{
					$erank4[] = $p;
				}
			}
		}
	
	
############################
	
		//assign eranks
		foreach($this->playermatches as $pm)
		{
			foreach($erank1 as $e1)
			{
				if($pm->lps == $e1->lps)
				{
					$pm->erank = 1;					
				}
			}
				
			if(count($erank2) > 0)
			{
				foreach($erank2 as $e2)
				{
					if($pm->lps == $e2->lps)
					{
						switch(count($erank1))
						{
							case 1:
								// if 1 person alone in first - second place
								$pm->erank = 2;
								
								
							break;
							
							case 2:
								// if 2 people tied to first - third place
								$pm->erank = 3;
							break;
							
							case 3:
								//if 3 people tied for first - fourth place
								$pm->erank = 4;
						}			
					}
				}
			}
			
			if(count($erank3) > 0)
			{
				foreach($erank3 as $e3)
				{
					if($pm->lps == $e3->lps)
					{
						switch(count($erank1) + count($erank2))
						{
							case 2:
								//two people in first two places - third place
								$pm->erank = 3;
														
							break;
							
							case 3:
								//three people in first two places - fourth place
								$pm->erank = 4;
							break;
						}
					}
				}
			}
	
			//fourth place - one player max, always fourth
			if(count($erank4) > 0)
			{
				foreach($erank4 as $e4)
				{
					if($pm->lps == $e4->lps)
					{
						$pm->erank = 4;
					}
					
				}
			}
		}
		
		
	}
	
	
	
	
	
	
	
}

?>