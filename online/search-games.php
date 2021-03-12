<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once '../users/init.php';
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
  <title>Search Games</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#search-button" ).click(function() {
      var username = $("#user1").val();
      window.location = "/previous-games.php?user=" + username;
    });

    $( "#user1" ).selectmenu();
  });
  </script>
</head>

<body>
  <div id="main">
    <h2>Search Games</h2>

    <div id="div-users"><label for="user1">Games played by:</label>
    <select name="user1" id="user1">
      <?php
      $users = fetchAllUsers();
      for($i = 0; $i < count($users); $i++) {
        echo '
        <option>'.$users[$i]->username.'</option>';
      }
      ?>
    </select></div>
    <br>
    <button class="ui-button ui-widget ui-corner-all" id="search-button" style="position: relative; top: 5px">Search</button>

  </div>
</body>
</html>
