<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $id = htmlspecialchars($_SESSION['user_id']);

  $displayMode = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  //$displayMode = $db->getAccountType($id);

  $json = json_encode($displayMode);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
