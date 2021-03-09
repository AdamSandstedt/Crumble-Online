<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once '../users/init.php';  //make sure this path is correct!
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}
?>

<style>
body {
  font-family: "Lato", sans-serif;
}

#main {
  transition: margin-left .5s;
  padding-left: 8px;
}
</style>

<head>
  <title>Two Player Local Game</title>
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
<div id="main">
  <h2 style="margin-bottom: 10px">Two Player Local Game</h2>

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
  </div>
  <br><br>

  <script>
    $( function() {
//       var h = parseInt(getParameterByName("height"));
//       var w = parseInt(getParameterByName("width"));
      var h = parseInt(getCookie("boardHeight"));
      var w = parseInt(getCookie("boardWidth"));
      if(!h || !w) {
        console.log('"boardHeight" and "boardWidth" cookies not found');
        return;
      }

      const pixelDensity = 128;
      var canvas = document.getElementById('crumble-canvas');
      canvas.width = pixelDensity*w;
      canvas.height = pixelDensity*h;
      var icanvas = document.getElementById('interactive-canvas');
      icanvas.width = pixelDensity*w;
      icanvas.height = pixelDensity*h;

      var cb = new CBoard(h, w);
      var cg = new CGame(cb);
      setupGraphics(cg, canvas, icanvas, true, false);
      var m = getCookie("moves");
      if(m) {
        var moves = m.split("/");
        for(var i = 0; i < moves.length; i++) {
          cg.doMove(moves[i], canvas, icanvas);
        }
      }

      document.getElementById("checkbox-notations").onclick = function() {
        cg.showNotations = $(this).prop('checked');
        cb.draw(canvas, cg.notationMap, cg.showNotations);
      }
    });
  </script>

</div>
</body>
</html> 

