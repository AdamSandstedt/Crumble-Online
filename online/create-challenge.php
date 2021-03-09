<?php
require_once '../users/init.php';  //make sure this path is correct!
$db = DB::getInstance();
function go_back() {
    echo '<script>
        window.location.replace("/new.php");
    </script>';
    die();
}
if (!securePage($_SERVER['PHP_SELF'])){go_back();}
if(!isset($_GET["width"]) || !isset($_GET["height"]) || !isset($_GET["play"]) || !isset($_GET["extra"])){go_back();}
$h = intval($_GET["height"]);
$w = intval($_GET["width"]);
$extra = $_GET["extra"];
if(strlen($extra) != 1) {go_back();}
if($h < 2 || $w < 2) {go_back();}
if($_GET["play"] != "b" && $_GET["play"] != "w") {go_back();}
$db->query("INSERT INTO game_challenges (id, width, height, user_id, plays, extra) VALUES (NULL, ?, ?, ?, ?, ?);", [$w, $h, $user->data()->id, $_GET["play"], $extra]);
if($db->error()) {
    file_put_contents ("error_log", "create-challenge line 18: ".$db->errorString(), FILE_APPEND);
    go_back();
}
if($db->count() == 0) {
    file_put_contents ("error_log", "0 rows affect by insert on line 18 of create-challenge", FILE_APPEND);
    go_back();
}
go_back();
?>