<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $type = htmlspecialchars($_POST['type']);
  $sender = htmlspecialchars($_SESSION['user_id']);
  $receiver = htmlspecialchars($_POST['receiver']);

  $send = true;

  if($sender == $receiver) {
    $send = false;
  } else {
    $stmt = $db->conn->prepare("SELECT * FROM notification WHERE notificationSender = ? AND notificationReceiver = ?");
    $stmt->bind_param('ii', $sender, $receiver);
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc())
    {
      if ($row['notificationType'] == $type && $row['notificationUnread'] == true) $send = false;
    }
  }

  if ($send) {
    $result = $db->sendNotification($receiver, $sender, $type);
    $json = json_encode($result);
    echo $json;
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
