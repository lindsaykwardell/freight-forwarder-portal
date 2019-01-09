<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);
    $username;
    if (htmlspecialchars($_POST['target']) == 'self'){
      $username = htmlspecialchars($_SESSION['username']);
    } else {
      $username = $db->getUsername(htmlspecialchars($_POST['target']));
    }
    $result = $db->assignBkgReq($id, $username);
    if ($result) {
      $json = json_encode($username);
      echo $json;
    }
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
