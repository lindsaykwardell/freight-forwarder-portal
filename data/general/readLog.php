<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
  if ($accountType == 1) {
    if (isset($_GET['len'])) {
      $len = '-n' . $_GET['len'];
      exec('tail ' . $len . ' ../log.txt > ../viewlog.txt');
    } else {
      exec('cat ../log.txt > ../viewlog.txt');
    }

    $log = fopen('../viewlog.txt', 'r');

    $file = array();

    while(!feof($log)) {
      $next = fgets($log);
      if ($next) $file[] = $next;
    }
    $json = json_encode($file);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
