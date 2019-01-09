<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    $id = htmlspecialchars($_POST['id']);
    $name = htmlspecialchars($_POST['name']);
    $date = date('Y-m-d H:i:s');
    $extLink = htmlspecialchars($_POST['extLink']);
    $category = htmlspecialchars($_POST['category']);
    $content = $_POST['content'];

    if (strpos($content, "<script>") != FALSE) exit;

    $result = $db->updateResource($id, $name, $date, $extLink, $category, $content);
    $db->clearResourceViews();
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
