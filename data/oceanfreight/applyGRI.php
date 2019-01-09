<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $ssco = htmlspecialchars($_POST['ssco']);
    $gri = htmlspecialchars($_POST['gri']);
    $origin = htmlspecialchars($_POST['origin']);

    $return = true;
    $drop = false;

    date_default_timezone_set('America/Los_Angeles');
    $date = date('Y-m-d');

    if($origin == "All")
    {
      $stmt = $db->conn->prepare("SELECT * FROM oceanfreight WHERE oceanfreightSsco = ?");
      $stmt->bind_param('s', $ssco);
      $stmt->execute();
      $result = $stmt->get_result();

      while($row = $result->fetch_assoc())
      {
        $newRate = $row['oceanfreightRate'] + $gri;
        if ($newRate > 0)
        {
          $return = $db->updateRate($newRate, $date, $row['oceanfreightShipper'], $ssco, $row['oceanfreightOrigin'], $row['oceanfreightDestination']);
        }
        else {
          $drop = $db->deleteRate($ssco, $row['oceanfreightShipper'], $row['oceanfreightOrigin'], $row['oceanfreightDestination']);
        }
      }
    }
    else if (strpos($origin, 'STATE') !== false)
    {
      $origin = str_replace("STATE-","", htmlspecialchars($_POST['origin']));
      $origins = array();

      $stmt = $db->conn->prepare("SELECT originListShort FROM originlist WHERE originListState = ?");
      $stmt->bind_param('s', $origin);
      $stmt->execute();
      $result = $stmt->get_result();

      while($row = $result->fetch_assoc())
      {
        $rowData = new stdClass();
        $rowData->originShort = $row["originListShort"];
        $origins[] = $rowData;
      }
      for ($i=0; $i < count($origins); $i++) {
        $result = $db->sqlQuery("SELECT * FROM oceanfreight WHERE oceanfreightSsco = '" . $ssco . "' AND oceanfreightOrigin = '" . $origins[$i]->originShort . "'");
        while($row = $result->fetch_assoc())
        {
          $newRate = $row['oceanfreightRate'] + $gri;
          if ($newRate > 0) {
            $return = $db->updateRate($newRate, $date, $row['oceanfreightShipper'], $ssco, $origins[$i]->originShort, $row['oceanfreightDestination']);
          }
          else {
            $drop = $db->deleteRate($ssco, $row['oceanfreightShipper'], $origins[$i]->originShort, $row['oceanfreightDestination']);
          }
        }
      }
    }
    else {
      $stmt = $db->conn->prepare("SELECT * FROM oceanfreight WHERE oceanfreightSsco = ? AND oceanfreightOrigin = ?");
      $stmt->bind_param('ss', $ssco, $origin);
      $stmt->execute();
      $result = $stmt->get_result();

      $return = true;
      while($row = $result->fetch_assoc())
      {
        $newRate = $row['oceanfreightRate'] + $gri;
        if ($newRate > 0)
        {
          $return = $db->updateRate($newRate, $date, $row['oceanfreightShipper'], $ssco, $origin, $row['oceanfreightDestination']);
        }
        else {
          $drop = $db->deleteRate($ssco, $row['oceanfreightShipper'], $origin, $row['oceanfreightDestination']);
        }
      }
    }
    if($drop == "Error updating record.")
    {
      $return = false;
    }

    $json = json_encode($return);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
