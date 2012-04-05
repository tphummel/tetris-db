<?php
require_once "DB_Mysql.inc.php";


Class PlayerMatch
{
	public $matchid;
	public $playerid;
	public $lines;
	public $time;
	public $wrank;
	public $erank;
	
	public function __construct($matchid = false, $playerid = false)
	{
		if(!$matchid or !$playerid)
		{
			return;
		}
		else
		{
			$dbc = dbi_connect();
			$query = "SELECT * FROM playermatch WHERE matchid = $matchid AND playerid = $playerid";
			$result = $dbc->query($query);
			$row = $result->fetch_assoc();
			foreach($row as $attr => $val)
			{
				$this->$attr = $val;
			}
			$dbc->close();
		}
	}
	
	// inserts new record.  if record exists, it updates it.  
	public function upsert()
	{
		if(!$this->matchid or !$this->playerid)
		{
			throw new Exception("playermatch needs both matchid and playerid to call upsert()");
		}
		
		$query = "INSERT INTO playermatch(matchid, playerid, lines, time, wrank, erank) 
				  VALUES (
					$this->matchid, 
					$this->playerid, 
					$this->lines, 
					$this->time, 
					$this->wrank, 
					$this->erank)
				  ON DUPLICATE KEY UPDATE 
				  lines = $this->lines, time = $this->time, wrank = $this->wrank, 
				  erank = $this->erank";
				  
				  //echo $query . '<br><br>';
		
		if($dbc = dbi_connect())
		{
			echo 'connected';
		}
		echo $query . '<br>';;
		
		if($dbc->query($query))
		{
			echo 'insert';
		}
		
		return $dbc->affected_rows;
		// 1 = insert, 2 = update
		//$row_ct = $dbc->affected_rows;
		//$dbc->close();
		
	}
	
	public function delete()
	// physically delete record
	{
		if(!$this->matchid or !$this->playerid)
		{
			throw new Exception("playermatch needs both matchid and playerid to call delete()");
		}
		$query = "DELETE FROM playermatch
				  WHERE matchid = $this->matchid AND playerid = $this->playerid";
		$dbc = dbi_connect();
		$dbc->query($query);
		$dbc->close();
	}
	
	public function gradePerf()
	{
	$eRate = $this->lines/$this->time;
	$grade = null;
	
	if ($eRate < .6)
	{
		$grade = "F";
		
	}
	elseif ($eRate < .63)
	{
		$grade = "D-";
	}
	elseif ($eRate < .67)
	{
		$grade = "D";
	}
	elseif ($eRate < .7)
	{
		$grade = "D+";
	}
	elseif ($eRate < .73)
	{
		$grade = "C-";
	}
	elseif ($eRate < .77)
	{
		$grade = "C";
	}
	elseif ($eRate < .8)
	{
		$grade = "C+";
	}
	elseif ($eRate < .83)
	{
		$grade = "B-";
	}
	elseif ($eRate < .87)
	{
		$grade = "B";
	}
	elseif ($eRate < .9)
	{
		$grade = "B+";
	}
	elseif ($eRate < .93)
	{
		$grade = "A-";
	}
	elseif ($eRate < .97)
	{
		$grade = "A";
	}
	elseif ($eRate < 1.0)
	{
		$grade = "A+";
	}
	elseif ($eRate < 1.05)
	{
		$grade = "1S-";
	}
	elseif ($eRate < 1.15)
	{
		$grade = "1S";
	}
	elseif ($eRate < 1.2)
	{
		$grade = "1S+";
	}
	elseif ($eRate < 1.25)
	{
		$grade = "2S-";
	}
	elseif ($eRate < 1.35)
	{
		$grade = "2S";
	}
	elseif ($eRate < 1.4)
	{
		$grade = "2S+";
	}
	elseif ($eRate < 1.45)
	{
		$grade = "3S-";
	}
	elseif ($eRate < 1.55)
	{
		$grade = "3S";
	}
	elseif ($eRate < 1.6)
	{
		$grade = "3S+";
	}
	elseif ($eRate < 1.65)
	{
		$grade = "4S-";
	}
	elseif ($eRate < 1.75)
	{
		$grade = "4S";
	}
	elseif ($eRate < 1.8)
	{
		$grade = "4S+";
	}
	elseif ($eRate < 1.85)
	{
		$grade = "5S-";
	}
	elseif ($eRate < 1.95)
	{
		$grade = "5S";
	}
	elseif ($eRate < 2)
	{
		$grade = "5S+";
	}
	elseif ($eRate >= 2)
	{
		$grade = "XS";
	}
	else
	{
		$grade = "NA";
	}
	
	return $grade;
	}
	
	// get win/eff points based on rank
	// $mode W - wrank
	// $mode E - erank
	function getPts($mode, $pCount)
	{
		$pts = -9999;
		if($mode == 'E')
		{
			$rank = $this->erank;
		}
		else
		{
			$rank = $this->wrank;
		}
		
		switch ($pCount)
		{
			case 4: //4 players
					switch ($rank)
					{
						case 1:
							$pts = 4;
							break;
						case 2:
							$pts = 3;
							break;
						case 3:
							$pts = 2;
							break;
						case 4:
							$pts = 1;
							break;
					}
				break;
			
			case 3: //3 players
					switch ($rank)
					{
						case 1:
							$pts = 3;
							break;
						case 2:
							$pts = 2;
							break;
						case 3:
							$pts = 1;
							break;
					}
				break;
			
			case 2: //2 players
					switch ($rank)
					{
						case 1:
							$pts = 2;
							break;
						case 2:
							$pts = 1;
							break;
					}
				break;
		}

	return $pts;
	}
	


}

?>