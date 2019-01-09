<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  // Should set a rule checking if it's your account OR if you're level 1.
  $id = htmlspecialchars($_POST['id']);
  $lastUpdated = date('Y-m-d H:i:s');
  
  $result = $db->deleteBookingRequest($id, $lastUpdated);
  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
