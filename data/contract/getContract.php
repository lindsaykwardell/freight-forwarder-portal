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

  $result = $db->getSSCO($ssco);

  $rowData = new stdClass();
  while($row = $result->fetch_assoc())
  {
    $rowData->sscoFull = $row['sscoFull'];

    $resultContract = $db->getContract($shipper, $ssco);
    while($contract = $resultContract->fetch_assoc())
    {
      $rowData->contractNumber = $contract['contractNumber'];
      if (isset($_GET['method']) && $_GET['method'] == 'raw')
      {
        $rowData->contractStart = $contract['contractStart'];
        $rowData->contractEnd = $contract['contractEnd'];
      } else {
        if ($contract['contractStart'] > 0) {
          $rowData->contractStart = date_format(date_create($contract['contractStart']), 'M d Y');
        } else {
          $rowData->contractStart = "";
        }
        if ($contract['contractEnd'] > 0) {
          $rowData->contractEnd = date_format(date_create($contract['contractEnd']), 'M d Y');
        } else {
          $rowData->contractEnd = "";
        }
      }
    }
  }
  $json = json_encode($rowData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
