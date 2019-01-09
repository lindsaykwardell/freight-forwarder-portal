<?php
include "../../../php.php";

sec_session_start();

if(login_check($db))
{
  $json = $_POST['request'];
  $request = json_decode($json);
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Booking Request</title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css' integrity='sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb' crossorigin='anonymous'>
  </head>
  <body>
    <div class='row'>
      <div class='col'>
        <h3><?php echo $request->Shipper; ?></h3>
      </div>
<?php if (strlen($request->BookingNumber) > 0) { ?>
      <div class='col'>
        <div class='text-right'>
          <label class='d-none d-sm-inline mr-sm-2' for='bookingNumber'>Booking Number</label>
          <h3><?php echo $request->BookingNumber; ?></h3>
        </div>
      </div>
<?php } ?>
    </div>
    <hr />
    <div class='row mb-3'>
      <div class='col'><?php echo $request->Origin . " / " . $request->Destination; ?></div>
<?php
if (strlen($request->DateRange) > 0)
{
  $request->Date = $request->Date . ' - ' . $request->DateRange;
}
?>
      <div class='col'><?php echo $request->DateType . " " . $request->Date; ?></div>
      <div class='col'><?php echo $request->CntrType . " x " . $request->CntrCount; ?></div>
      <div class='col'><?php echo $request->Ssco; ?></div>
    </div>
    <div class='row mb-3'>
      <div class='col'><?php echo $request->Ref; ?></div>
      <div class='col'><?php echo $request->Consignee; ?></div>
      <div class='col'><?php echo $request->Product; ?></div>
      <div class='col'></div>
    </div>
    <div>
      <b>Notes:</b><br />
      <?php echo $request->Notes; ?>
    </div>
  </body>
</html>
<?php
}
else {
  header("HTTP/1.0 403 Forbidden");
}
?>
