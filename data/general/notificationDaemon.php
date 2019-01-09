<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

include "../../php.php";

sec_session_start();

if(login_check($db))
{

/**
 * Constructs the SSE data format and flushes that data to the client.
 *
 * @param string $id Timestamp/id of this connection.
 * @param string $msg Line of text that should be transmitted.
 */
function sendMsg($id, $msg) {
  echo "id: $id" . PHP_EOL;
  echo "data: $msg" . PHP_EOL;
  echo PHP_EOL;
  ob_flush();
  flush();
}

$user = htmlspecialchars($_SESSION['user_id']);

$notify = array();

$stmt = $db->conn->prepare("SELECT * FROM notification WHERE notificationReceiver = ? AND notificationUnread = '1'");
$stmt->bind_param('i', $user);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc())
{
  $rowData = new stdClass();
  $rowData->ID = $row['notificationType'] . "-" . $row['notificationID'];
  $rowData->Sender = $db->getUsername($row['notificationSender']);
  switch($row['notificationType']) {
    case "NEWBKG":
      $rowData->Message = " has requested new bookings.";
      break;
    case "ASGNBKG":
      $rowData->Message = " has assigned bookings requests to you.";
      break;
    case "EDITBKG":
      $rowData->Message = " has updated a booking request.";
      break;
  }

  $notify[] = $rowData;
}

for ($i=0; $i < count($notify); $i++) {
  $msg = $notify[$i]->Sender . $notify[$i]->Message;
  sendMsg($notify[$i]->ID, $msg);
  // $log = fopen("../log.txt", "a");
  // fwrite($log, date("Y/m/d h:i:sa") . " - " . $_SESSION['username'] . "  received a notifcation from " . $notify[$i]->Sender . "\n");
  // fclose($log);
}

}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
