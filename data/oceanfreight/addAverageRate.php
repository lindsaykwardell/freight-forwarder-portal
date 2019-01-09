<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $origin = htmlspecialchars($_POST['origin']);
    $destination = htmlspecialchars($_POST['destination']);
    $rate = htmlspecialchars($_POST['rate']);

    // Add new rate
    $result = $db->addAverageRate($origin, $destination, $rate);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
