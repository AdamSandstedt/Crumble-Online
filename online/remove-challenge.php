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
if(!isset($_GET["id"])){go_back();}
$id = intval($_GET["id"]);
$query = $db->query("SELECT * from game_challenges where id = ?;", [$id]);
if($db->error()) {
    file_put_contents ("error_log", "remove-challenge line 13: ".$db->errorString(), FILE_APPEND);
    go_back();
}
$results = $query->first();
if($results->user_id != $user->data()->id) {go_back();}
$db->query("DELETE FROM game_challenges where id = ?;", [$id]);
if($db->error()) {
    file_put_contents ("error_log", "remove-challenge line 20: ".$db->errorString(), FILE_APPEND);
    go_back();
}
if($db->count() == 0) {
    file_put_contents ("error_log", "0 rows affect by delete on line 20 of remove-challenge", FILE_APPEND);
    go_back();
}
go_back();
?>