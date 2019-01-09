<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $table = htmlspecialchars($_POST['table']);

  $stmt = $db->conn->prepare("SELECT COUNT(*) AS tableCount FROM ?");
  $stmt->bind_param('s', $table);
  $stmt->execute();
  $result = $stmt->get_result();

  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
