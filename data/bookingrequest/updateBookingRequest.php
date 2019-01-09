<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $id = htmlspecialchars($_POST['id']);
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
  $result = $db->updateBookingRequest($id, $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated);
  $json = json_encode($result);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
