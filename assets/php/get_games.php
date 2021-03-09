<?php
ignore_user_abort(false);
require_once '../../users/init.php'; //make sure this path is correct!
$db = DB::getInstance();
if(!isset($user) || !$user->isLoggedIn()) {die();}
$query = $db->query('SELECT user_id_black, user_id_white, moves FROM games_current WHERE user_id_black = ? OR user_id_white = ?', [$user->data()->id, $user->data()->id]);
if($db->error()) {
    file_put_contents ("error_log", "get_games line 6: ".$db->errorString(), FILE_APPEND);
}
$results = $query->results();
$your_turn = 0;
$opponents_turn = 0;
foreach($results as $row) {
    if($row->moves == "")
        $num_moves = 0;
    else
        $num_moves = count(explode("\r\n", $row->moves));
    if($num_moves % 2 == 0 && $row->user_id_black == $user->data()->id || $num_moves % 2 == 1 && $row->user_id_white == $user->data()->id)
        $your_turn++;
    else
        $opponents_turn++;
}
echo $your_turn." ".$opponents_turn;
?>