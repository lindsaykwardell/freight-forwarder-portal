<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  $ssco = htmlspecialchars($_GET['ssco']);

  $result = $db->getSSCO($ssco);

  $sscoData = new stdClass();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $sscoData->Short = $row["sscoShort"];
      $sscoData->Full = $row["sscoFull"];
      $sscoData->PerContainer = $row['sscoPerContainer'];
      $sscoData->Note = $row['sscoNote'];
    }
  }

  $json = json_encode($sscoData);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
