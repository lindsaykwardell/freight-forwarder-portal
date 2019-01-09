<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $origin = htmlspecialchars($_GET['origin']);
    $destination = htmlspecialchars($_GET['destination']);

    $averages = array();
    $result = $db->loadAverageRates($origin, $destination);
    while($row = $result->fetch_assoc())
    {
      $rowData = new stdClass();

      $rowData->rate = $row['averageRate'];
      $rowData->date = date_format(date_create($row['averageGenerated']), 'M d Y');

      $averages[] = $rowData;
    }
    $json = json_encode($averages);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
