<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
?>

<style>
body {
  font-family: "Lato", sans-serif;
}

#main {
  transition: margin-left .5s;
  padding-left: 8px;
}

.radio-label {
  margin: 8px;
}

.spinner-size {
  width: 20px;
}

.button {
  text-decoration: none;
  background-color: #EEEEEE;
  color: #333333;
  padding: 8px 10px 8px 10px;
  border-top: 1px solid #CCCCCC;
  border-right: 1px solid #333333;
  border-bottom: 1px solid #333333;
  border-left: 1px solid #CCCCCC;
}

.button:hover {
  background-color: #DDDDDD;
}
</style>

<head>
  <title>New Game</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="/assets/js/cookies.js"></script>
  <script>
  $( function() {
    $( "input[type='radio']" ).checkboxradio({
      icon: false,
    });
    $( "#radio-single-player" ).checkboxradio( "disable" );
    document.getElementById("radio-online").onclick = function() {
      document.getElementById("select-players").style.display = "none";
      document.getElementById("select-challenge").style.display = "";
    }
    document.getElementById("radio-local").onclick = function() {
      document.getElementById("select-players").style.display = "";
      document.getElementById("select-challenge").style.display = "none";
    }
    document.getElementById("radio-open-challenges").onclick = function() {
      document.getElementById("div-open-challenges").style.display = "";
      document.getElementById("div-create-challenge").style.display = "none";
    }
    document.getElementById("radio-create-challenge").onclick = function() {
      document.getElementById("div-open-challenges").style.display = "none";
      document.getElementById("div-create-challenge").style.display = "";
    }

    $( ".spinner-size" ).spinner({
      min: 2,
      stop: function(event, ui) {
          if($("#spinner-height").spinner("value") % 2 == 1 && $("#spinner-width").spinner("value") % 2 == 1) {
              document.getElementById("div-extra").style.display = "";
          } else {
              document.getElementById("div-extra").style.display = "none";
          }
      }
    });
    $( ".spinner-size" ).spinner( "value", 6 );

    $( "#slider-level" ).slider({
      min: 1,
      max: 21,
      value: 21,
      slide: function( event, ui ) {
        var x = ui.value;
        if(ui.value == 21) {
          x = "&#8734";
        }
        document.getElementById("label-level").innerHTML = "Crumble Level: " + x;
      },
      disabled: true,
    });

    $( "#start-game-local" ).click(function() {
      setCookie("boardWidth", document.getElementById("spinner-width").value, 1);
      setCookie("boardHeight", document.getElementById("spinner-height").value, 1);
      setCookie("moves", "", -1);
      window.location = "/local/two-player.php";
    });
    
    $( "#create-as-black" ).click(function() {
      if($("#spinner-height").spinner("value") % 2 == 1 && $("#spinner-width").spinner("value") % 2 == 1) {
        var extra = $("#extra").val() == "Black" ? "b" : "w";
      } else {
        var extra = "n";
      }
      window.location.replace("/online/create-challenge.php?width=" + document.getElementById("spinner-width").value + "&height=" + document.getElementById("spinner-height").value + "&play=b&extra=" + extra);
    });
    $( "#create-as-white" ).click(function() {
      if($("#spinner-height").spinner("value") % 2 == 1 && $("#spinner-width").spinner("value") % 2 == 1) {
        var extra = $("#extra").val() == "Black" ? "b" : "w";
      } else {
        var extra = "n";
      }
      window.location.replace("/online/create-challenge.php?width=" + document.getElementById("spinner-width").value + "&height=" + document.getElementById("spinner-height").value + "&play=w&extra=" + extra);
    });
    $( "#create-as-random" ).click(function() {
      if($("#spinner-height").spinner("value") % 2 == 1 && $("#spinner-width").spinner("value") % 2 == 1) {
        var extra = $("#extra").val() == "Black" ? "b" : "w";
      } else {
        var extra = "n";
      }
      window.location.replace("/online/create-challenge.php?width=" + document.getElementById("spinner-width").value + "&height=" + document.getElementById("spinner-height").value + "&play=" + (Math.random() < 0.5 ? "b" : "w") + "&extra=" + extra);
    });
    
    $( "#extra" ).selectmenu();
  });
  </script>
  <?php
  if(!isset($user) || !$user->isLoggedIn()) {
    echo '<script>$(function() {$( "#radio-online" ).checkboxradio( "disable" );});</script>';
  }
  ?>
</head>

<body>
  <div id="main">
    <h2>New Game</h2>

    <div class="widget">
    <fieldset style="border: 2px solid black; width:500px">
      <legend style="width: auto">Game Settings:</legend>
      <label for="spinner-height">Board size: </label>
      <input id="spinner-height" name="value" class="spinner-size">
      <label for="spinner-width">x</label>
      <input id="spinner-width" name="value" class="spinner-size">
      <br>
      <div id="div-extra" style="display: none;"><label for="extra">Extra piece goes to: </label>
      <select name="extra" id="extra">
        <option>Black</option>
        <option selected="selected">White</option>
      </select></div>
      <br>
      <label for="slider-level" id="label-level">Crumble Level: &#8734</label>
      <div id="slider-level" style="width:300px"></div>
    </fieldset>

    <br>
    <fieldset style="border: 2px solid black; width:500px">
      <label for="radio-local" class="radio-label">Local Game</label>
      <?php
      if(isset($user) && $user->isLoggedIn()) {
      echo '<input type="radio" name="radio-1" id="radio-local">
      <label for="radio-online"class="radio-label">Online Game</label>
      <input type="radio" name="radio-1" id="radio-online" Checked>';
      } else {
      echo '<input type="radio" name="radio-1" id="radio-local" Checked>
      <label for="radio-online"class="radio-label">Online Game</label>
      <input type="radio" name="radio-1" id="radio-online">';
      }
      if(isset($user) && $user->isLoggedIn()) {
      echo '<fieldset style="border: 2px solid black; display: none" id="select-players">';
      } else {
      echo '<fieldset style="border: 2px solid black;" id="select-players">';
      }
      echo '<label for="radio-two-player" class="radio-label">Two Player</label>
        <input type="radio" name="radio-2" id="radio-two-player" Checked>
        <br>
        <label for="radio-single-player" class="radio-label">Single Player</label>
        <input type="radio" name="radio-2" id="radio-single-player">
        <br>
        <button class="ui-button ui-widget ui-corner-all" id="start-game-local" style="position: relative; left: 300px; bottom: 10px">Start Game</button>

      </fieldset>';
      if(isset($user) && $user->isLoggedIn()) {
      echo '<fieldset style="border: 2px solid black;" id="select-challenge">';
      } else {
      echo '<fieldset style="border: 2px solid black; display: none" id="select-challenge">';
      }
      ?>
        <label for="radio-open-challenges" class="radio-label">Open Challenges</label>
        <input type="radio" name="radio-3" id="radio-open-challenges" Checked>
        <br>
        <label for="radio-create-challenge" class="radio-label">Create Challenge</label>
        <input type="radio" name="radio-3" id="radio-create-challenge">
        <div id="div-open-challenges" style="position: relative; left: 180px; bottom: 50px; border: 1px solid black; width: 300px; height: auto">
        <?php
        $query = $db->query("SELECT * FROM game_challenges", array());
        if($db->error())
            file_put_contents ("error_log", "new line 187: ".$db->errorString(), FILE_APPEND);
        $results = $query->results();
        if(count($results) == 0) {
            echo "No open challenges. Your should create one so someone else can accept it.";
        }
        if(isset($user) && $user->isLoggedIn()) {
            foreach($results as $row) {
                echo '<div style="border: 1px solid black">
                '.$row->height.' x '.$row->width;
                if($row->extra != "n") {
                    echo '  Extra piece goes to ';
                    if($row->extra == "w") {
                        echo "white";
                    } else {
                        echo "black";
                    }
                }
                echo '<br>
                vs ';
                $query2 = $db->query("SELECT username FROM users WHERE id = ?", [$row->user_id]);
                if($db->error())
                    file_put_contents ("error_log", "new line 208: ".$db->errorString(), FILE_APPEND);
                echo $query2->first()->username.'<br>';
                if($row->plays == "b") {
                    echo "Play as white";
                } else {
                    echo "Play as black";
                }
                if($row->user_id != $user->data()->id) {
                echo '<label for="radio-accept-'.$row->id.'" class="radio-label">Accept Challenge</label>
                    <input type="radio" name="radio-0" id="radio-accept-'.$row->id.'">
                    <script>$(function() {
                    $( "#radio-accept-'.$row->id.'" ).click(function() {
                      window.location.replace("/online/accept-challenge.php?challenge='.$row->id.'");
                    });
                    });</script>';
                } else {
                echo '<label for="radio-remove-'.$row->id.'" class="radio-label">Remove Challenge</label>
                    <input type="radio" name="radio-0" id="radio-remove-'.$row->id.'">
                    <script>$(function() {
                    $( "#radio-remove-'.$row->id.'" ).click(function() {
                      window.location.replace("/online/remove-challenge.php?id='.$row->id.'");
                    });
                    });</script>';
                }
                
                echo '</div><br>';
            }
        }
        ?>
        </div>
        <div id="div-create-challenge" style="display: none; position: relative; left: 180px; bottom: 50px; border: 1px solid black; width: 300px; height: 150px">
        Uses the game settings above<br>
        <button class="ui-button ui-widget ui-corner-all" id="create-as-black" style="position: relative; top: 5px">Play as black</button>
        <br>
        <button class="ui-button ui-widget ui-corner-all" id="create-as-white" style="position: relative; top: 10px">Play as white</button>
        <br>
        <button class="ui-button ui-widget ui-corner-all" id="create-as-random" style="position: relative; top: 15px">Play as random</button>
        </div>

      </fieldset>
    </fieldset>

    </div>

  </div>
</body>
</html> 

