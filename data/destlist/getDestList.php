<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $result = $db->getDestList();
  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->destinationShort = $row["destListShort"];
      $rowData->destinationFull = $row["destListFull"];
      $resultData[] = $rowData;
    }
  }
  if (isset($_GET['shipper']))
  {
    $shipper = htmlspecialchars($_GET['shipper']);

    $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
    if ($accountType == 2) {
      $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
      $shipper = $db->getShipperShortByID($shipperID);
    }

    $result = $db->getDestinations($shipper);
    $existingDestinations = array();
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $rowData = new stdClass();
        $rowData->destinationShort = $row["destinationShort"];
        $existingDestinations[] = $rowData;
      }
    }

    foreach ($existingDestinations as $existing) {
      for ($i=0; $i < count($resultData); $i++) {
        if ($existing->destinationShort == $resultData[$i]->destinationShort) {
          array_splice($resultData, $i, 1);
        }
      }
    }
  }
  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
