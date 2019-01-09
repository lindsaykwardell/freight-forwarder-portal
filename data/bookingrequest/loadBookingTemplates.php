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

  $templates = array();

  $result = $db->loadBookingTemplates($shipper);
  while($row = $result->fetch_assoc())
  {
    $rowData = new stdClass();

    $rowData->id = $row['templateID'];
    $rowData->Name = $row['templateName'];

    $templates[] = $rowData;
  }

  $json = json_encode($templates);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
