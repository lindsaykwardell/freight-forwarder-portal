<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $shipper = htmlspecialchars($_POST['shipper']);
    $destinationShort = htmlspecialchars($_POST['destination']);

    $stmt = $db->conn->prepare("SELECT destListFull FROM destlist WHERE destListShort = ?");
    $stmt->bind_param('s', $destinationShort);
    $stmt->execute();
    $result = $stmt->get_result();
    $destinationFull = "";
    while($row = $result->fetch_assoc()) {
      $destinationFull = $row["destListFull"];
    }
    $result = $db->addDestination($shipper, $destinationShort, $destinationFull);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
