<?php
require_once '../../users/init.php'; //make sure this path is correct!
$db = DB::getInstance();
$game = Input::get("game");
if(!$game) {die();}
$query = $db->query('SELECT user_id_black, user_id_white, time, time_black, time_white FROM games_current WHERE id = ?', [$game]);
if($db->error()) {
    file_put_contents ("error_log", "get_times line 6: ".$db->errorString(), FILE_APPEND);
}
$results = $query->first();
if(!isset($results->time) || !isset($results->user_id_black) || !isset($results->user_id_white)) {
    die();
}
if($user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white) {
    die();
}
echo $results->time." ".$results->time_black." ".$results->time_white;
?>