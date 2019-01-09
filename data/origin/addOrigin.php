<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $shipper = htmlspecialchars($_POST['shipper']);
    $originShort = htmlspecialchars($_POST['origin']);

    $stmt = $db->conn->prepare("SELECT originListFull, originListState FROM originlist WHERE originListShort = ?");
    $stmt->bind_param('s', $originShort);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $originFull = "";
    while($row = $result->fetch_assoc()) {
      $originFull = $row["originListFull"];
      $originState = $row['originListState'];
    }
    $result = $db->addOrigin($shipper, $originShort, $originFull, $originState);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
