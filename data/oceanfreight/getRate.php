<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $ssco = htmlspecialchars($_GET['ssco']);
  $shipper = htmlspecialchars($_GET['shipper']);

  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $origin = htmlspecialchars($_GET['origin']);
  $destination = htmlspecialchars($_GET['destination']);

  $rates = array();
  $result = $db->getRate($ssco, $shipper, $origin, $destination);

  while($row = $result->fetch_assoc())
  {
    $rowData = new stdClass();

    $rowData->shipper = $shipper;
    $rowData->ssco = $ssco;
    $rowData->rate = $row['oceanfreightRate'];
    $rowData->updated = $row['oceanfreightUpdated'];
    $rates[] = $rowData;
  }
  if (isset($rates[0]->rate))
  {
    $json = json_encode($rates);
  }
  else {
    $json = json_encode(0);
  }
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
