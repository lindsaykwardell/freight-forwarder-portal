<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $id = htmlspecialchars($_POST['id']);

  $stmt = $db->conn->prepare("DELETE FROM notification WHERE notificationID = ?");
  $stmt->bind_param('i', $id);
  $result = $stmt->execute();
  if ($result) $db->log("has cleared notification " . $id . ".");

  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
