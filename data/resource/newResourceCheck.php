<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $result = $db->newResourceCheck();
  $isViewed = 0;
  while($row = $result->fetch_assoc())
  {
    $isViewed = $row['isViewed'];
  }
  
  $json = json_encode($isViewed);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
