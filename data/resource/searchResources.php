<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $key = htmlspecialchars($_POST['key']);
  $keys = explode(' ', $key);
  $len = count($keys);
  $query = "";
  for ($i=0; $i < $len; $i++) {
    $query = $query . "+" . $keys[$i] . "* ";
  }
  $result = $db->searchResources($query);
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
  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
