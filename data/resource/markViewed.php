<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $db->markResourceViewed();
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
