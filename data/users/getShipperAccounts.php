<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $users = array();
    $result = $db->getShipperAccounts();
    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        $rowData = new stdClass();
        $rowData->UserID = $row['id'];
        $rowData->UserName = $row['username'];
        $rowData->Email = $row['email'];
        $users[] = $rowData;
      }
    }

    $json = json_encode($users);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
