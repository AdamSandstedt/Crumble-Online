<?php
require_once '../users/init.php';  //make sure this path is correct!
$db = DB::getInstance();
function go_back() {
    if(isset($_GET["challenge"])) {
        echo '<script>
            window.location.replace("/new.php");
        </script>';
    } else {
        header('Location: /index.php');
        echo '<script>
            window.location.replace("/index.php");
        </script>';
    }
    die();
}
if(!isset($_GET["challenge"])) {go_back();}
if (!securePage($_SERVER['PHP_SELF'])){go_back();}
$query = $db->query('SELECT * FROM game_challenges WHERE id = ?', [$_GET["challenge"]]);
if($db->error()) {
    file_put_contents ("error_log", "accept-challenge line 19: ".$db->errorString(), FILE_APPEND);
    go_back();
}
$results = $query->first();
if($user->data()->id == $results->user_id) {go_back();}
$db->query('INSERT INTO games_current (id, user_id_black, user_id_white, width, height, moves, extra) VALUES (NULL, ?, ?, ?, ?, \'\', ?);', [($results->plays == 'b' ? $results->user_id : $user->data()->id), ($results->plays == 'w' ? $results->user_id : $user->data()->id), $results->width, $results->height, $results->extra]);
if($db->error()) {
    file_put_contents ("error_log", "accept-challenge line 26: ".$db->errorString(), FILE_APPEND);
    go_back();
}
if($db->count() == 0) {
    file_put_contents ("error_log", "0 rows affect by insert on line 26 of accept-challenge", FILE_APPEND);
    go_back();
}

$db->query('DELETE FROM game_challenges WHERE id = ?', [$_GET["challenge"]]);
if($db->error()) {
    file_put_contents ("error_log", "accept-challenge line 36: ".$db->errorString(), FILE_APPEND);
    go_back();
}
if($db->count() == 0) {
    file_put_contents ("error_log", "0 rows affect by delete on line 36 of accept-challenge", FILE_APPEND);
    go_back();
}

$query = $db->query('SELECT * FROM games_current WHERE user_id_'.($results->plays == 'b' ? 'white' : 'black').' = '.$user->data()->id.' ORDER BY id DESC');
if($db->error()) {
    file_put_contents ("error_log", "accept-challenge line 46: ".$db->errorString(), FILE_APPEND);
    go_back();
}
echo '<script>
window.location.replace("/online/play.php?game='.$query->first()->id.'");
</script>';
?>