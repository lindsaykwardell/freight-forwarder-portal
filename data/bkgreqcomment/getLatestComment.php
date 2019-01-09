<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_GET['id']);

    $comment = $db->getLatestBkgReqComment($id);
    $json = json_encode($comment);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
