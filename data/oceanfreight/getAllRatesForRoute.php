<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $origin = htmlspecialchars($_GET['origin']);
    $destination = htmlspecialchars($_GET['destination']);

    $rates = array();
    $result = $db->getAllRatesForRoute($origin, $destination);

    while($row = $result->fetch_assoc())
    {
      $rates[] = $row['oceanfreightRate'];
    }
    $json = json_encode($rates);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
