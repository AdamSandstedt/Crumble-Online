<?php
ignore_user_abort(false);
require_once '../../users/init.php'; //make sure this path is correct!
$db = DB::getInstance();
if(!isset($_GET["game"])) {die();}
$query = $db->query('SELECT user_id_black, user_id_white, moves FROM games_current WHERE id = ?', [$_GET["game"]]);
if($db->error()) {
    file_put_contents ("error_log", "get_turn line 6: ".$db->errorString(), FILE_APPEND);
}
$results = $query->first();
if(!isset($results->moves) || !isset($results->user_id_black) || !isset($results->user_id_white)) {
    echo "none";
    die();
}
if($user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white) {
    echo "none";
    die();
}
if($results->moves == "")
    $moves = [];
else
    $moves = explode("\r\n", $results->moves);
if(count($moves) % 2 == 0) {
    echo "b";
} else {
    echo "w";
}
?>