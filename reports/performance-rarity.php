<?php
$title = "Report : Performance Rarity";

$dir = dirname ( __FILE__ ) ;
include_once($dir . "/../templates/header.php");
require_once($dir . "/../config/db.php");

$mysqli = mysqli_init();
$mysqli->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-certificates.crt", NULL, NULL);
$mysqli->real_connect($db_host, $db_username, $db_password, $db_database);

// show form
if(array_key_exists('type',$_GET)){
  $matchtype = $_GET['type'];
}else{
  $matchtype = "";
}
?>

<div class="report">
<h1>Performance Rarity Report</h1>
  <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
  <select name="type">
    <option value="2" <?php echo ($matchtype=="2")?" SELECTED":"" ?>>2P</option>
    <option value="3" <?php echo ($matchtype=="3")?" SELECTED":"" ?>>3P</option>
    <option value="4" <?php echo ($matchtype=="4")?" SELECTED":"" ?>>4P</option>
    <option value="ALL" <?php echo ($matchtype=="ALL")?" SELECTED":"" ?>>ALL</option>
  </select>
  <input type="submit" value="submit">
  </form>
<?php
if(array_key_exists('type',$_GET)){
  if($matchtype == 'ALL'){
    $title = "All Match Types";
    $rptPlayers = '2,3,4';
  }else{
    $title = $matchtype ."P Matches Only";
    $rptPlayers = $matchtype;
  }

  $query = "
    SELECT a.lines, COUNT(a.matchid) as freq
    FROM playermatch a
    WHERE (select count(playerid) from playermatch where matchid = a.matchid) IN (".$rptPlayers.")
    GROUP BY a.lines
    ORDER BY a.lines ASC";

  $result = $mysqli->query($query);
  $mysqli->close();
  ?>
  <table style="margin: 0 auto;">
  <tr>
    <td class="tablehead" colspan="12"><?php echo $title; ?></td>
  </tr>
  <tr>
    <td class="colhead">Lines</td>
    <td class="colhead">Freq</td>
  </tr>
  <?php
  $count = 0;
  while ($row = $result->fetch_object()) {
    while ($count < $row->lines) {
      ?>
      <tr>
        <td class="data" style="background: yellow;"><?php echo $count; ?></td>
        <td class="data" style="background: yellow;">0</td>
      </tr>
      <?php
      $count++;
    }

    ?>
    <tr>
      <td class="data"><?php echo $row->lines; ?></td>
      <td class="data"><?php echo $row->freq; ?></td>
    </tr>
    <?php
    $count++;
  }
  ?>
  </table>
<!-- close report div -->
</div>
<?php
}
include_once($dir . "/../templates/footer.php");
?>
</body>
</html>
