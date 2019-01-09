<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $id = htmlspecialchars($_GET['id']);

  $template = new stdClass();

  $result = $db->loadBookingTemplate($id);
  while($row = $result->fetch_assoc())
  {
    $template->Ssco = $row['templateSsco'];
    $template->CntrCount = $row['templateCntrCount'];
    $template->CntrType = $row['templateCntrType'];
    $template->Consignee = $row['templateConsignee'];
    $template->Origin = $row['templateOrigin'];
    $template->Destination = $row['templateDestination'];
    $template->Date = $row['templateDate'];
    $template->DateType = $row['templateDateType'];
  }

  $json = json_encode($template);
  echo $json;
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
