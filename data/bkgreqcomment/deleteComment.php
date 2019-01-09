<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);

    $bkgreqID = $db->getBkgReqID($id);

    $result = $db->deleteBkgReqComment($id);

    if ($result) {
      $json = json_encode($bkgreqID);
      echo $json;
    } else {
      $json = json_encode($result);
      echo $json;
    }
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
