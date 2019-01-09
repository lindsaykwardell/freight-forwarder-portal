<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  // if ($accountType == 1) {
    $bkgreqID = htmlspecialchars($_POST['bkgreqID']);
    $username = htmlspecialchars($_SESSION['username']);
    $comment = htmlspecialchars($_POST['comment']);

    date_default_timezone_set('America/Los_Angeles');
    $date = date('Y-m-d H:i:s');

    // Add new booking
    $result = $db->addBkgReqComment($bkgreqID, $username, $date, $comment);
    $json = json_encode($result);
    echo $json;
  // }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
