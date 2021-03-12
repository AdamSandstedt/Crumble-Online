<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once 'users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<style>
body {
  font-family: "Lato", sans-serif;
}

table, td {
    border: 1px solid black;
}

#main {
  transition: margin-left .5s;
  padding-left: 8px;
}
</style>

<head>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="/assets/js/crumble-game.js"></script>
  <script src="/assets/js/cookies.js"></script>
</head>

<body>
  <div id="main">
  <?php
  $username = Input::get("user");
  if($username) {
    $query = $db->query("SELECT id FROM users WHERE username = ?", [$username]);
    if($db->count() == 0) {
      echo 'Username not found';
      die();
    }
    $id = $query->first()->id;
    echo '<h2>Games played by '.$username.'</h2>';
  } else {
    echo '<h2>My Completed Games:</h2>';
    if(isset($user) && $user->isLoggedIn()) {
      $id = $user->data()->id;
    }
  }
  $query = $db->query("SELECT * FROM games_completed WHERE user_id_black = ? OR user_id_white = ? ORDER BY end_time DESC", [$id, $id], array());
  if($db->error())
      file_put_contents ("error_log", "previous-games line 98: ".$db->errorString(), FILE_APPEND);
  $results = $query->results();
  foreach($results as $row) {
      if($row->moves == "")
          $moves = [];
      else
          $moves = explode("\r\n", $row->moves);
      echo '<div style="border:solid; border-width:2px; width:fit-content">';
      echo '<a href="/online/view-game.php?game='.$row->id.'">';
      if($row->user_id_black == $user->data()->id) {
          echo $user->data()->username;
      } else {
          $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_black]);
          if($db->error())
              file_put_contents ("error_log", "previous-games line 112: ".$db->errorString(), FILE_APPEND);
          echo $query->first()->username;
      }
      echo " vs ";
      if($row->user_id_white == $user->data()->id) {
          echo $user->data()->username;
      } else {
          $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_white]);
          if($db->error())
              file_put_contents ("error_log", "previous-games line 121: ".$db->errorString(), FILE_APPEND);
          echo $query->first()->username;
      }
      echo '<div style="position: relative; width: fit-content; height: fit-content">
  <canvas id="crumble-canvas'.$row->id.'"
    style="border:1px solid black; position: relative; width: 400px; height: '. $row->height/$row->width*400 .'px"></canvas>
  </div>';
      echo '<table>';
      for($i = 0; $i < count($moves); $i++) {
          if($i % 2 == 0) {
              echo "<tr><td>".($i / 2 + 1).".</td><td>".$moves[$i]."</td>";
          } else {
              echo '<td>'.$moves[$i]."</td></tr>";
          }
      }
      echo '</table></a></div><br>';
      echo '<script>$(function() {
      const pixelDensity = 128;
      var canvas = document.getElementById("crumble-canvas'.$row->id.'");
      canvas.width = pixelDensity*'.$row->width.';
      canvas.height = pixelDensity*'.$row->height.';
      var cb = new CBoard('.$row->height.', '.$row->width.', "'.$row->extra.'");
      var cg = new CGame(cb);
      setupGraphics(cg, canvas, undefined, false, false);';
      foreach($moves as $move) {
          echo 'cg.doMove("'.$move.'", canvas);';
      }
      echo '});</script>';
  }
  ?>

  <!--h3>Completed Games:</h3-->

  </div>
</body>
</html>
