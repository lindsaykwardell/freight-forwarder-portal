<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $requested = date('Y-m-d H:i:s');
  $assignedTo = htmlspecialchars($_SESSION['username']);
  $shipper = htmlspecialchars($_POST['shipper']);
  if ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $shipper = $db->getShipperShortByID($shipperID);
    $stmt = $db->conn->prepare("SELECT shipperAssignedTo FROM shipper WHERE shipperShort = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignedToID;
    while($row = $result->fetch_assoc())
    {
      $assignedToID = $row['shipperAssignedTo'];
    }
    $assignedTo = $db->getUsername($assignedToID);
  }
  $ssco = htmlspecialchars($_POST['ssco']);
  $consignee = htmlspecialchars($_POST['consignee']);
  $cntrCount = htmlspecialchars($_POST['cntrCount']);
  $cntrType = htmlspecialchars($_POST['cntrType']);
  $refNum = htmlspecialchars($_POST['refNum']);
  $origin = htmlspecialchars($_POST['origin']);
  $destination = htmlspecialchars($_POST['destination']);
  $dateType = htmlspecialchars($_POST['dateType']);

  $date = htmlspecialchars($_POST['date']);
  $dates = explode(' - ', $date);
  $date = date('Y-m-d',strtotime($dates[0]));
  $dateRange = 0;
  if(isset($dates[1])) $dateRange = date('Y-m-d',strtotime($dates[1]));

  $product = htmlspecialchars($_POST['product']);
  $notes = htmlspecialchars($_POST['notes']);

  $lastUpdated = date('Y-m-d H:i:s');

  // Add new booking
  $result = $db->addBookingRequest($requested, $assignedTo, $shipper, $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated);
  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
