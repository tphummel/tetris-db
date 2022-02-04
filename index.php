<?php
$title = "The New Tetris";

require_once "templates/header.php";
require_once "config/db.php";
//require_once("http://thenewtetris.freehostia.com/dblogin.php");

    $mysqli = mysqli_init();
    $mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
    $mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

    //get total matches
    $query = "SELECT count(matchid) as totMatches FROM tntmatch";
    $result = $mysqli->query($query);
    $totMatches = $result->fetch_assoc()['totMatches'];

    //get total performances
    $query = "SELECT count(matchid) as totPerfs FROM playermatch";
    $result = $mysqli->query($query);
    $totPerfs = $result->fetch_assoc()['totPerfs'];

    //get total players
    $query = "SELECT count(playerid) as totPlayers FROM player";
    $result = $mysqli->query($query);
    $totPlayers = $result->fetch_assoc()['totPlayers'];

    //get total time
    $query = "SELECT sum(time) as totTime FROM playermatch";
    $result = $mysqli->query($query);
    $totTime = $result->fetch_assoc()['totTime'];
    $day = intval($totTime/86400);
    $hr = intval(($totTime - ($day*86400))/3600);
    $min = intval(($totTime - ($day*86400) - ($hr*3600))/60);
    $sec = str_pad($totTime - $day*86400 - $hr*3600 - $min*60,2,"0", STR_PAD_LEFT);
    $tStr = $day . ' Days -  ' . $hr . ' Hours -  ' . $min .  ' Minutes -  ' . $sec . ' Seconds';

    //get total lines
    $query = "SELECT sum(pm.lines) as totLines FROM playermatch pm";
    $result = $mysqli->query($query);
    $totLines = $result->fetch_assoc()['totLines'];

    //get 2p perfs
    $query = "select count(p.matchid)
from playermatch p
where (select count(playerid) from playermatch where matchid = p.matchid) = 2";
    $result = $mysqli->query($query);
    $p2Ct = $result->fetch_array()[0];

    //get 3p perfs
    $query = "select count(p.matchid)
from playermatch p
where (select count(playerid) from playermatch where matchid = p.matchid) = 3";
    $result = $mysqli->query($query);
    $p3Ct = $result->fetch_array()[0];

    //get 4p perfs
    $query = "select count(p.matchid)
from playermatch p
where (select count(playerid) from playermatch where matchid = p.matchid) = 4";
    $result = $mysqli->query($query);
    $p4Ct = $result->fetch_array()[0];

    //get 2p matches
    $query = "select count(m.matchid)
from tntmatch m
where (select count(playerid) from playermatch where matchid = m.matchid) = 2";
    $result = $mysqli->query($query);
    $p2CtM = $result->fetch_array()[0];

    //get 3p matches
    $query = "select count(m.matchid)
from tntmatch m
where (select count(playerid) from playermatch where matchid = m.matchid) = 3";
    $result = $mysqli->query($query);
    $p3CtM = $result->fetch_array()[0];

    //get 2p matches
    $query = "select count(m.matchid)
from tntmatch m
where (select count(playerid) from playermatch where matchid = m.matchid) = 4";
    $result = $mysqli->query($query);
    $p4CtM = $result->fetch_array()[0];

    $mysqli->close();

$version = file_get_contents("VERSION");
?>

<b>Welcome to TNT V<?php echo $version ?></b><br><br>

<table border="0">
<tr>
<!-- Stat Summary -->
<td>
  <table cellpadding="10">
    <tr><td colspan="2"><u>Stats At-A-Glance</u></td></tr>
    <tr><td>Total Players</td><td><i><?php echo $totPlayers ?></i></td></tr>
    <tr><td valign="top">Total Matches</td><td><i><?php echo number_format($totMatches,0) ?></i><br>
    <ul>

    <li><i><?php echo '2p: ' . number_format($p2CtM,0); ?></i></li>
    <li><i><?php echo '3p: ' . number_format($p3CtM,0); ?></i></li>
    <li><i><?php echo '4p: ' . number_format($p4CtM,0); ?></i></li>

    </ul>
    </td>
    </tr>

  <tr><td valign="top">Total Performances</td><td><i><?php echo number_format($totPerfs,0) ?></i><br>
  <ul>

  <li><i><?php echo '2p: ' . number_format($p2Ct,0); ?></i></li>
  <li><i><?php echo '3p: ' . number_format($p3Ct,0); ?></i></li>
  <li><i><?php echo '4p: ' . number_format($p4Ct,0); ?></i></li>

  </ul></td></tr>
  <tr><td>Total Time</td><td><i><?php echo $tStr ?></i></td></tr>
  <tr><td>Total Lines</td><td><i><?php echo number_format($totLines,0) ?></i></td></tr>
  </table>
</td>

<td valign="top">
  <table border="1" cellpadding="5">
  <tr>
    <th colspan="2">The Lowdown</th>
  </tr>
  <tr>
    <td>Game:</td><td>The New Tetris</td>
  </tr>
  <tr>
    <td>System:</td><td>Nintendo 64</td>
  </tr>
  <tr>
    <td>Players:</td><td>2-4</td>
  </tr>
  <tr>
    <td>Mode:</td><td>Marathon</td>
  </tr>
  <tr>
    <td>Garbage:</td><td>Directed</td>
  </tr>


  </table>
</td>

</tr>
</table>

<?php
require_once("templates/footer.php");
?>


</body>
</html>
