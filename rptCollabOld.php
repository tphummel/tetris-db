<html>
<head>
<title>Collaboration Report</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script type="text/javascript" src="validate.js"></script>
<link rel="stylesheet" type="text/css" href="style1.css" />
</head>

<body>

<?php
//broke this by changing tmext view, took away the first, second, third, fourth.  Now allowing ties so its not so cut and dry.  
//might just have to get the players in each match with php.

# rptCollab.php
# reports match summaries


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
?>

<div class="report">
	<form name="criteria" method="get" action="rptCollab.php">

<!--Criteria-->
	<?php
	$sizes = array('25', '50', '100', 'All');
	?>
	<h1>Match Collaboration Report</h1>
	
	<div>
	<label>Results:&nbsp;</label>
	<select name="size" >
	<?php
	foreach ($sizes as $size)
	{
	?>
		<option value="<?php echo $size; ?>"><?php echo $size; ?></option>
	<?php
	}
	$p4Check = (!empty($_GET["p4"])) ? "Checked" : "";
	$p3Check = (!empty($_GET["p3"])) ? "Checked" : "";
	$p2Check = (!empty($_GET["p2"])) ? "Checked" : "";
	?>
	</select>
	&nbsp;-&nbsp;
	<label for="2p"><input id="2p" type="checkbox" name="p2" value="2" <?php echo $p2Check; ?> >2p</label>
	&nbsp;
	<label for="3p"><input id="3p" type="checkbox" name="p3" value="3" <?php echo $p3Check; ?> >3p</label>
	&nbsp;
	<label for="4p"><input id="4p" type="checkbox" name="p4" value="4" <?php echo $p4Check; ?> >4p</label>
	
	&nbsp;-&nbsp;
	
	<input type="submit" value="Generate">
<!--
function setAll()
{
        document.forms['q'].elements['audio'].checked = false;
        document.forms['q'].elements['video'].checked = false;
        document.forms['q'].elements['apps'].checked = false;
        document.forms['q'].elements['games'].checked = false;
        document.forms['q'].elements['porn'].checked = false;
        document.forms['q'].elements['other'].checked = false;
}
function rmAll()
{
        document.forms['q'].elements['all'].checked = false;
}
-->
	</div>
	</form>
	
<?php
//Report Body if criteria has been chosen
if (isset($_GET["size"])) //this checks if form was submitted
{
	if(isset($_GET["sort"]))
	{
		$sort = $_GET["sort"];
	}
	else
	{
		$sort = 1;
	}
	
	//sort column
	switch ($sort)
	{
		case 1:
			$sort = "mTi";
		break;
		
		case 2:
			$sort = "mTpp";
		break;
		
		case 3:
			$sort = "mLi";
		break;
		
		case 4:
			$sort = "mLps";
		break;
		
		case 5:
			$sort = "mLpp";
		break;
	}
	
	//player counts to include
	$mTypes = array();
	//$_GET["p4"], $_GET["p3"], $_GET["p2"]);
	if(!empty($_GET["p4"]))
	{
		$mTypes[] = $_GET["p4"];
	}
	if(!empty($_GET["p3"]))
	{
		$mTypes[] = $_GET["p3"];
	}
	if(!empty($_GET["p2"]))
	{
		$mTypes[] = $_GET["p2"];
	}
	
	$where = "WHERE pCt in(";
	foreach ($mTypes as $mt)
	{
		$where = $where . $mt . ", ";
	}
	//where clause for game types
	$where = trim($where, ", ");
	$where = $where . ") ";
	
	//trim last comma, add close paren + space
	
	//asc or desc - alternates if each time same sort header link is clicked
	if(isset($_GET["order"]))
	{
		$order = $_GET["order"];
		if($order == "desc")
		{
			$order = "asc";
		}
		elseif($order == "asc")
		{
			$order = "desc";
		}
		else
		{
			$order = "asc";
		}
	}
	else
	{
		$order = "desc";
	}
	
	$size = $_GET["size"];
	$all = $_GET["all"];
	$twoP = $_GET["2p"];
	$threeP = $_GET["3p"];
	$fourP = $_GET["4p"];
	
	$pCount = -1;
	
	//assemble query string
	$query = "SELECT * FROM tmext " . $where . " ORDER BY " . $sort . " " . $order . " limit " . $size;
	echo $query;
	$result = mysql_query($query, $connection) or die(mysql_error());
	?>
	
	<table align="center">
	<tr>
	<td class="tablehead" colspan="12">Match Totals</td>
	</tr>
	<tr>
	<td class="colhead">MatchID</td>
	<td class="colhead">Date</td>
	<td class="colhead"><a href="rptCollab.php?sort=1&order=<?php echo $order; ?>&size=<?php echo $size; 
		if(!empty($_GET["p4"]))
		{
			echo "&p4=4";
		}
		if(!empty($_GET["p3"]))
		{
			echo "&p3=3";
		}
		if(!empty($_GET["p2"]))
		{
			echo "&p2=2";
		}
	?>
	">Time</a></td>
	<td class="colhead"><a href="rptCollab.php?sort=2&order=<?php echo $order; ?>&size=<?php echo $size; 
		if(!empty($_GET["p4"]))
		{
			echo "&p4=4";
		}
		if(!empty($_GET["p3"]))
		{
			echo "&p3=3";
		}
		if(!empty($_GET["p2"]))
		{
			echo "&p2=2";
		}
	?>">TPP</a></td>
	<td class="colhead"><a href="rptCollab.php?sort=3&order=<?php echo $order; ?>&size=<?php echo $size; 
		if(!empty($_GET["p4"]))
		{
			echo "&p4=4";
		}
		if(!empty($_GET["p3"]))
		{
			echo "&p3=3";
		}
		if(!empty($_GET["p2"]))
		{
			echo "&p2=2";
		}
	?>">Lines</a></td>
	<td class="colhead"><a href="rptCollab.php?sort=4&order=<?php echo $order; ?>&size=<?php echo $size; 
		if(!empty($_GET["p4"]))
		{
			echo "&p4=4";
		}
		if(!empty($_GET["p3"]))
		{
			echo "&p3=3";
		}
		if(!empty($_GET["p2"]))
		{
			echo "&p2=2";
		}
	?>">LPS</a></td>
	<td class="colhead"><a href="rptCollab.php?sort=5&order=<?php echo $order; ?>&size=<?php echo $size; 
		if(!empty($_GET["p4"]))
		{
			echo "&p4=4";
		}
		if(!empty($_GET["p3"]))
		{
			echo "&p3=3";
		}
		if(!empty($_GET["p2"]))
		{
			echo "&p2=2";
		}
		
		###############
		# Behavior a little funky with having to click generate to update the number of players before you can resort by column header
		# But it works
		###############
		
		
		
	?>">LPP</a></td>
	<td class="colhead">Grade</td>
	<td class="colhead">First</td>
	<td class="colhead">Second</td>
	<td class="colhead">Third</td>
	<td class="colhead">Fourth</td>
	</tr>
	
	<?php
	while ($row = mysql_fetch_array($result))
	{
		$mid = $row["matchid"];
		$mDate = $row["matchdate"];
		$pCt = $row["pCt"];
		$mLi = $row["mLi"];
		$mTi = $row["mTi"];
		
		$mLps = $row["mLps"];
		$mGrade = gradePerf($mTi, $mLi);
		$mLpp = $mLi/$pCt;
		
		$mTpp = $mTi/$pCt;
		$name1 = $row["name1"];
		$id1 = $row["id1"];
		$first = '<a href="playerinfo.php?playerid=' . $id1 . '">' . $name1 . '</a>';
		
		$name2 = $row["name2"];
		$id2 = $row["id2"];
		$second = '<a href="playerinfo.php?playerid=' . $id2 . '">' . $name2 . '</a>';
		
		if(isset($row["name3"]))
		{
			$name3 = $row["name3"];
			$id3 = $row["id3"];
			$third = '<a href="playerinfo.php?playerid=' . $id3 . '">' . $name3 . '</a>';
			if(isset($row["name4"]))
			{
				$name4 = $row["name4"];
				$id4 = $row["id4"];
				$fourth = '<a href="playerinfo.php?playerid=' . $id4 . '">' . $name4 . '</a>';
			}
			else
			{
				$fourth = "-";
			}
		}
		else
		{
			$third = "-";
			$fourth = "-";
		}
		
		
		?>
		<tr>
		<td class="data"><?php echo '<a href="matchinfo.php?matchid=' . $mid . '">' . $mid . '</a>'; ?></td>
		<td class="data"><?php echo $mDate; ?></td>
		<td class="data"><?php echo $mTi; ?></td>
		<td class="data"><?php echo round($mTpp, 2); ?></td>
		
		<td class="data"><?php echo $mLi; ?></td>
		<td class="data"><?php echo $mLps; ?></td>
		<td class="data"><?php echo $mLpp; ?></td>
		<td class="data"><?php echo $mGrade; ?></td>
		<td class="data"><?php echo $first;  ?></td>
		<td class="data"><?php echo $second;  ?></td>
		<td class="data"><?php echo $third;  ?></td>
		<td class="data"><?php echo $fourth;  ?></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}
?>
</div>

<?php
include_once("footer.php");
?>
</body>
</html>