<?php
require_once '../../users/init.php';  //make sure this path is correct!
$db = DB::getInstance();
if (!securePage($_SERVER['PHP_SELF'])){die();}
$re = "/^\d+(,\d+)*[VHJ]([NESW]*|[NESW]-[NESW]+|\d+|\d+-\d+[NESW]-[NESW]+|\d+(,\d+)[NESW]*)$/";
$move = Input::get("move");
// file_put_contents ("temp.txt", "$move\n", FILE_APPEND);

// if(!$move || $move != "done" && !preg_match($re, $move)) {die();}
if(!$move || !preg_match($re, $move)) {die();}
$game = Input::get("game");
// file_put_contents ("temp.txt", "$move, $game\n", FILE_APPEND);

$query = $db->query('SELECT * FROM games_current WHERE id = ?', [$game]);
if($db->error()) {
    file_put_contents ("error_log", "submit-move line 20: ".$db->errorString(), FILE_APPEND);
    die();
}
if($db->count() == 0) {
    die();
}
$results = $query->first();
if($user->data()->id != $results->user_id_black && $user->data()->id != $results->user_id_white){die();}
if($results->moves == '')
    $moves = [];
else
    $moves = explode("\r\n", $results->moves);
// if($results->user_id_black == $user->data()->id && count($moves) % 2 == 1 && $move != "done") {die();}
// if($results->user_id_white == $user->data()->id && count($moves) % 2 == 0 && $move != "done") {die();}
if($results->user_id_black == $user->data()->id && count($moves) % 2 == 1) {die();}
if($results->user_id_white == $user->data()->id && count($moves) % 2 == 0) {die();}
$time = Input::get("time");
// file_put_contents ("temp.txt", "$move, $game, $time\n", FILE_APPEND);

if(isset($time)) {
    $time = intval($time);
} else {
    $time = time();
}
if($results->moves == '') {
    // if($move == "done") {
    //     die();
    // } else {
    $db->query('UPDATE games_current SET moves = ?, time = ? WHERE games_current.id = ?', [$move, $time, $game]);
    // }
    if($db->error()) {
        file_put_contents ("error_log", "submit-move line 39: ".$db->errorString(), FILE_APPEND);
        die();
    }
    if($db->count() == 0) {
        file_put_contents ("error_log", "0 rows affect by update on line 39 of submit-move", FILE_APPEND);
        die();
    }
} else {
    if($results->time_black && $results->time_white) {
        $timeb = $results->time_black;
        $timew = $results->time_white;
        if(count($moves) % 2 == 0) {
            $timeb += $results->time - $time;
            if($timeb < 0)
                $timeb = 0;
        } else {
            $timew += $results->time - $time;
            if($timew < 0)
                $timew = 0;
        }
        // if($move == "done") {
        //     if($timeb == 0 || $timew == 0)
        //         $db->query('UPDATE games_current SET time = ?, time_black = ?, time_white = ? WHERE games_current.id = ?', [$time, $timeb, $timew, $game]);
        //     else
        //         die();
        // } else {
        $db->query('UPDATE games_current SET moves = CONCAT(moves, ?), time = ?, time_black = ?, time_white = ? WHERE games_current.id = ?', ["\r\n".$move, $time, $timeb, $timew, $game]);
        // }
        if($db->error()) {
            file_put_contents ("error_log", "submit-move line 61: ".$db->errorString(), FILE_APPEND);
            die();
        }
        if($db->count() == 0) {
            file_put_contents ("error_log", "0 rows affect by update on line 61 of submit-move", FILE_APPEND);
            die();
        }
    } else {
        // if($move == "done") {
        //     die();
        // } else {
        $db->query('UPDATE games_current SET moves = CONCAT(moves, ?), time = ? WHERE games_current.id = ?', ["\r\n".$move, $time, $game]);
        // }
        if($db->error()) {
            file_put_contents ("error_log", "submit-move line 71: ".$db->errorString(), FILE_APPEND);
            die();
        }
        if($db->count() == 0) {
            file_put_contents ("error_log", "0 rows affect by update on line 71 of submit-move", FILE_APPEND);
            die();
        }
    }
}
$win = Input::get("win");
if(isset($timeb) && isset($timew)) {
    if($timeb == 0)
        $win = "w";
    else if($timew == 0)
        $win = "b";
    // else if($move == "done")
    //     $win = "";
}
// file_put_contents ("temp.txt", "$move, $game, $time, $win\n\n", FILE_APPEND);
if($win == "b" || $win == "w" || $win == "d") {
    $query = $db->query('SELECT * FROM games_current WHERE id = ?', [$game]);
    if($db->error()) {
        file_put_contents ("error_log", "submit-move line 83: ".$db->errorString(), FILE_APPEND);
        die();
    }
    $results = $query->first();
    $db->query('INSERT INTO games_completed (id, user_id_black, user_id_white, width, height, moves, extra, winner, end_time, time_black, time_white) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);', [$results->id, $results->user_id_black, $results->user_id_white, $results->width, $results->height, $results->moves, $results->extra, $win, $results->time, $results->time_black, $results->time_white]);
    if($db->error()) {
        file_put_contents ("error_log", "submit-move line 89: ".$db->errorString(), FILE_APPEND);
        die();
    }
    if($db->count() == 0) {
        file_put_contents ("error_log", "0 rows affect by insert on line 89 of submit-move", FILE_APPEND);
        die();
    }
    $db->query('DELETE FROM games_current WHERE id = ?', [$results->id]);
    if($db->error()) {
        file_put_contents ("error_log", "submit-move line 98: ".$db->errorString(), FILE_APPEND);
        die();
    }
    if($db->count() == 0) {
        file_put_contents ("error_log", "0 rows affect by delete on line 98 of submit-move", FILE_APPEND);
        die();
    }
    echo '<script>
    window.location.replace("/online/view-game.php?game='.$game.'");
    </script>';
    die();
}
?>