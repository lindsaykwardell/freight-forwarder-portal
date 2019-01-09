<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $key = htmlspecialchars($_GET['key']);
  $result;
  if ($key == 'ALL') $result = $db->getResourceList();
  if (is_numeric($key)) $result = $db->getResource($key);
  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->ID = $row['resourceID'];
      $rowData->Date = date_format(date_create($row['resourceDate']), 'M d Y g:i a');
      $rowData->Name = $row['resourceName'];
      $rowData->Category = $row['resourceCategory'];
      $rowData->Sticky = $row['resourceSticky'];
      $rowData->ExtLink = $row['resourceExtLink'];
      $rowData->Content = $row['resourceContent'];
      $resultData[] = $rowData;
    }
  }
  if ($key != 'ALL') {
    $log = fopen("../log.txt", "a");
    fwrite($log, date("Y/m/d h:i:sa") . " - " . $_SESSION['username'] . " loaded resource " . $resultData[0]->Name . "\n");
    fclose($log);
  }

  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
