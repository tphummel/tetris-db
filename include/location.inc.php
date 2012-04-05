<?php
require_once 'DB_Mysql.inc.php';

/*
location.inc.php
location code-behind file
supports location form and Location objects
*/

class Location
{
	
	public $locationid;
	public $locationname;
	public $address;
	public $city;
	public $state;
	public $zip;
	public $createdby;
	public $locationdescription;
	public $createdate;
	public $image;
	
	public function __construct($locationid = false, $createdby = false)
	{
		$dbc = dbi_connect();
		// either empty return empty object
		if(!$locationid or !$createdby)
		{
			return;
		}
		else
		{
			$query = "SELECT * FROM location WHERE locationid = $shlogeeid";
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
		if(!$this->locationid)
		{
			throw new Exception("location needs locationid to call update()");
		}
		$query = "UPDATE location
				  SET locationname = '$this->locationname', address = '$this->address', city = '$this->city',
				  state = '$this->state', zip = '$this->zip', createdby = $this->createdby, 
				  locationdescription = '$this->locationdescription', createddate = '$this->createddate',
				  image = '$this->image'
				  WHERE locationid = $this->locationid";
		$dbc = dbi_connect();
		$dbc->query($query);
		$dbc->close();
	}
	
	public function insert()
	{
		if($this->locationid)
		{
			throw new Exception("location object has a locationid, can't insert");
		}
		$query = "INSERT INTO location
					(locationname, address, city, state, zip, createdby, locationdescription, createddate, image)
				  VALUES ('$this->locationname', '$this->address', $this->city, '$this->state', '$this->zip', '$this->createdby', 
					'$this->locationdescription', $this->createdby, '$this->createddate', '$this->image')";
		$dbc = dbi_connect();
		$dbc->query($query);
		
		// get assigned user id and put in object
		$this->locationid = $dbc->insert_id;
		$dbc->close();
	}
	
	public function delete()
	{
		if(!$this->locationid)
		{
			throw new Exception("location object has no locationid -- can't delete");
		}
		$query = "DELETE FROM location
				  WHERE locationid = $this->locationid";
		$dbc = dbi_connect();
		$dbc->query($query);
		$dbc->close();
	}

}
?>