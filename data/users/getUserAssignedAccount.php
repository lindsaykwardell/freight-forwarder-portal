<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $uid = htmlspecialchars($_GET['id']);
    $shipper = $db->getUserAssignedAccout($uid);
    $shipperShort = $db->getShipperShortByID($shipper);

    $json = json_encode($shipperShort);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
