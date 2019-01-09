<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $ssco = htmlspecialchars($_POST['ssco']);
    $shipper = htmlspecialchars($_POST['shipper']);

    $result = $db->removeContract($ssco, $shipper);
    if($result === true)
    {
      $result = $db->clearRates($ssco, $shipper);
    }
    else {
      $result = false;
    }
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
