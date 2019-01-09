<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $destination = htmlspecialchars($_POST['destination']);
    $shipper = htmlspecialchars($_POST['shipper']);

    $result = $db->removeDestination($shipper, $destination);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
