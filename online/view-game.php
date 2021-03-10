<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
$query = $db->query('SELECT * FROM games_completed WHERE id = ?', [$_GET["game"]]);
if($db->error())
    file_put_contents ("error_log", "view-game line 9: ".$db->errorString(), FILE_APPEND);
$results = $query->first();
if($db->count() == 0 || $user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white){die();}
?>

<style>
body {
  font-family: "Lato", sans-serif;
}

table, td {
    border: 1px solid black;
    overflow: hidden;
    white-space: nowrap;
    vertical-align: top;
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
  <script>
  $( function() {
    $( "input[type='checkbox']" ).checkboxradio();
  });
  </script>
</head>

<body>
<div id="main" style="width:fit-content;">
  <h2 style="margin-bottom: 10px">Game Over - <?php
    if($results->moves == '')
        $moves = [];
    else
        $moves = explode("\r\n", $results->moves);
    if($results->user_id_black == $user->data()->id) {
        echo $user->data()->username;
        if(count($moves) % 2 == 0)
            $your_turn = true;
        else
            $your_turn = false;
    } else {
        $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$results->user_id_black]);
        if($db->error())
            file_put_contents ("error_log", "view-game line 61: ".$db->errorString(), FILE_APPEND);
        echo $query->first()->username;
    }
    echo " vs ";
    if($results->user_id_white == $user->data()->id) {
        echo $user->data()->username;
        if(count($moves) % 2 == 1)
            $your_turn = true;
        else
            $your_turn = false;
    } else {
        $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$results->user_id_white]);
        if($db->error())
            file_put_contents ("error_log", "view-game line 74: ".$db->errorString(), FILE_APPEND);
        echo $query->first()->username;
    }
    echo '</h2>';
    if(isset($results->time_black) && isset($results->time_white)) {
        echo '<h3 id="time-black" style="display:inline"></h3><h3 style="display:inline;"> - </h3><h3 id="time-white" style="display:inline;"></h3>';
    }
    ?>
  <table style="border:none;"><tr><td style="border:none;">

  <div style="position: relative; width: fit-content; height: fit-content">
  <canvas id="crumble-canvas"
    style="border:1px solid black; position: relative;"></canvas>
  <canvas id="interactive-canvas"
    style="position: absolute; left: 0; top: 0; z-index: 1;"></canvas>
  </div>
  <div>
    <label for="checkbox-notations">Show Notations</label>
    <input type="checkbox" name="checkbox-1" id="checkbox-notations">
    <button class="ui-button ui-widget ui-corner-all" id="previous-move"><</button>
    <button class="ui-button ui-widget ui-corner-all" id="next-move">></button>
  </div>
  <br><br>

  <?php
    echo '<script>
    $( function() {
    var h = '.$results->height.'
    var w = '.$results->width.'
    var extra = "'.$results->extra.'"
    const pixelDensity = 128;
    var canvas = document.getElementById("crumble-canvas");
    canvas.width = pixelDensity*w;
    canvas.height = pixelDensity*h;';
    if(isset($results->time_black) && isset($results->time_white)) {
        echo 'var timeb = '.$results->time_black.';
        var timew = '.$results->time_white.';';
    } else {
        echo 'var timeb = undefined;
        var timew = undefined;';
    }
    echo 'var time = undefined;';
    echo 'var cb = new CBoard(h, w, extra);
    var cg = new CGame(cb, false, time, timeb, timew);';
    echo 'setupGraphics(cg, canvas, undefined, true);';
    foreach($moves as $move) {
        echo 'cg.doMove("'.$move.'", canvas);';
    }
    echo 'document.getElementById("checkbox-notations").onclick = function() {
    cg.showNotations = $(this).prop("checked");
    cb.draw(canvas, cg.notationMap, cg.showNotations);
    }
    });';
    if(isset($results->time_black) && isset($results->time_white)) {
    echo 'var startTimer = true;
    var secondsB = '.$results->time_black.';
    var hours = Math.floor(secondsB / (60 * 60));
    var minutes = Math.floor((secondsB % (60 * 60)) / 60);
    var seconds = Math.floor(secondsB % 60);
    document.getElementById("time-black").innerHTML = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    secondsW = '.$results->time_white.';
    hours = Math.floor(secondsW / (60 * 60));
    minutes = Math.floor((secondsW % (60 * 60)) / 60);
    seconds = Math.floor(secondsW % 60);
    document.getElementById("time-white").innerHTML = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    ';
    echo '
    if(secondsW == 0) {
        document.getElementById("time-white").innerHTML = "out of time";
    }
    if(secondsB == 0) {
        document.getElementById("time-black").innerHTML = "out of time";
    }';
    }
    echo '</script>';
  ?>

</div></td><td style="border:none;">
<div>
<table style="float: right;">
<?php
for($i = 0; $i < count($moves); $i++) {
    if($i % 2 == 0) {
        echo "<tr><td>".($i / 2 + 1).".</td><td>".$moves[$i]."</td>";
    } else {
        echo '<td>'.$moves[$i]."</td></tr>";
    }
}
?>
</table>
</div></td></tr></table>

</body>
</html>
