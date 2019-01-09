<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_GET['id']);

    $comments = array();
    $result = $db->getBkgReqComments($id);
    while($row = $result->fetch_assoc())
    {
      $rowData = new stdClass();

      $rowData->CommentID = $row['ID'];
      $rowData->User = $row['username'];
      $rowData->Date = date_format(date_create($row['datePosted']), 'M d Y g:i a');
      $rowData->Comment = $row['comment'];

      $comments[] = $rowData;
    }
    $json = json_encode($comments);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
