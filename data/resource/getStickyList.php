<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $result = $db->getStickyResourceList();
  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $rowData = new stdClass();
      $rowData->ID = $row['resourceID'];
      $rowData->Name = $row['resourceName'];
      $rowData->Category = $row['resourceCategory'];
      $rowData->ExtLink = $row['resourceExtLink'];
      $resultData[] = $rowData;
    }
  }
  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
