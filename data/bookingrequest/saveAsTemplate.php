<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $shipper = htmlspecialchars($_POST['shipper']);

  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $ssco = htmlspecialchars($_POST['ssco']);
  $cntrCount = htmlspecialchars($_POST['cntrCount']);
  $cntrType = htmlspecialchars($_POST['cntrType']);
  $consignee = htmlspecialchars($_POST['consignee']);
  $origin = htmlspecialchars($_POST['origin']);
  $destination = htmlspecialchars($_POST['destination']);
  $date = htmlspecialchars($_POST['date']);
  $dateType = htmlspecialchars($_POST['dateType']);

  $name = "";
  if (count($consignee) > 0) {
    $name = $consignee . "/";
  }

  $name = $name . $ssco . " - " . $cntrCount . " " . $cntrType . " " . $origin . "/" . $destination;

  // date_default_timezone_set('America/Los_Angeles');
  // $date = date('Y-m-d');

  // Add new booking
  $result = $db->saveBookingTemplate($name, $shipper, $ssco, $cntrCount, $cntrType, $consignee, $origin, $destination, $date, $dateType);
  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
