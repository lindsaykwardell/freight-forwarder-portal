<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
  $siteTitle = $_POST['title'];
  $siteHeader = $_POST['header'];
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $siteTitle; ?></title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css' integrity='sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb' crossorigin='anonymous'>

    <script>
      this.resizeTo(750, 800);
    </script>
    <style>
      td {
      min-width: 125px;
       border: 1px solid black;
       margin: 5px;
       padding: 5px;
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
     @media screen {
       table {
         width: 700px;
       }
     }
     @media print {
       table {
         width: 99%;
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
          <img src="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/" . $image . "placeholder.jpg"; ?>" style="width: 100%;" alt="FF Logo"/>
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
