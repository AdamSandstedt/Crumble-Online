<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
if(file_exists("install/index.php")) {
    //perform redirect if installer files exist
    //this if{} block may be deleted once installed
    header("Location: install/index.php");
}

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
</style>

<head>
  <title>Crumble</title>
</head>

<body>
<div id="main">
  <img src="crumble_game.jpg">
</div>
</body>

<script>
openNav()
</script>
</html> 

