<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $data = htmlspecialchars($_POST['content']);
  $log = fopen("../log.txt", "a");
  fwrite($log, date("Y/m/d h:i:sa") . " - " . $_SESSION['username'] . "  received a notifcation: " . $data . "\n");
  fclose($log);

  echo json_encode(true);
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
