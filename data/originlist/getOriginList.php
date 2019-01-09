<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $result = $db->getOriginList();
  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->originShort = $row["originListShort"];
      $rowData->originFull = $row["originListFull"];
      $rowData->originState = $row["originListState"];
      $resultData[] = $rowData;
    }
  }
  if(isset($_GET['shipper']))
  {
    $shipper = htmlspecialchars($_GET['shipper']);

    $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
    if ($accountType == 2) {
      $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
      $shipper = $db->getShipperShortByID($shipperID);
    }

    $result = $db->getOrigins($shipper);
    $existingOrigins = array();
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $rowData = new stdClass();
        $rowData->originShort = $row["originShort"];
        $existingOrigins[] = $rowData;
      }
    }
    if(count($existingOrigins) >= 5)
    {
      $resultData = "LimitReached";
    }
    else {
      foreach ($existingOrigins as $existing) {
        for ($i=0; $i < count($resultData); $i++) {
          if ($existing->originShort == $resultData[$i]->originShort) {
            array_splice($resultData, $i, 1);
          }
        }
      }
    }
  }
  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
