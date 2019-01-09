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

  $origin = htmlspecialchars($_GET['origin']);
  $destination = htmlspecialchars($_GET['destination']);
  $saveSearch = filter_var($_GET['saveSearch'], FILTER_VALIDATE_BOOLEAN);

  $destinations = array();

  $stmt = $db->conn->prepare("SELECT destinationShort, destinationFull FROM destination WHERE destinationShort = ?");
  $stmt->bind_param('s', $destination);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc())
  {
    $rowData = new stdClass();
    $rowData->destinationShort = $row["destinationShort"];
    $rowData->destinationFull = $row["destinationFull"];
    $destinations[] = $rowData;
  }

  $resultData = array();

  if ($origin == "All")
  {
    $result = $db->getOrigins($shipper);
    if ($result->num_rows > 0) {
      $origins = array();
      while($row = $result->fetch_assoc()) {
        $rowData = new stdClass();
        $rowData->originShort = $row["originShort"];
        $rowData->originFull = $row["originFull"];
        $origins[] = $rowData;
      }
    }
    for ($i=0; $i < count($origins); $i++) {
      $result = $db->getShipperSSCO($shipper);
      while($row = $result->fetch_assoc()) {
        $ssco = $row['sscoShort'];
        $perContainer = $row['sscoPerContainer'];
        $resultRate = $db->getRate($ssco, $shipper, $origins[$i]->originShort, $destination);

        while($rateData = $resultRate->fetch_assoc())
        {
            $rowData = new stdClass();
            $rowData->ssco = $ssco;
            $rowData->origin = $origins[$i]->originFull;
            $rowData->destination = $destinations[0]->destinationFull;
            $rowData->rate = $rateData['oceanfreightRate'] + $perContainer;
            $resultData[] = $rowData;
        }
      }
    }
  }
  else if (strpos(htmlspecialchars($_GET['origin']), 'STATE') !== false)
  {
    $origin = str_replace("STATE-","", htmlspecialchars($_GET['origin']));

    if($saveSearch) {
      $result = $db->saveSearch($shipper, $origin, true, $destination);
      if (!$result) {
        echo "I broke!";
      }
    }

    $origins = array();

    $stmt = $db->conn->prepare("SELECT originListShort, originListFull FROM originlist WHERE originListState = ?");
    $stmt->bind_param('s', $origin);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc())
    {
      $rowData = new stdClass();
      $rowData->originShort = $row["originListShort"];
      $rowData->originFull = $row["originListFull"];
      $origins[] = $rowData;
    }
    for ($i=0; $i < count($origins); $i++) {
      $result = $db->getShipperSSCO($shipper);
      while($row = $result->fetch_assoc()) {
        $ssco = $row['sscoShort'];
        $perContainer = $row['sscoPerContainer'];
        $resultRate = $db->getRate($ssco, $shipper, $origins[$i]->originShort, $destination);

        while($rateData = $resultRate->fetch_assoc())
        {
            $rowData = new stdClass();
            $rowData->ssco = $ssco;
            $rowData->origin = $origins[$i]->originFull;
            $rowData->destination = $destinations[0]->destinationFull;
            $rowData->rate = $rateData['oceanfreightRate'] + $perContainer;
            $resultData[] = $rowData;
        }
      }
    }
  }
  else {
    if($saveSearch) {
      $result = $db->saveSearch($shipper, $origin, false, $destination);
      if (!$result) {
        echo "I broke!";
      }
    }

    $origins = array();

    $stmt = $db->conn->prepare("SELECT originShort, originFull FROM origin WHERE originShort = ?");
    $stmt->bind_param('s', $origin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc())
    {
      $rowData = new stdClass();
      $rowData->originShort = $row["originShort"];
      $rowData->originFull = $row["originFull"];
      $origins[] = $rowData;
    }

    $result = $db->getShipperSSCO($shipper);
    while($row = $result->fetch_assoc()) {
      $ssco = $row['sscoShort'];
      $perContainer = $row['sscoPerContainer'];

      $resultRate = $db->getRate($ssco, $shipper, $origin, $destination);

      while($rateData = $resultRate->fetch_assoc())
      {
        $rowData = new stdClass();
        $rowData->ssco = $ssco;
        $rowData->origin = $origins[0]->originFull;
        $rowData->destination = $destinations[0]->destinationFull;
        $rowData->rate = $rateData['oceanfreightRate'] + $perContainer;
        $resultData[] = $rowData;
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
