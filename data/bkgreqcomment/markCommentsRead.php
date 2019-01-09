<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);
    $assignedTo = $db->getBkgReqAssignedAccout($id);
    $result = true;

    if ($assignedTo == htmlspecialchars($_SESSION['username'])) {
      $result = $db->bkgReqMarkCommentsRead($id);
    }
    $json = json_encode($result);
    echo $json;
  } else {
    echo json_encode(false);
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
