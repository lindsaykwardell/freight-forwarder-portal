<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $result;
  $result = $db->getResourceCategoryList();
  $resultData = array();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $resultData[] = $row['resourceCategory'];
    }
  }
  $json = json_encode($resultData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
