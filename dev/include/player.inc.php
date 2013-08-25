<?php
require_once 'DB_Mysql.inc.php';

/*
location.inc.php
location code-behind file
supports location form and Location objects
*/

class Player
{
	
	public $playerid;
	public $firstname;
	public $lastname;
	public $createdate;
	public $image;
	public $birthdate;
	
	public function __construct($playerid = false)
	{
		$dbc = dbi_connect();
		// either empty return empty object
		if(!$playerid)
		{
			return;
		}
		else
		{
			$query = "SELECT * FROM player WHERE playerid = $playerid";
			$result = $dbc->query($query);
			$row = $result->fetch_assoc();
			foreach($row as $attr => $val)
			{
				$this->$attr = $val;
			}
		}
		$dbc->close();
	}
	public function update()
	{
		if(!$this->playerid)
		{
			throw new Exception("player needs playerid to call update()");
		}
		$query = "UPDATE player
				  SET firstname = '$this->firstname', lastname = '$this->lastname', createdate = '$this->createdate',
				  image = '$this->image', birthdate = '$this->birthdate'
				  WHERE playerid = $this->playerid";
		$dbc = dbi_connect();
		$dbc->query($query);
		$dbc->close();
	}
	
	public function insert()
	{
		if($this->playerid)
		{
			throw new Exception("player object has a playerid, can't insert");
		}
		$query = "INSERT INTO player
					(firstname, lastname, createdate, image, birthdate)
				  VALUES ('$this->firstname', '$this->lastname', '$this->createdate', '$this->image', '$this->birthdate')";
		$dbc = dbi_connect();
		$dbc->query($query);
		
		// get assigned user id and put in object
		$this->playerid = $dbc->insert_id;
		$dbc->close();
	}
	
	public function delete()
	{
		if(!$this->playerid)
		{
			throw new Exception("player object has no playerid -- can't delete");
		}
		$query = "DELETE FROM player
				  WHERE playerid = $this->playerid";
		$dbc = dbi_connect();
		$dbc->query($query);
		$dbc->close();
	}
	
}

?>