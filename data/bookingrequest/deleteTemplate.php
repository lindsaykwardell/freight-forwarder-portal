<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $id = htmlspecialchars($_POST['id']);

  $result = $db->deleteBookingTemplate($id);

  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
