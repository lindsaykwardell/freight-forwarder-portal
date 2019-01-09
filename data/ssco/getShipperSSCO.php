<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $shipper = htmlspecialchars($_GET['shipper']);

  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $result = $db->getShipperSSCO($shipper);
  if ($result->num_rows > 0) {
    $resultData = array();
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->sscoShort = $row['sscoShort'];
      $rowData->sscoFull = $row['sscoFull'];
      $resultData[] = $rowData;
    }
    $json = json_encode($resultData);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
