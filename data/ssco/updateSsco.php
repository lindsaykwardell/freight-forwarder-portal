<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));

  if($accountType == 1)
  {
    $sscoShort = htmlspecialchars($_POST['sscoShort']);
    $sscoFull = htmlspecialchars($_POST['sscoFull']);
    $perContainer = htmlspecialchars($_POST['perContainer']);
    $sscoNote = htmlspecialchars($_POST['sscoNote']);

    $result = $db->updateSsco($sscoShort, $sscoFull, $perContainer, $sscoNote);

    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
