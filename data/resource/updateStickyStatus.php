<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);
    $state = htmlspecialchars($_POST['state']);

    $result = $db->updateStickyStatus($id, $state);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
