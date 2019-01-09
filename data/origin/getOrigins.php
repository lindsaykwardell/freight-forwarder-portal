<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $shipper = $_GET['shipper'];

  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $result = $db->getOrigins($shipper);
  if ($result->num_rows > 0) {
    $resultData = array();
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->originShort = $row["originShort"];
      $rowData->originFull = $row["originFull"];
      $rowData->originState = $row["originState"];
      $rowData->originFavorite = $row["originFavorite"];
      $rowData->originStateFavorite = $row["originStateFavorite"];
      $resultData[] = $rowData;
    }
    $json = json_encode($resultData);
    echo $json;
  }
  else {
    $json = json_encode(0);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
