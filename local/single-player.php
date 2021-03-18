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
  <title>Single Player Local Game</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="/assets/js/crumble-game-ai.js"></script>
  <script src="/assets/js/minmax.js"></script>
  <script src="/assets/js/cookies.js"></script>
  <script>
  $( function() {
    $( "input[type='checkbox']" ).checkboxradio();

    $( "#slider-depth" ).slider({
      min: 1,
      max: 6,
      value: parseInt(getCookie("ai-depth")),
      slide: function( event, ui ) {
        var x = ui.value;
        document.getElementById("label-depth").innerHTML = "AI Depth: " + x;
        setCookie("ai-depth", x, 24*7);
      },
    });
    document.getElementById("label-depth").innerHTML = "AI Depth: " + getCookie("ai-depth");
  });
  </script>
</head>

<body>
<div id="main">
  <h2 style="margin-bottom: 10px">Single Player Local Game</h2>

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
    <label for="slider-depth" id="label-depth"></label>
    <div id="slider-depth" style="width:300px"></div>
  </div>
  <br><br>
  </div></td><td style="border:none;">
  <div>
  <table style="float: right;" id="moves-table"> </table>
  </table>
  </div></td></tr></table>

  <script>
    $( function() {
//       var h = parseInt(getParameterByName("height"));
//       var w = parseInt(getParameterByName("width"));
      var h = parseInt(getCookie("boardHeight"));
      var w = parseInt(getCookie("boardWidth"));
      var extra = getCookie("extra");
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

      var cb = new CBoard(h, w, extra);
      var cg = new CGame(cb);

      var AIPlayer = getCookie("ai-player");
      var table = document.getElementById("moves-table");
      var m = getCookie("moves");
      if(m) {
        var moves = m.split("/");
        for(var i = 0; i < moves.length; i++) {
          cg.doMove(moves[i], canvas, icanvas);
          var rows = table.rows;
          var lastRow = rows[rows.length-1];
          if(!lastRow || lastRow.cells.length == 3) {
              lastRow = table.insertRow();
              lastRow.insertCell();
              lastRow.cells[0].innerText = rows.length + ".";
          }
          lastRow.insertCell();
          lastRow.cells[lastRow.cells.length-1].innerText = cg.notation;
        }
        table.rows[Math.floor((cg.historyIndex - 1) / 2)].cells[((cg.historyIndex - 1) % 2) + 1].style.backgroundColor = "yellow";
        if(cg.turn == AIPlayer) {
          cg.action = "done";
        }
      }
      setupGraphics(cg, canvas, icanvas, true, undefined, table, AIPlayer);

      document.getElementById("checkbox-notations").onclick = function() {
        cg.showNotations = $(this).prop('checked');
        cb.draw(canvas, cg.notationMap, cg.showNotations);
      }
    });
  </script>

</div>
</body>
</html>
