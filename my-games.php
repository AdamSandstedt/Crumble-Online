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
  if(isset($_GET["user"]) && isset($user) && $user->isLoggedIn() && $user->data()->id == 1) {
    $id = $_GET["user"];
    $query = $db->query("SELECT * FROM games_current WHERE user_id_black = ? OR user_id_white = ? ORDER BY time DESC", [$id, $id], array());
    if($db->error())
        file_put_contents ("error_log", "my-games line 39: ".$db->errorString(), FILE_APPEND);
    $results = $query->results();
    foreach($results as $row) {
        if($row->moves == "")
            $moves = [];
        else
            $moves = explode("\r\n", $row->moves);
        echo '<div style="border:solid; border-width:2px; width:fit-content">';
        echo '<a href="/online/play.php?game='.$row->id.'">';
        if($row->user_id_black == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_black]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 53: ".$db->errorString(), FILE_APPEND);
            echo $query->first()->username;
        }
        echo " vs ";
        if($row->user_id_white == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_white]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 62: ".$db->errorString(), FILE_APPEND);
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
  } else {
    echo '<h2>My Games:</h2>';
  if(isset($_COOKIE["moves"])) {
    $moves = $_COOKIE["moves"];
  }
  if(isset($_COOKIE["boardWidth"])) {
    $width = $_COOKIE["boardWidth"];
  }
  if(isset($_COOKIE["boardHeight"])) {
    $height = $_COOKIE["boardHeight"];
  }
  if(isset($_COOKIE["extra"])) {
    $extra = $_COOKIE["extra"];
  }
  if(isset($moves) && isset($width) && isset($height) && isset($extra)) {
    echo '<div style="border:solid; border-width:2px; width:fit-content">';
    if(isset($_COOKIE["ai-player"]))
      echo '<a href="/local/single-player.php">';
    else
      echo '<a href="/local/two-player.php">';
    echo '<table style="border:none;"><tr><td style="border:none; vertical-align:top">
    Local Game
    </td></tr><tr><td style="border:none; vertical-align:top">
    <div style="position: relative; width: fit-content; height: fit-content">
    <canvas id="crumble-canvas"
        style="border:1px solid black; position: relative; width: 400px; height: '. $height/$width*400 .'px"></canvas>
    </td><td style="border:none; vertical-align:top;">
    </div><table>';
    $moves = explode("/", $moves);
    for($i = 0; $i < count($moves); $i++) {
        if($i % 2 == 0) {
            echo "<tr><td>".($i / 2 + 1).".</td><td>".$moves[$i]."</td>";
        } else {
            echo '<td>'.$moves[$i]."</td></tr>";
        }
    }
    echo '</table></table></a></div><br>';
    echo '<script>$(function() {
    const pixelDensity = 128;
    var canvas = document.getElementById("crumble-canvas");
    canvas.width = pixelDensity*'.$width.';
    canvas.height = pixelDensity*'.$height.';
    var cb = new CBoard('.$height.', '.$width.', "'.$extra.'");
    var cg = new CGame(cb);
    setupGraphics(cg, canvas, undefined, false, false);';
    foreach($moves as $move) {
        echo 'cg.doMove("'.$move.'", canvas);';
    }
    echo '});</script>';

  }
    echo '<h3>Online Games:</h3>';

  if(isset($user) && $user->isLoggedIn()) {
    $id = $user->data()->id;
    $query = $db->query("SELECT * FROM games_current WHERE user_id_black = ? OR user_id_white = ? ORDER BY time DESC", [$id, $id], array());
    if($db->error())
        file_put_contents ("error_log", "my-games line 139: ".$db->errorString(), FILE_APPEND);
    $results = $query->results();
    echo '<script>
    var your_turn = 0;
    var opponents_turn = 0;
    </script>';
    echo '<h4>Your Turn</h4>';
    foreach($results as $row) {
        if($row->moves == "")
            $moves = [];
        else
            $moves = explode("\r\n", $row->moves);
        if($row->user_id_black == $user->data()->id && count($moves) % 2 == 1 || $row->user_id_white == $user->data()->id && count($moves) % 2 == 0)
            continue;
        echo '<div style="border:solid; border-width:2px; width:fit-content">';
        echo '<a href="/online/play.php?game='.$row->id.'">
        <table style="border:none;"><tr><td style="border:none; vertical-align:top">';
        if($row->user_id_black == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_black]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 156: ".$db->errorString(), FILE_APPEND);
            echo $query->first()->username;
        }
        echo " vs ";
        if($row->user_id_white == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_white]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 165: ".$db->errorString(), FILE_APPEND);
            echo $query->first()->username;
        }
        echo '</td></tr><tr><td style="border:none; vertical-align:top"><div style="position: relative; width: fit-content; height: fit-content">
    <canvas id="crumble-canvas'.$row->id.'"
      style="border:1px solid black; position: relative; width: 400px; height: '. $row->height/$row->width*400 .'px"></canvas>
    </div>';
        echo '</td><td style="border:none; vertical-align:top"><table>';
        for($i = 0; $i < count($moves); $i++) {
            if($i % 2 == 0) {
                echo "<tr><td>".($i / 2 + 1).".</td><td>".$moves[$i]."</td>";
            } else {
                echo '<td>'.$moves[$i]."</td></tr>";
            }
        }
        echo '</table></table></a></div><br>';
        echo '<script>your_turn++;
        $(function() {
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
    echo '<h4>Opponent\'s Turn</h4>';
    foreach($results as $row) {
        if($row->moves == "")
            $moves = [];
        else
            $moves = explode("\r\n", $row->moves);
        if($row->user_id_black == $user->data()->id && count($moves) % 2 == 0 || $row->user_id_white == $user->data()->id && count($moves) % 2 == 1)
            continue;
        echo '<div style="border:solid; border-width:2px; width:fit-content">';
        echo '<a href="/online/play.php?game='.$row->id.'">
        <table style="border:none;"><tr><td style="border:none; vertical-align:top">';
        if($row->user_id_black == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_black]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 209: ".$db->errorString(), FILE_APPEND);
            echo $query->first()->username;
        }
        echo " vs ";
        if($row->user_id_white == $user->data()->id) {
            echo $user->data()->username;
        } else {
            $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id_white]);
            if($db->error())
                file_put_contents ("error_log", "my-games line 218: ".$db->errorString(), FILE_APPEND);
            echo $query->first()->username;
        }
        echo '</td></tr><tr><td style="border:none; vertical-align:top"><div style="position: relative; width: fit-content; height: fit-content">
    <canvas id="crumble-canvas'.$row->id.'"
      style="border:1px solid black; position: relative; width: 400px; height: '. $row->height/$row->width*400 .'px"></canvas>
    </div>';
        echo '</td><td style="border:none; vertical-align:top"><table>';
        for($i = 0; $i < count($moves); $i++) {
            if($i % 2 == 0) {
                echo "<tr><td>".($i / 2 + 1).".</td><td>".$moves[$i]."</td>";
            } else {
                echo '<td>'.$moves[$i]."</td></tr>";
            }
        }
        echo '</table></table></a></div><br>';
        echo '<script>opponents_turn++;
        $(function() {
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
  }
  }
  ?>
  <script>
    setTimeout(() => {
        location.reload();
    }, 1200000);
    var turns = your_turn + " " + opponents_turn;
    var xmlhttp = new XMLHttpRequest();
    // var refreshTime = new Date().getTime() + 1200000;
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // console.log(this.responseText, turns, this.responseText == turns);
            // console.log(refreshTime - new Date().getTime());
            // if(new Date().getTime() > refreshTime) { // refresh the page after 20 minutes of inactivity
            //     location.reload();
            // }
            if(this.responseText == turns) {
                setTimeout(() => {
                    this.open("GET", "/assets/php/get_games.php", true);
                    this.send();
                }, 20000);
            } else {
                location.reload();
            }
        }
    };
    xmlhttp.open("GET", "/assets/php/get_games.php", true);
    xmlhttp.send();
  </script>

  </div>
</body>
</html>
