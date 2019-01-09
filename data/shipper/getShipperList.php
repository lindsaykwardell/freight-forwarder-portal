<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $stmt;
  if($accountType == 1)
  {
    $stmt = $db->conn->prepare("SELECT shipperShort, shipperFull FROM shipper ORDER BY shipperFull");
  } elseif ($accountType == 2) {
    $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
    $stmt = $db->conn->prepare("SELECT shipperShort, shipperFull FROM shipper WHERE shipperID = ? ORDER BY shipperFull");
    $stmt->bind_param('i', $shipperID);
  }

  $stmt->execute();
  $result = $stmt->get_result();

  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->shipperShort = $row["shipperShort"];
      $rowData->shipperFull = $row["shipperFull"];
      $resultData[] = $rowData;
    }
  }

  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
