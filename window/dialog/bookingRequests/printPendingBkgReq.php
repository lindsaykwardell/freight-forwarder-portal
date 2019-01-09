<?php
include "../../../php.php";

sec_session_start();

if(login_check($db))
{
  $siteTitle = $_POST['title'];
  $siteHeader = $_POST['header'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Pending Booking Requests</title>
    <link rel="stylesheet" href="<?php echo "https://" . $_SERVER['HTTP_HOST'] . "/" . $style; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo "https://" . $_SERVER['HTTP_HOST'] . "/" . $style; ?>mdb.min.css" />
    <link rel="stylesheet" href="<?php echo "https://" . $_SERVER['HTTP_HOST'] . "/" . $style; ?>main.css">
    <style>
    body {
      background: white;
    }
     thead {
       display:table-header-group;
       margin: 5px;
       padding: 5px;
     }
      tr:nth-child(even){
        background: #eee;
     }
     .hideMe {
       display: none;
     }
     @media print {
       .btn-success, .btn-orange, .btn-indigo, .btn-grey, .btn-dark {
         background-color: white !important;
       }
       table {
         margin: 0 auto;
       }
       .hideMe {
         display: none;
       }
     }
    </style>
  </head>
  <body>
    <section>
      <div class="row mb-2">
        <div class="col-5">
          <img src="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/" . $image . "placeholder.jpg"; ?>" style="width: 300px;" alt="FF Logo"/>
        </div>
        <div class="col-7 text-center">
          <h1><?php echo $siteHeader; ?></h1>
          <?php
          date_default_timezone_set('America/Los_Angeles');
          $date = date('Y-m-d h:i:s A');
          ?>
          <p>Generated <?php echo $date; ?></p>
        </div>
      </div>
    </section>
  </body>
</html>
<?php
}
else {
  header("HTTP/1.0 403 Forbidden");
}
?>
