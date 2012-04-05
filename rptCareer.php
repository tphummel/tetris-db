<html>
<head>
<title>Performance Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>

<body>

<?php

# rptCareer.php
# reports career records

/*
highest lines career
highest lps
highest games
highest ewins
highest lwins
highest power wins
regardless of time
*/


//left navbar / banner
include_once("header.php");
require_once("db_login.php");
require_once("points.inc.php");
require_once("grade.php");
require_once("statPower.php");

 
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
		



include_once("footer.php");
?>
</body>
</html>