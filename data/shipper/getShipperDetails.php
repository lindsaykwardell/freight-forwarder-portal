<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $shipper = htmlspecialchars($_GET['shipper']);
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
  }

  $result = $db->getShipperDetails($shipper);

  $shipperData = new stdClass();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $shipperData->Short = $row["shipperShort"];
      $shipperData->Full = $row["shipperFull"];
      $shipperData->Phone = $row["shipperPhone"];
      $shipperData->Address = $row['shipperAddress'];
      $shipperData->Note = $row['shipperNote'];
      $shipperData->AssignedTo = $row['shipperAssignedto'];
    }
  }

  $json = json_encode($shipperData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
