<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if($accountType == 1)
  {
    $shipperShort = htmlspecialchars($_POST['shipperShort']);
    $shipperFull = htmlspecialchars($_POST['shipperFull']);
    $shipperPhone = htmlspecialchars($_POST['shipperPhone']);
    $shipperAddress = htmlspecialchars($_POST['shipperAddress']);
    $shipperNote = htmlspecialchars($_POST['shipperNote']);
    $shipperAssigned = htmlspecialchars($_POST['shipperAssigned']);

    $result = $db->updateShipper($shipperShort, $shipperFull, $shipperPhone, $shipperAddress, $shipperNote, $shipperAssigned);

    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
