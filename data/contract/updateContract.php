<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $shipper = htmlspecialchars($_POST['shipper']);
    $ssco = htmlspecialchars($_POST['ssco']);
    $contract = htmlspecialchars($_POST['contract']);
    $startDate = date('Y-m-d',strtotime(htmlspecialchars($_POST['startDate'])));
    $endDate = date('Y-m-d',strtotime(htmlspecialchars($_POST['endDate'])));

    $result = $db->updateContract($shipper, $ssco, $contract, $startDate, $endDate);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
