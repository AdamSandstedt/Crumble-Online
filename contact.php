<!DOCTYPE html>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once 'users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

if(!empty($_POST)){
$response = preProcessForm();
if($response['form_valid'] == true){
postProcessForm($response);
$ticket_id = $db->query("SELECT id FROM support ORDER BY id DESC")->first()->id;
if($db->error())
    file_put_contents ("error_log", "contact line 13: ".$db->errorString(), FILE_APPEND);
$email_subject = "Crumble Ticket: ".$ticket_id;
$email_body = "Email:<br>".$response['fields']['email']."<br><br>Message:<br>".$response['fields']['message'];
email("crumble.online.contact@gmail.com", $email_subject, $email_body);
header( "Location: /contact-success.php" );
}
}

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
<?php
displayForm('support');
if(isset($user) && $user->isLoggedIn()) {
    echo '<script>
    document.getElementById("email").value = "'.$user->data()->email.'";
    </script>';
}
?>
</div>
</body>

</html> 

