<?php
// DB Connection Details

$servername = "";
$username = "";
$password = "";
$dbname = "";

// DB Class

class DB {
  public $conn;

  function __construct($servername, $username, $password, $dbname)
  {
    $this->conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($this->conn->connect_error) {
        die("Connection failed: " . $conn->connect_error
    );
    }
  }

  // Perform any SQL query
  public function sqlQuery($sql)
  {
    $result = $this->conn->query($sql);
    return $result;
  }

  public function log($txt)
  {
    $log = fopen("../log.txt", "a");
    fwrite($log, date("Y/m/d h:i:sa") . " - " . $_SESSION['username'] . " " . $txt . "\n");
    fclose($log);
    // $entry = $_SESSION['username'] . " " . $txt;
    // $stmt = $this->conn->prepare("INSERT INTO log (entry) VALUES (?)");
    // $stmt->bind_param('s', $entry);
    // $stmt->execute();
  }

  public function sendNotification($receiver, $sender, $type)
  {
    $stmt = $this->conn->prepare("INSERT INTO notification (notificationReceiver, notificationSender, notificationType) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $receiver, $sender, $type);
    $this->log("sent a notification.");
    return $stmt->execute();
  }

  public function getEmpList() {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE accountType = 1");
    $stmt->execute();
    $this->log("got employee list.");
    return $stmt->get_result();
  }

  public function getShipperAccounts() {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE accountType = 2");
    $stmt->execute();
    $this->log("got shipper account list.");
    return $stmt->get_result();
  }

  public function getUsername($id) {
    $stmt = $this->conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['username'];
    }
  }

  public function getAccountType($id) {
    $stmt = $this->conn->prepare("SELECT accountType FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['accountType'];
    }
  }

  public function getUserAssignedAccout($uid) {
    $stmt = $this->conn->prepare("SELECT attachedTo FROM users WHERE id = ?");
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['attachedTo'];
    }
  }

  public function pairToShipper($shipper, $account) {
    $stmt = $this->conn->prepare("UPDATE users SET attachedTo = ? WHERE id = ?");
    $stmt->bind_param('ii', $shipper, $account);
    $this->log("attached account " . $account ." to shipper " . $shipper);
    return $stmt->execute();
  }

  public function addNewSsco($sscoShort, $sscoFull, $perContainer, $sscoNote) {
    $stmt = $this->conn->prepare("INSERT INTO ssco (sscoShort, sscoFull, sscoPerContainer, sscoNote) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $sscoShort, $sscoFull, $perContainer, $sscoNote);
    $this->log("added new carrier " . $sscoShort . ".");
    return $stmt->execute();
  }

  // Get a specific SSCO
  public function getSSCO($ssco)
  {
    $stmt = $this->conn->prepare("SELECT * FROM ssco WHERE sscoShort = ?");
    $stmt->bind_param('s', $ssco);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function updateSsco($sscoShort, $sscoFull, $perContainer, $sscoNote)
  {
    $stmt = $this->conn->prepare("UPDATE ssco SET sscoFull = ?, sscoPerContainer = ?, sscoNote = ? WHERE sscoShort = ?");
    $stmt->bind_param('siss', $sscoFull, $perContainer, $sscoNote, $sscoShort);
    $this->log("updated " . $sscoShort . ".");
    return $stmt->execute();
  }

  // Get a list of SSCOs
  public function getSscoList()
  {
    $stmt = $this->conn->prepare("SELECT * FROM ssco ORDER BY sscoFull");
    $stmt->execute();
    return $stmt->get_result();
  }

  public function deleteSsco($ssco)
  {
    $stmt = $this->conn->prepare("DELETE FROM ssco WHERE sscoShort = ?");
    $stmt->bind_param('s', $ssco);
    $this->log("deleted " . $ssco . ".");
    return $stmt->execute();
  }

  public function addNewShipper($shipperShort, $shipperFull, $shipperPhone, $shipperAddress, $shipperNote, $shipperAssigned) {
    $stmt = $this->conn->prepare("INSERT INTO shipper (shipperShort, shipperFull, shipperPhone, shipperAddress, shipperNote, shipperAssignedto) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssss', $shipperShort, $shipperFull, $shipperPhone, $shipperAddress, $shipperNote, $shipperAssigned);
    $this->log("added new shipper " . $shipperShort . ".");
    return $stmt->execute();
  }

  public function updateShipper($shipperShort, $shipperFull, $shipperPhone, $shipperAddress, $shipperNote, $shipperAssigned)
  {
    $stmt = $this->conn->prepare("UPDATE shipper SET shipperFull = ?, shipperPhone = ?, shipperAddress = ?, shipperNote = ?, shipperAssignedTo = ? WHERE shipperShort = ?");
    $stmt->bind_param('ssssss', $shipperFull, $shipperPhone, $shipperAddress, $shipperNote, $shipperAssigned, $shipperShort);
    $this->log("updated " . $shipperShort . ".");
    return $stmt->execute();
  }

  public function deleteShipper($shipper)
  {
    $stmt = $this->conn->prepare("DELETE FROM shipper WHERE shipperShort = ?");
    $stmt->bind_param('s', $shipper);
    $this->log("deleted " . $shipper . ".");
    return $stmt->execute();
  }

  // Get a list of all SSCOs a shipper has a contract with.
  public function getShipperSSCO($shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM ssco a INNER JOIN contract b ON b.contractSsco = a.sscoShort WHERE b.contractShipper = ? ORDER BY sscoFull");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    $this->log("got SSCOs for " . $shipper . ".");
    return $stmt->get_result();
  }

  public function getContractList($shipper)
  {
    if ($shipper == "0")
    {
      $stmt = $this->conn->prepare("SELECT * FROM contract ORDER BY contractEnd, contractSsco, contractShipper");
    }
    else {
      $stmt = $this->conn->prepare("SELECT * FROM contract WHERE contractShipper = ? ORDER BY contractSsco");
      $stmt->bind_param('s', $shipper);
    }
    $stmt->execute();
    $this->log("got contract list for " . $shipper. ".");
    return $stmt->get_result();
  }

  public function getContractListForSsco($ssco)
  {
    $stmt = $this->conn->prepare("SELECT * FROM contract WHERE contractSsco = ? ORDER BY contractShipper");
    $stmt->bind_param('s', $ssco);
    $stmt->execute();
    $this->log("got contract list for " . $ssco. ".");
    return $stmt->get_result();
  }

  // Get full shipper name
  public function getShipperFull($shipper)
  {
    $stmt = $this->conn->prepare("SELECT shipperFull FROM shipper WHERE shipperShort = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getShipperDetails($shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM shipper WHERE shipperShort = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getShipperShortByID($id) {
    $stmt = $this->conn->prepare("SELECT shipperShort FROM shipper WHERE shipperID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['shipperShort'];
    }
  }

  public function getShipperIdByShort($short) {
    $stmt = $this->conn->prepare("SELECT shipperID FROM shipper WHERE shipperShort = ?");
    $stmt->bind_param('s', $short);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['shipperID'];
    }
  }

  public function getShipperAssignedAccout($id) {
    $stmt = $this->conn->prepare("SELECT shipperUserAccount FROM shipper WHERE shipperID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['shipperUserAccount'];
    }
  }

  public function getBkgReqAssignedAccout($id) {
    $stmt = $this->conn->prepare("SELECT assignedTo FROM bookingrequest WHERE bookingrequestID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['assignedTo'];
    }
  }

  // Add a new contract
  public function addContract($shipper, $ssco, $contract, $startDate, $endDate)
  {
    $stmt = $this->conn->prepare("INSERT INTO contract (contractShipper, contractSsco, contractNumber, contractStart, contractEnd) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $shipper, $ssco, $contract, $startDate, $endDate);
    $this->log("added a contract for " . $shipper . ".");
    return $stmt->execute();
  }

  // Update an existing contract
  public function updateContract($shipper, $ssco, $contract, $startDate, $endDate)
  {
    $stmt = $this->conn->prepare("UPDATE contract SET contractNumber =  ?, contractStart = ?, contractEnd = ? WHERE contractShipper = ? AND contractSsco = ?");
    $stmt->bind_param('sssss', $contract, $startDate, $endDate, $shipper, $ssco);
    $this->log("updated a contract for " . $shipper. ".");
    return $stmt->execute();
  }

  // Get origins for a specific shipper
  public function getOrigins($shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM origin WHERE originShipper = ? ORDER BY originState, originFull ASC");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Add a new origin
  public function addOrigin($shipper, $originShort, $originFull, $originState)
  {
    $stmt = $this->conn->prepare("INSERT INTO origin (originShort, originFull, originShipper, originState) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $originShort, $originFull, $shipper, $originState);
    $this->log("added origin " . $originShort . " to " . $shipper. ".");
    return $stmt->execute();
  }

  // Remove origin
  public function removeOrigin($shipper, $origin)
  {
    $stmt = $this->conn->prepare("DELETE FROM origin WHERE originShort = ? AND originShipper = ?");
    $stmt->bind_param('ss', $origin, $shipper);
    $this->log("removed origin " . $originShort . " from " . $shipper. ".");
    return $stmt->execute();
  }

  public function deleteOriginPerShipper($shipper) {
    $stmt = $this->conn->prepare("DELETE FROM origin WHERE originShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
  }

  // Get destinations for a specific shipper
  public function getDestinations($shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM destination WHERE destinationShipper = ? ORDER BY destinationFull ASC");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Add a new destination
  public function addDestination($shipper, $destinationShort, $destinationFull)
  {
    $stmt = $this->conn->prepare("INSERT INTO destination (destinationShort, destinationFull, destinationShipper) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $destinationShort, $destinationFull, $shipper);
    $this->log("added destination " . $destinationShort . " to " . $shipper. ".");
    return $stmt->execute();
  }

  // Remove destination
  public function removeDestination($shipper, $destination)
  {
    $stmt = $this->conn->prepare("DELETE FROM destination WHERE destinationShort = ? AND destinationShipper = ?");
    $stmt->bind_param('ss', $destination, $shipper);
    $this->log("removed destination " . $destinationShort . " from " . $shipper. ".");
    return $stmt->execute();
  }

  public function deleteDestinationPerShipper($shipper) {
    $stmt = $this->conn->prepare("DELETE FROM destination WHERE destinationShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
  }

  public function saveSearch($shipper, $origin, $isState, $destination)
  {
    // Step 1: Unflag all prior saved favorite searches.
    // Step 2: If a state was chosen for Origin, save that, otherwise just save the city.
    // Step 3: Save the destination.
    // Step 4: Return result.
    $result = true;

    $stmt = $this->conn->prepare("UPDATE origin SET originFavorite = false, originStateFavorite = false WHERE originShipper = ?");
    $stmt->bind_param('s', $shipper);
    if(!$stmt->execute()) $result = false;

    $stmt = $this->conn->prepare("UPDATE destination SET destinationFavorite = false WHERE destinationShipper = ?");
    $stmt->bind_param('s', $shipper);
    if(!$stmt->execute()) $result = false;

    if($isState){
      $stmt = $this->conn->prepare("UPDATE origin SET originStateFavorite = true WHERE originShipper = ? AND originState = ?");
      $stmt->bind_param('ss', $shipper, $origin);
      if(!$stmt->execute()) $result = false;
    }
    else{
      $stmt = $this->conn->prepare("UPDATE origin SET originFavorite = true WHERE originShipper = ? AND originShort = ?");
      $stmt->bind_param('ss', $shipper, $origin);
      if(!$stmt->execute()) $result = false;
    }

    $stmt = $this->conn->prepare("UPDATE destination SET destinationFavorite = true WHERE destinationShipper = ? AND destinationShort = ?");
    $stmt->bind_param('ss', $shipper, $destination);
    if(!$stmt->execute()) $result = false;

    return $result;
  }

  // Get a specific contract
  public function getContract($shipper, $ssco)
  {
    $stmt = $this->conn->prepare("SELECT * FROM contract WHERE contractShipper = ? AND contractSsco = ?");
    $stmt->bind_param('ss', $shipper, $ssco);
    $stmt->execute();
    $this->log("queried contract " . $ssco . " for " . $shipper . ".");
    return $stmt->get_result();
  }

  // Remove destination
  public function removeContract($ssco, $shipper)
  {
    $stmt = $this->conn->prepare("DELETE FROM contract WHERE contractShipper = ? AND contractSsco = ?");
    $stmt->bind_param('ss', $shipper, $ssco);
    $this->log("removed contract " . $ssco . " from " . $shipper . ".");
    return $stmt->execute();
  }

  public function deleteContractPerShipper($shipper) {
    $stmt = $this->conn->prepare("DELETE FROM contract WHERE contractShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
  }

  public function deleteContractPerSsco($ssco) {
    $stmt = $this->conn->prepare("DELETE FROM contract WHERE contractSsco = ?");
    $stmt->bind_param('s', $ssco);
    $stmt->execute();
  }

  // Get a specific rate
  public function getRate($ssco, $shipper, $origin, $destination)
  {
    $stmt = $this->conn->prepare("SELECT * FROM oceanfreight WHERE oceanfreightSsco = ? AND oceanfreightShipper = ? AND oceanfreightOrigin = ? AND oceanfreightDestination = ?");
    $stmt->bind_param('ssss', $ssco, $shipper, $origin, $destination);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getRates($ssco, $shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM oceanfreight WHERE oceanfreightSsco = ? AND oceanfreightShipper = ?");
    $stmt->bind_param('ss', $ssco, $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getAllRatesForRoute($origin, $destination)
  {
    $stmt = $this->conn->prepare("SELECT * FROM oceanfreight WHERE oceanfreightOrigin = ? AND oceanfreightDestination = ?");
    $stmt->bind_param('ss', $origin, $destination);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Add a new rate
  public function addRate($ssco, $shipper, $origin, $destination, $rate, $date)
  {
    $stmt = $this->conn->prepare("INSERT INTO oceanfreight (oceanfreightShipper, oceanfreightSsco, oceanfreightOrigin, oceanfreightDestination, oceanfreightRate, oceanfreightUpdated, oceanfreightUpdatedBy) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('ssssiss', $shipper, $ssco, $origin, $destination, $rate, $date, $_SESSION['username']);
    return $stmt->execute();
  }

  // Update an existing rate
  public function updateRate($rate, $date, $shipper, $ssco, $origin, $destination)
  {
    $stmt = $this->conn->prepare("UPDATE oceanfreight SET oceanfreightRate = ?, oceanfreightUpdated = ? WHERE oceanfreightShipper = ? AND oceanfreightSsco = ? AND oceanfreightOrigin = ? AND oceanfreightDestination = ?");
    $stmt->bind_param('isssss', $rate, $date, $shipper, $ssco, $origin, $destination);
    return $stmt->execute();
  }

  // Delete a rate
  public function deleteRate($ssco, $shipper, $origin, $destination)
  {
    $stmt = $this->conn->prepare("DELETE FROM oceanfreight WHERE oceanfreightSsco = ? AND oceanfreightShipper = ? AND oceanfreightDestination = ? AND oceanfreightOrigin = ?");
    $stmt->bind_param('ssss', $ssco, $shipper, $destination, $origin);
    return $stmt->execute();
  }

  // Delete all rates for a specific shipper/SSCO
  public function clearRates($ssco, $shipper)
  {
    $stmt = $this->conn->prepare("DELETE FROM oceanfreight WHERE oceanfreightSsco = ? AND oceanfreightShipper = ?");
    $stmt->bind_param('ss', $ssco, $shipper);
    return $stmt->execute();
  }

  public function deleteRatesPerShipper($shipper) {
    $stmt = $this->conn->prepare("DELETE FROM oceanfreight WHERE oceanfreightShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
  }

  public function deleteRatesPerSsco($ssco) {
    $stmt = $this->conn->prepare("DELETE FROM oceanfreight WHERE oceanfreightSsco = ?");
    $stmt->bind_param('s', $ssco);
    $stmt->execute();
  }

  // Add a new rate
  public function addAverageRate($origin, $destination, $rate)
  {
    $stmt = $this->conn->prepare("INSERT INTO oceanfreight_average (averageOrigin, averageDestination, averageRate) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $origin, $destination, $rate);
    return $stmt->execute();
  }

  public function loadAverageRates($origin, $destination)
  {
    $stmt = $this->conn->prepare("SELECT * FROM oceanfreight_average WHERE averageOrigin = ? AND averageDestination = ? ORDER BY averageGenerated DESC");
    $stmt->bind_param('ss', $origin, $destination);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Return list of all available origins
  public function getOriginList()
  {
    $stmt = $this->conn->prepare("SELECT * FROM originlist ORDER BY originListState, originListFull");
    $stmt->execute();
    return $stmt->get_result();
  }

  // Return list of all available destinations
  public function getDestList()
  {
    $stmt = $this->conn->prepare("SELECT * FROM destlist ORDER BY destListFull");
    $stmt->execute();
    return $stmt->get_result();
  }

  // Return list of pending bookings.
  public function getPendingBookings($key, $target, $range, $start, $end)
  {
    $sql;
    $stmt;
    switch ($target) {
      case 'shipper':
        $sql = "SELECT * FROM bookingrequest WHERE bookingrequestShipper = ? AND bookingrequestType != 'DELETE' ";
        if (!$range) $sql = $sql . "AND bookingrequestType != 'END' ";
        $sql = $sql . 'AND bookingrequestDate BETWEEN ? AND ? GROUP BY bookingrequest.bookingrequestID ORDER BY bookingrequest.bookingrequestID DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $key, $start, $end);
        $this->log("viewed pending bookings for " . $key . ".");
        break;
      case 'username':
        $sql = 'SELECT * FROM bookingrequest WHERE assignedTo = ? AND bookingrequestType != "DELETE" ';
        if (!$range) $sql = $sql . 'AND bookingrequestType != "END" ';
        $sql = $sql . 'AND bookingrequestDate BETWEEN ? AND ? GROUP BY bookingrequest.bookingrequestID ORDER BY bookingrequest.bookingrequestID DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('sss', $key, $start, $end);
        $this->log("viewed pending bookings for " . $key . ".");
        break;
      case 'deleted':
        $sql = 'SELECT * FROM bookingrequest WHERE bookingrequestType = "DELETE" ';
        $sql = $sql . 'AND bookingrequestDate BETWEEN ? AND ? GROUP BY bookingrequest.bookingrequestID ORDER BY bookingrequest.bookingrequestID DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $start, $end);
        $this->log("viewed deleted bookings.");
        break;
      default:
        $sql = 'SELECT * FROM bookingrequest WHERE bookingrequestDate BETWEEN ? AND ? AND bookingrequestType != "DELETE" ';
        if (!$range) $sql = $sql . 'AND bookingrequestType != "END" ';
        $sql = $sql . 'GROUP BY bookingrequest.bookingrequestID ORDER BY bookingrequest.bookingrequestID DESC';
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $start, $end);
        $this->log("viewed all pending bookings.");
    }
    $stmt->execute();
    return $stmt->get_result();
  }

  public function getLatestBkgReqComment($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bkgreqcomment WHERE bkgreqID = ? ORDER BY ID DESC LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
      while($row = $result->fetch_assoc())
      {
        return $row['comment'];
      }
    }
  }

  public function bkgReqMarkCommentsRead($id)
  {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestIsRead = 1 WHERE bookingrequestID = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
  }

  public function updateBkgReqReadState($id)
  {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestIsRead = 0 WHERE bookingrequestID = ?");
    $stmt->bind_param('i', $id);
    return $stmt->execute();
  }

  public function getBookingRequest($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bookingrequest WHERE bookingrequestID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $this->log("viewed booking request " . $id . ".");
    return $stmt->get_result();
  }

  // Add a new booking request
  public function addBookingRequest($requested, $assignedTo, $shipper, $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated)
  {
    $stmt = $this->conn->prepare("INSERT INTO bookingrequest (bookingrequestType, bookingrequestDateRequested, assignedTo, bookingrequestShipper, bookingrequestSsco, bookingrequestConsignee, bookingrequestCntrCount, bookingrequestCntrType, bookingrequestRef, bookingrequestOrigin, bookingrequestDestination, bookingrequestDateType, bookingrequestDate, bookingrequestDateRange, bookingrequestProduct, bookingrequestNotes, lastUpdated) VALUES
    ('NEW', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssissssssssss', $requested, $assignedTo, $shipper, $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated);
    $this->log("added a new booking request.");
    return $stmt->execute();
  }

  public function updateBookingRequest($id, $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated)
  {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestSsco = ?,
                                                            bookingrequestConsignee = ?,
                                                            bookingrequestCntrCount = ?,
                                                            bookingrequestCntrType = ?,
                                                            bookingrequestRef = ?,
                                                            bookingrequestOrigin = ?,
                                                            bookingrequestDestination = ?,
                                                            bookingrequestDateType = ?,
                                                            bookingrequestDate = ?,
                                                            bookingrequestDateRange = ?,
                                                            bookingrequestProduct = ?,
                                                            bookingrequestNotes = ?,
                                                            lastUpdated = ?
                                                            WHERE bookingrequestID = ?");
    $stmt->bind_param('ssissssssssssi', $ssco, $consignee, $cntrCount, $cntrType, $refNum, $origin, $destination, $dateType, $date, $dateRange, $product, $notes, $lastUpdated, $id);
    $this->log("updated booking request " . $id . ".");
    return $stmt->execute();
  }

  public function deleteBookingRequest($id, $lastUpdated) {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestType = 'DELETE', lastUpdated = ? WHERE bookingrequestID = ?");
    
    $stmt->bind_param('si', $lastUpdated, $id);
    // $stmt = $this->conn->prepare("DELETE FROM bookingrequest WHERE bookingrequestID = ?");
    // $stmt->bind_param('i', $id);
    $this->log("deleted booking request " . $id . ".");
    //$bkgIsDeleted = $stmt->execute();
    return $stmt->execute();
    
    // $stmt = $this->conn->prepare("DELETE FROM bkgreqcomment WHERE bkgreqID = ?");
    // $stmt->bind_param('i', $id);
    // $bkgCommentsAreDeleted = $stmt->execute();
    // return ($bkgIsDeleted && $bkgCommentsAreDeleted);
  }

  public function deleteBkgReqPerShipper($shipper) {
    $stmt = $this->conn->prepare("SELECT bookingrequestID FROM bookingrequest WHERE bookingrequestShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()) {
      $this->deleteBookingRequest($row['bookingrequestID']);
    }
  }

  public function loadBookingTemplate($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bookingrequest_template WHERE templateID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function loadBookingTemplates($shipper)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bookingrequest_template WHERE templateShipper = ?");
    $stmt->bind_param('s', $shipper);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Save a new booking template
  public function saveBookingTemplate($name, $shipper, $ssco, $cntrCount, $cntrType, $consignee, $origin, $destination, $date, $dateType)
  {
    $stmt = $this->conn->prepare("INSERT INTO bookingrequest_template (templateName, templateShipper, templateSsco, templateCntrCount, templateCntrType, templateConsignee, templateOrigin, templateDestination, templateDate, templateDateType) VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssissssss', $name, $shipper, $ssco, $cntrCount, $cntrType, $consignee, $origin, $destination, $date, $dateType);
    $this->log("saved a new template.");
    return $stmt->execute();
  }

  // Remove booking template
  public function deleteBookingTemplate($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM bookingrequest_template WHERE templateID = ?");
    $stmt->bind_param('i', $id);
    $this->log("deleted a template.");
    return $stmt->execute();
  }

  // Add a booking number to a request.
  public function addBookingNumber($id, $number, $lastUpdated)
  {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestNumber = ?, lastUpdated = ? WHERE bookingrequestID = ?");
    $stmt->bind_param('ssi', $number, $lastUpdated, $id);
    $this->log("added booking number " . $number . " to request " . $id . ".");
    return $stmt->execute();
  }

  public function assignBkgReq($id, $username)
  {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET assignedTo = ? WHERE bookingrequestID = ?");
    $stmt->bind_param('si', $username, $id);
    $this->log("assigned booking request " . $id . " to " . $username . ".");
    return $stmt->execute();
  }

  public function updateBkgReqStatus($id, $status, $lastUpdated) {
    $stmt = $this->conn->prepare("UPDATE bookingrequest SET bookingrequestType = ?, lastUpdated = ? WHERE bookingrequestID = ?");
    $stmt->bind_param('ssi', $status, $lastUpdated, $id);
    return $stmt->execute();
  }

  // Return list of booking request comments.
  public function getBkgReqComments($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM bkgreqcomment WHERE bkgreqID = ? ORDER BY ID DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return $stmt->get_result();
  }

  // Add a new booking request
  public function addBkgReqComment($bkgreqID, $username, $date, $comment)
  {
    if ($username === $_SESSION['username'])
    {
      $stmt = $this->conn->prepare("INSERT INTO bkgreqcomment (bkgreqID, username, datePosted, comment) VALUES
      (?, ?, ?, ?)");
      $stmt->bind_param('isss', $bkgreqID, $username, $date, $comment);
      return $stmt->execute();
    }
    else {
      return false;
    }
  }

  public function getBkgReqID($id)
  {
    $stmt = $this->conn->prepare("SELECT bkgreqID FROM bkgreqcomment WHERE ID = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc())
    {
      return $row['bkgreqID'];
    }
  }

  public function deleteBkgReqComment($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM bkgreqcomment WHERE ID = ?");
    $stmt->bind_param('i', $id);
    $this->log("deleted comment " . $id . "");
    return $stmt->execute();
  }

  public function newResourceCheck()
  {
    $stmt = $this->conn->prepare("SELECT COUNT(isViewed) AS isViewed FROM resource_isviewed WHERE userID = ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    return $stmt->get_result();
  }

  public function markResourceViewed() 
  {
    $stmt = $this->conn->prepare("INSERT INTO resource_isviewed (userID, isViewed) VALUES
    (?, '1')");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $this->log("viewed new resources.");
    $stmt->execute();
  }

  public function clearResourceViews()
  {
    $stmt = $this->conn->prepare("DELETE FROM resource_isviewed WHERE userID <> ?");
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
  }

  public function getResourceList()
  {
    $stmt = $this->conn->prepare("SELECT * FROM resource ORDER BY resourceDate DESC LIMIT 10");
    $stmt->execute();
    $this->log("loaded resource list");
    return $stmt->get_result();
  }

  public function getResource($id)
  {
    $stmt = $this->conn->prepare("SELECT * FROM resource WHERE resourceID = ? ORDER BY resourceDate DESC");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    // $this->log("loaded resource " . $id . "");
    return $stmt->get_result();
  }

  public function searchResources($query)
  {
    $stmt = $this->conn->prepare("SELECT * FROM resource WHERE MATCH (resourceName) AGAINST (? IN NATURAL LANGUAGE MODE) ORDER BY resourceDate DESC");
    $stmt->bind_param('s', $query);
    $stmt->execute();
    $this->log("searched resources for '" . $query . "'");
    return $stmt->get_result();
  }

  public function getStickyResourceList()
  {
    $stmt = $this->conn->prepare("SELECT * FROM resource WHERE resourceSticky = '1' ORDER BY resourceCategory, resourceDate DESC");
    $stmt->execute();
    $this->log("loaded resource sticky list");
    return $stmt->get_result();
  }

  public function getResourceCategoryList()
  {
    $stmt = $this->conn->prepare("SELECT DISTINCT resourceCategory FROM resource ORDER BY resourceCategory ASC");
    $stmt->execute();
    $this->log("loaded resource category list");
    return $stmt->get_result();
  }

  public function addNewResource($name, $date, $extLink, $category, $content)
  {
    $stmt = $this->conn->prepare("INSERT INTO resource (resourceName, resourceDate, resourceExtLink, resourceCategory, resourceContent, resourceSticky) VALUES
    (?, ?, ?, ?, ?, '1')");
    $stmt->bind_param('sssss', $name, $date, $extLink, $category, $content);
    $this->log("added a new resource.");
    return $stmt->execute();
  }

  public function updateResource($id, $name, $date, $extLink, $category, $content)
  {
    $stmt = $this->conn->prepare("UPDATE resource SET resourceName = ?, resourceDate = ?, resourceExtLink = ?, resourceCategory = ?, resourceContent = ? WHERE resourceID = ?");
    $stmt->bind_param('sssssi', $name, $date, $extLink, $category, $content, $id);
    $this->log("updated resource " + $id);
    return $stmt->execute();
  }

  public function updateStickyStatus($id, $state) {
    $stmt = $this->conn->prepare("UPDATE resource SET resourceSticky = ? WHERE resourceID = ?");
    $stmt->bind_param('si', $state, $id);
    $this->log("updated sticky status for " . $id . "");
    return $stmt->execute();
  }

  public function deleteResource($id)
  {
    $stmt = $this->conn->prepare("DELETE FROM resource WHERE resourceID = ?");
    $stmt->bind_param('i', $id);
    $this->log("deleted resource " . $id . "");
    return $stmt->execute();
  }
}

$db = new DB($servername, $username, $password, $dbname);
?>
