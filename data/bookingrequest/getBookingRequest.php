<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $id = htmlspecialchars($_GET['id']);

  $rowData = new stdClass();
  $result = $db->getBookingRequest($id);
  while($row = $result->fetch_assoc())
  {
    $rowData->reqDate = date_format(date_create($row['bookingrequestDateRequested']), 'n/j');
    if ($accountType == 1) $rowData->reqDate = date_format(date_create($row['bookingrequestDateRequested']), 'n/j g:ia');
    $rowData->requestID = $row['bookingrequestID'];
    $rowData->Status = $row['bookingrequestType'];
    $rowData->BookingNumber = $row['bookingrequestNumber'];
    $rowData->AssignedTo = $row['assignedTo'];
    $rowData->Shipper = $row['bookingrequestShipper'];
    $rowData->Ref = $row['bookingrequestRef'];
    $rowData->Ssco = $row['bookingrequestSsco'];
    $rowData->Consignee =$row['bookingrequestConsignee'];
    $rowData->CntrCount = $row['bookingrequestCntrCount'];
    $rowData->CntrType = $row['bookingrequestCntrType'];
    $rowData->Origin = $row['bookingrequestOrigin'];
    $rowData->Destination = $row['bookingrequestDestination'];
    $rowData->DateType = $row['bookingrequestDateType'];
    $rowData->Date = date_format(date_create($row['bookingrequestDate']), 'n/j');
    $rowData->DateRange = date_format(date_create($row['bookingrequestDateRange']), 'n/j');
    if ($row['bookingrequestDate'] == 0) $rowData->Date = "";
    if ($row['bookingrequestDateRange'] == 0) $rowData->DateRange = "";
    $rowData->Product = $row['bookingrequestProduct'];
    $rowData->Notes = $row['bookingrequestNotes'];

    if ($accountType != 1 && ($rowData->Status == 'NEW' || $rowData->Status == 'REQ' || $rowData->Status == 'MOD'))
    {
      $rowData->BookingNumber = "";
    }

  }
  $json = json_encode($rowData);
  echo $json;
}

?>
