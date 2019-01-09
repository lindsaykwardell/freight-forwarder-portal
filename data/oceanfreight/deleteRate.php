<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $shipper = htmlspecialchars($_POST['shipper']);
    $ssco = htmlspecialchars($_POST['ssco']);
    $origin = htmlspecialchars($_POST['origin']);
    $destination = htmlspecialchars($_POST['destination']);

    $result = $db->deleteRate($ssco, $shipper, $origin, $destination);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
