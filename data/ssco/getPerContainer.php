<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $ssco = htmlspecialchars($_GET['ssco']);

  $result = $db->getSSCO($ssco);
  if ($result->num_rows > 0) {
    $resultData = "";
    while($row = $result->fetch_assoc()) {
      $resultData = $row["sscoPerContainer"];
    }
    $json = json_encode($resultData);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
