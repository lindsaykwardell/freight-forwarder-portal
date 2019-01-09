<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $ssco = htmlspecialchars($_GET['ssco']);
  $shipper = htmlspecialchars($_GET['shipper']);

  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $originJson = $_GET['origins'];
  $origins = json_decode($originJson, true);

  $destinationJson = $_GET['destinations'];
  $destinations = json_decode($destinationJson, true);

  $rates = array();
  $sorted = array();
  $result = $db->getRates($ssco, $shipper);

  while($row = $result->fetch_assoc())
    {
      $rowData = new stdClass();

      $rowData->origin = $row['oceanfreightOrigin'];
      $rowData->destination = $row['oceanfreightDestination'];
      $rowData->rate = $row['oceanfreightRate'];
      $rowData->updated = date_format(date_create($row['oceanfreightUpdated']), 'M d Y');
      $rowData->updatedBy = $row['oceanfreightUpdatedBy'];

      $rates[] = $rowData;
    }

  foreach ($origins as $origin) {
    foreach ($destinations as $destination) {
      foreach($rates as $rate) {
        if($rate->origin == $origin && $rate->destination == $destination) {
          $sorted[] = $rate;
        }
      }
    }
  }

  $json = json_encode($rates);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
