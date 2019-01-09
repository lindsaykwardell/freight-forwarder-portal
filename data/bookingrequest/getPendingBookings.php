<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $key;
  $method;
  $bookings = array();

  $range = htmlspecialchars($_GET['range']);
  $range = explode(' - ', $range);
  $range[0] = date('Y-m-d',strtotime($range[0]));
  $range[1] = date('Y-m-d',strtotime($range[1]));

  // Hack to allow shippers to see all pending requests.
  // Range should only apply to shippers for viewing closed
  // requests (to cut down on DB calls).
  if($accountType == 2 && !isset($_GET['getCompleted'])) {
    $range[0] = date('Y-m-d', strtotime($range[0] . ' - 2 years'));
    $range[1] = date('Y-m-d', strtotime($range[1] . ' + 2 years'));
  }

  if ($accountType == 1) {
    if(isset($_GET['shipper']))
    {
      $key = htmlspecialchars($_GET['shipper']);
      $method = 'shipper';
    } elseif (isset($_GET['username'])) {
      $key = htmlspecialchars($_GET['username']);
      $method = 'username';
    } elseif (isset($_GET['deleted'])) {
      $key = 'DELETED';
      $method = 'deleted';
    } else {
      $key = false;
      $method = 'all';
    }
  } elseif ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $key = $db->getShipperShortByID($shipperID);
    $method = 'shipper';
  }
  $result;
  if (isset($_GET['getCompleted']) && htmlspecialchars($_GET['getCompleted']) == true) {
    $result = $db->getPendingBookings($key, $method, true, $range[0], $range[1]);
  } else {
    $result = $db->getPendingBookings($key, $method, false, $range[0], $range[1]);
  }
  while($row = $result->fetch_assoc())
  {
    $rowData = new stdClass();

    $rowData->id = $row['bookingrequestID'];
    $rowData->Status = $row['bookingrequestType'];
    $rowData->AssignedTo = $row['assignedTo'];
    $rowData->DateRequested = date_format(date_create($row['bookingrequestDateRequested']), 'n/d');
    $rowData->Shipper = $row['bookingrequestShipper'];
    $rowData->Ref = $row['bookingrequestRef'];
    $rowData->Ssco = $row['bookingrequestSsco'];
    $rowData->Consignee = $row['bookingrequestConsignee'];
    $rowData->CntrCount = $row['bookingrequestCntrCount'];
    $rowData->CntrType = $row['bookingrequestCntrType'];
    $rowData->Origin = $row['bookingrequestOrigin'];
    $rowData->Destination = $row['bookingrequestDestination'];
    $rowData->DateType = $row['bookingrequestDateType'];
    // Convert old flag to new type. Probably not needed in final version.
    if ($row['bookingrequestDateType'] == 'Cutoff') $rowData->DateType = "CUT";
    $rowData->Date = date_format(date_create($row['bookingrequestDate']), 'n/d');
    $rowData->DateRange = date_format(date_create($row['bookingrequestDateRange']), 'n/d');
    if ($row['bookingrequestDate'] == 0) $rowData->Date = "";
    if ($row['bookingrequestDateRange'] == 0) $rowData->DateRange = "";
    $rowData->Notes = $row['bookingrequestNotes'];
    $rowData->Product = $row['bookingrequestProduct'];
    $rowData->BookingNumber = $row['bookingrequestNumber'];
    $rowData->LatestComment = $db->getLatestBkgReqComment($row['bookingrequestID']);

    $isRead = $row['bookingrequestIsRead'];
    if ($_SESSION['username'] == $row['assignedTo']) {
      $rowData->IsRead = $isRead;
    } else {
      $rowData->IsRead = 1;
    }


    if($accountType != 1){
      $rowData->LatestComment = "";
      $rowData->Shipper = "";
    }

    if ($accountType != 1 && ($rowData->Status == 'NEW' || $rowData->Status == 'REQ' || $rowData->Status == 'MOD'))
    {
      $rowData->BookingNumber = "";
    }

    $bookings[] = $rowData;
  }

  $json = json_encode($bookings);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
