<?php 
/*
manageplayer.php
provides admin functionality for adding/editing player accounts.  
makes use of the jpgraph library for generating dynamic images
*/
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>
<body>
<?php
include_once("header.php");
?>

<?php
include_once('db_login.php');
//logs onto and chooses TNT DB
    
$connection = mysql_connect($db_host, $db_username, $db_password);
if(!$connection)
{
    die ("Could not connect to the database: <br />". mysql_error());
}
$db_select = mysql_select_db($db_database,$connection);
if (!$db_select)
{
    die ("Could not select the database: <br />". mysql_error());
}
    
switch ($_GET['action'])
{
    case "view":
	
        $query = "SELECT * FROM player";
    	$result = mysql_query($query, $connection) or die(mysql_error());
    	echo '<table border="1">';
    	echo '<tr><th>ID</th><th>First</th><th>Last</th>
    	<th>Login</th><th>Pass</th><th>Created</th></tr>';
    
    	while ($row = mysql_fetch_array($result))
    	{
        	$playerid = $row["playerid"];
        	$firstname = $row["firstname"];
        	$lastname = $row["lastname"];
        	$username = $row["username"];
        	$password = $row["password"];
        	$createdate = $row["createdate"];
        	echo ('
        	<tr>
         	<td>'.$playerid.'</td>
         	<td>'.$firstname.'</td>
         	<td>'.$lastname.'</td>
         	<td>'.$username.'</td>
         	<td>'.$password.'</td>
         	<td>'.$createdate.'</td>
        	</tr>
        	');
   		 }
			echo "</table>";
    	
    break;
		
	case "fill":
	?>
		<form action="manageplayer.php?action=add" method="post">
		<table>
		<tr><th>Username:</th><td><input type="text" name="user"></td></tr>
		<tr><th>Password:</th><td><input type="password" name="pass"></td></td></tr>
		<tr><th>Confirm:</th><td><input type="password" name="confirm"></td></td></tr>
		<tr><th>Firstname:</th><td><input type="text" name="first"></td></tr>
		<tr><th>Lastname:</th><td><input type="text" name="last"></td></tr>
		<tr><td>&nbsp;</td><td align="right"><input type="button" value="Reset"><input type="submit" value="Submit"></td></tr>		
		</table>
		</form>
		
		<?php
		break;
	
	case "add":
		$fName = $_POST["first"];
		$lName = $_POST["last"];
		$uName = $_POST["user"];
		$uPass = $_POST["pass"];
		$cDate = $_POST["cdate"];
		
		$today = date("Y-m-d");
		
    	$query = "INSERT INTO player
    	(playerid, firstname, lastname, username, password, createdate)
    	VALUES (NULL, '$fName', '$lName', '$uName', '$uPass', '$today')";
    	$result = mysql_query($query, $connection);
    	if (!$result)
    	{
        	die ("Could not insert record: <br />". mysql_error());
    	}
		echo "Player Created!";
	break;
	
	}
		
		$connection-->mysql_close;
		?>

<?php
//close page body / add page footer
include_once("footer.php");
?>

</body>
</html>
