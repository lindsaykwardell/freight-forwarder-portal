<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $origin = htmlspecialchars($_POST['origin']);
    $shipper = htmlspecialchars($_POST['shipper']);

    $result = $db->removeOrigin($shipper, $origin);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
