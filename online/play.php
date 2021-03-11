<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
$query = $db->query('SELECT * FROM games_current WHERE id = ?', [$_GET["game"]]);
if($db->error())
    file_put_contents ("error_log", "play line 9: ".$db->errorString(), FILE_APPEND);
$results = $query->first();
if(!isset($results->user_id_black)) {
    $query = $db->query('SELECT * FROM games_completed WHERE id = ?', [$_GET["game"]]);
    if($db->error())
        file_put_contents ("error_log", "play line 14: ".$db->errorString(), FILE_APPEND);
    $results = $query->first();
    if($user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white){die();}
    echo '<script>
    window.location.replace("/online/view-game.php?game='.$results->id.'");
    </script>';
}
if($user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white){die();}
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
  <title>Online Game</title>
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
<div id="main" style="width:fit-content; clear:both;">
  <h2 style="margin-bottom: 10px">Online Game - <?php
    if($results->moves == '')
        $moves = [];
    else
        $moves = explode("\r\n", $results->moves);
    if($results->user_id_black == $user->data()->id) {
        $player = "b";
        echo $user->data()->username;
        if(count($moves) % 2 == 0)
            $your_turn = true;
        else
            $your_turn = false;
    } else {
        $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$results->user_id_black]);
        if($db->error())
            file_put_contents ("error_log", "play line 72: ".$db->errorString(), FILE_APPEND);
        echo $query->first()->username;
    }
    echo " vs ";
    if($results->user_id_white == $user->data()->id) {
        $player = "w";
        echo $user->data()->username;
        if(count($moves) % 2 == 1)
            $your_turn = true;
        else
            $your_turn = false;
    } else {
        $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$results->user_id_white]);
        if($db->error())
            file_put_contents ("error_log", "play line 85: ".$db->errorString(), FILE_APPEND);
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
    <button class="ui-button ui-widget ui-corner-all" id="end-turn">End Turn</button>
    <label for="checkbox-notations">Show Notations</label>
    <input type="checkbox" name="checkbox-1" id="checkbox-notations">
    <button class="ui-button ui-widget ui-corner-all" id="previous-move"><</button>
    <button class="ui-button ui-widget ui-corner-all" id="next-move">></button>
    <br>
    <button class="ui-button ui-widget ui-corner-all" id="copy-to-local">Copy Game to Local</button>
  </div>
  <br><br>

</div></td><td style="border:none;">
<div>
<table id="moves-table" style="float: right;">
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
</div></td></tr>
</table>
  <?php
    echo '<script>
    var h = '.$results->height.'
    var w = '.$results->width.'
    var extra = "'.$results->extra.'"
    const pixelDensity = 128;
    var canvas = document.getElementById("crumble-canvas");
    canvas.width = pixelDensity*w;
    canvas.height = pixelDensity*h;';
    echo 'var icanvas = document.getElementById("interactive-canvas");
    icanvas.width = pixelDensity*w;
    icanvas.height = pixelDensity*h;';
    echo 'var cb = new CBoard(h, w, extra);
    ';
    if(isset($results->time_black) && isset($results->time_white)) {
        echo 'var timeb = '.$results->time_black.';
        var timew = '.$results->time_white.';';
    } else {
        echo 'var timeb = undefined;
        var timew = undefined;';
    }
    if(!isset($results->time) || count($moves) == 0) {
        echo 'var time = undefined;';
    } else {
        echo 'var time = '.$results->time.';';
    }
    echo 'var cg = new CGame(cb, false, time, timeb, timew);';
    foreach($moves as $move) {
        echo 'cg.doMove("'.$move.'", canvas);';
    }
    echo 'var table = document.getElementById("moves-table");
    setupGraphics(cg, canvas, icanvas, true, '.$results->id.', table);
    if(cg.historyIndex > 0)
      table.rows[Math.floor((cg.historyIndex - 1) / 2)].cells[((cg.historyIndex - 1) % 2) + 1].style.backgroundColor = "yellow";';
    if(!$your_turn) {
        echo 'cg.action = "done";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if((cg.history.length == 0 ? "" : cg.history[cg.history.length-1]) == this.responseText) {
                    setTimeout(() => {
                        this.open("GET", "/assets/php/get_move.php?game='.$results->id.'", true);
                        this.send();
                    }, 1000);
                } else {
                    // console.log(cg.notation);
                    // console.log(this.responseText);
                    var time = Math.floor(new Date().getTime() / 1000);
                    if(cg.time && cg.timeb && cg.timew) {
                        if(cg.turn == "b") {
                            cg.timeb -= time - cg.time;
                        } else {
                            cg.timew -= time - cg.time;
                        }
                        console.log("play");
                    }
                    cg.time = time;
                    while(cg.historyIndex < cg.history.length) {
                      cg.redo('.$results->id.', table);
                    }
                    if(cg.historyIndex > 0)
                      table.rows[Math.floor((cg.historyIndex - 1) / 2)].cells[((cg.historyIndex - 1) % 2) + 1].style.backgroundColor = "initial";
                    cg.doMove(this.responseText, canvas);
                    if(cg.winner) {
                        window.location.replace("/online/view-game.php?game='.$results->id.'");
                    }
                    var rows = table.rows;
                    var lastRow = rows[rows.length-1];
                    if(!lastRow || lastRow.cells.length == 3) {
                        lastRow = table.insertRow();
                        lastRow.insertCell();
                        lastRow.cells[0].innerText = rows.length + ".";
                    }
                    lastRow.insertCell();
                    lastRow.cells[lastRow.cells.length-1].innerText = this.responseText;
                    table.rows[Math.floor((cg.historyIndex - 1) / 2)].cells[((cg.historyIndex - 1) % 2) + 1].style.backgroundColor = "yellow";
                    if(cg.timeb && cg.timew) {
                        xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                // console.log(this.responseText);
                                var times = this.responseText.split(" ");
                                cg.time = times[0];
                                cg.timeb = times[1];
                                cg.timew = times[2];
                            } else if(this.readyState == 4) {
                                window.location.reload();
                            }
                        };
                        xmlhttp.open("GET", "/assets/php/get_times.php?game='.$results->id.'", true);
                        xmlhttp.send();
                    }
                }
            } else if(this.readyState == 4) {
                window.location.reload();
            }
        };
        xmlhttp.open("GET", "/assets/php/get_move.php?game='.$results->id.'", true);
        xmlhttp.send();
        ';
    }
    echo 'document.getElementById("checkbox-notations").onclick = function() {
    cg.showNotations = $(this).prop("checked");
    cb.draw(canvas, cg.notationMap, cg.showNotations);
    }
    $( "#copy-to-local" ).click(function() {
      setCookie("moves", cg.history.join("/"), 24*7);
      setCookie("boardWidth", cg.board.width, 24*7);
      setCookie("boardHeight", cg.board.height, 24*7);
      setCookie("extra", cg.board.extra, 24*7);
      window.location.href = "/local/two-player.php";
    });
    ';
    if(isset($results->time_black) && isset($results->time_white)) {
    echo '
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
    if(count($moves) == 0) {
    } else if(count($moves) % 2 == 0) {
        echo '
        if(secondsW == 0) {
            document.getElementById("time-white").innerHTML = "out of time";
        }';
    }else {
        echo '
        if(secondsB == 0) {
            document.getElementById("time-black").innerHTML = "out of time";
        }
        ';
    }

    echo '
    (function updateTime() {
    var player = "'.$player.'";
    var turn = cg.history.length % 2 == 0 ? "b" : "w";
    if((turn == "b" ? cg.timew : cg.timeb) > 0 && cg.time) {
        var now = Math.floor(new Date().getTime() / 1000);
        var countDownDate = +cg.time + (turn == "b" ? +cg.timeb : +cg.timew);
        var distance = countDownDate - now;
    } else {
        var distance = turn == "b" ? cg.timeb : cg.timew;
    }
    // console.log(+cg.time, cg.timeb, cg.timew);

    var hours = Math.floor(distance / (60 * 60));
    var minutes = Math.floor((distance % (60 * 60)) / 60);
    var seconds = Math.floor(distance % 60);
    // console.log(now, distance, cg.time);

    document.getElementById(turn == "b" ? "time-black" : "time-white").innerHTML = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    if (distance <= 0) {
    document.getElementById(turn == "b" ? "time-black" : "time-white").innerHTML = "out of time";
    if(distance <= -10) {
        cg.winner = turn == "b" ? "w" : "b";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else if(this.readyState == 4) {
                window.location.reload();
            }
        };
        xmlhttp.open("POST", "/assets/php/submit-move.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("game='.$results->id.'&move=done&win=" + (turn == "b" ? "w" : "b") + "&time="+Math.floor(new Date().getTime() / 1000));
    }
    }
    var distance1 = distance;

    distance = turn == "b" ? cg.timew : cg.timeb;
    var hours = Math.floor(distance / (60 * 60));
    var minutes = Math.floor((distance % (60 * 60)) / 60);
    var seconds = Math.floor(distance % 60);
    document.getElementById(turn == "b" ? "time-white" : "time-black").innerHTML = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    if (distance <= 0) {
    document.getElementById(turn == "b" ? "time-white" : "time-black").innerHTML = "out of time";
    if(distance <= -10) {
        cg.winner = turn == "b" ? "w" : "b";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else if(this.readyState == 4) {
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
        };
        xmlhttp.open("POST", "/assets/php/submit-move.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send("game='.$results->id.'&move=done&win=" + (turn == "b" ? "w" : "b") + "&time="+Math.floor(new Date().getTime() / 1000));
    }
    }
    if(distance1 <= 0 || distance <= 0) {
        cg.action = "done";
    } else {
        if(cg.action == "done" && player == turn)
            cg.action = "";
    }

    // if(cg.winner != "b" && cg.winner != "w" && cg.winner != "d")
    setTimeout(updateTime, 200);
    })();
    ';
    }
    echo '</script>';
  ?>

</body>
</html>
