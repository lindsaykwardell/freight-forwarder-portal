<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);
    $status = htmlspecialchars($_POST['status']);
    $lastUpdated = date('Y-m-d H:i:s');

    $result = $db->updateBkgReqStatus($id, $status, $lastUpdated);
    $json = json_encode($result);
    echo $json;
  } else {
    echo "false";
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
