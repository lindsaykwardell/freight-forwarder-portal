<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

include "../../php.php";

sec_session_start();

if(login_check($db))
{

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 */
function sendMsg($msg) {
  echo "id: UPDATE" . PHP_EOL;
  echo "data: $msg" . PHP_EOL;
  echo PHP_EOL;
  ob_flush();
  flush();
  sleep(60 * 5);
}
$accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));

$notify = array();

$end = date('Y-m-d H:i:s');

$start = strtotime($end);
$start = $start-(60*5);
$start = date("Y-m-d H:i:s", $start);

$stmt;
if ($accountType == 1) {
  $stmt = $db->conn->prepare("SELECT * FROM bookingrequest WHERE lastUpdated BETWEEN ? AND ?");
  $stmt->bind_param('ss', $start, $end);
} elseif ($accountType == 2) {
  $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
  $shipper = $db->getShipperShortByID($shipperID);
  $stmt = $db->conn->prepare("SELECT * FROM bookingrequest WHERE lastUpdated BETWEEN ? AND ? AND bookingrequestShipper = ?");
  $stmt->bind_param('sss', $start, $end, $shipper);
}

$stmt->execute();
$result = $stmt->get_result();

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

  $notify[] = $rowData;
}

for ($i=0; $i < count($notify); $i++) {
  $msg = json_encode($notify);
  sendMsg($msg);
}

} else {
  header("HTTP/1.0 404 Not Found");
}
?>
