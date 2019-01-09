<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $resultData = array();
  if (isset($_GET['shipper']) || $accountType == 2)
  {
    $shipper = htmlspecialchars($_GET['shipper']);
    if ($accountType == 2) {
      $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
      $shipper = $db->getShipperShortByID($shipperID);
    }

    $result = $db->getContractList($shipper);
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $rowData = new stdClass();
        $rowData->contractShipper = $row['contractShipper'];
        $rowData->contractSsco = $row['contractSsco'];
        $rowData->contractNumber = $row['contractNumber'];
        if ($row['contractStart'] > 0) {
          $rowData->contractStart = date_format(date_create($row['contractStart']), 'M d Y');
        } else {
          $rowData->contractStart = "";
        }
        if ($row['contractEnd'] > 0) {
          $rowData->contractEnd = date_format(date_create($row['contractEnd']), 'M d Y');
        } else {
          $rowData->contractEnd = "";
        }
        $resultData[] = $rowData;
      }
    }
  }
  else
  {
    if ($accountType == 1) {
      if (isset($_GET['ssco'])) {
        $ssco = htmlspecialchars($_GET['ssco']);
        $result = $db->getContractListForSsco($ssco);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $rowData = new stdClass();
            $rowData->contractShipper = $row['contractShipper'];
            $rowData->contractSsco = $row['contractSsco'];
            $rowData->contractNumber = $row['contractNumber'];
            if ($row['contractStart'] > 0) {
              $rowData->contractStart = date_format(date_create($row['contractStart']), 'M d Y');
            } else {
              $rowData->contractStart = "";
            }
            if ($row['contractEnd'] > 0) {
              $rowData->contractEnd = date_format(date_create($row['contractEnd']), 'M d Y');
            } else {
              $rowData->contractEnd = "";
            }
            $resultData[] = $rowData;
          }
        }
      } else {
        $shipper = "0";
        $result = $db->getContractList($shipper);
        if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
            $rowData = new stdClass();
            $rowData->contractShipper = $row['contractShipper'];
            $rowData->contractSsco = $row['contractSsco'];
            $rowData->contractNumber = $row['contractNumber'];
            if ($row['contractStart'] > 0) {
              $rowData->contractStart = date_format(date_create($row['contractStart']), 'M d Y');
            } else {
              $rowData->contractStart = "";
            }
            if ($row['contractEnd'] > 0) {
              $rowData->contractEnd = date_format(date_create($row['contractEnd']), 'M d Y');
            } else {
              $rowData->contractEnd = "";
            }
            $resultData[] = $rowData;
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
