<?php
include "../../php.php";

sec_session_start();

if(login_check($db))
{
$shipper = htmlspecialchars($_GET['shipper']);

$accountType = $db->getAccountType(htmlspecialchars($_SESSION['user_id']));
if ($accountType == 2) {
  $shipperID = $db->getUserAssignedAccout(htmlspecialchars($_SESSION['user_id']));
  $shipper = $db->getShipperShortByID($shipperID);
}

$method = htmlspecialchars($_GET['method']);
$result = $db->getShipperFull($shipper);
$shipperFull;

while($row = $result->fetch_assoc())
{
  $shipperFull = $row['shipperFull'];
}

function cmp($a, $b)
{
    if ($a->rate == $b->rate) {
        return 0;
    }
    return ($a->rate < $b->rate) ? -1 : 1;
}

if ($method == "contracts")
{
  $siteTitle = "CONTRACT MATRIX";
  $siteHeader = "Contract Matrix";
}
else {
  $siteTitle = "RATE MATRIX";
  $siteHeader = "Rate Matrix";
}

?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php echo $siteTitle . " - " . $shipper; ?></title>
    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css' integrity='sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb' crossorigin='anonymous'>
    <script>
      this.resizeTo(750, 800);
    </script>
    <style>
      td {
       border: 1px solid black;
       margin: 5px;
       padding: 5px;
       min-width: 125px;
     }
     thead {
       display:table-header-group;
       margin: 5px;
       padding: 5px;
     }
      tr:nth-child(even){
        background: #eee;
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
  <body id="bodyId">
    <div>
      <button class="btn btn-success btn-sm hideMe" onclick='window.print()'>Print</button>
    </div>
    <section>
      <div class="row mb-2">
        <div class="col-5">
          <img src="<?php echo "http://" . $_SERVER['HTTP_HOST'] . "/" . $image . "placeholder.jpg"; ?>" style="width: 100%;" alt="FF Logo"/>
        </div>
        <div class="col-7 text-center">
          <h1><?php echo $siteHeader; ?></h1>
          <h4><?php echo $shipperFull; ?></h4>
          <?php
          date_default_timezone_set('America/Los_Angeles');
          $date = date('Y-m-d h:i:s A');
          ?>
          <p>Generated <?php echo $date; ?></p>
        </div>
      </div>
<?php
if ($method == "rates" || $method == "full")
{
?>
      <table class="mb-3">
        <?php

        echo "<thead><tr><th style='width: 125px;'></th>";

        $stmt = $db->conn->prepare("SELECT * FROM origin WHERE originShipper = ? ORDER BY originShort ASC");
        $stmt->bind_param('s', $shipper);
        $stmt->execute();
        $resultOrigin = $stmt->get_result();

        while($rowOrigin = $resultOrigin->fetch_assoc())
        {
          echo "<th>" . $rowOrigin['originFull'] . "</th>";
        }

        echo "</tr></thead>";

        $stmt = $db->conn->prepare("SELECT * FROM destination INNER JOIN destlist ON destination.destinationShort = destlist.destListShort WHERE destination.destinationShipper = ? ORDER BY destinationShort");
        $stmt->bind_param('s', $shipper);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
            $destination = $row['destinationShort'];
            echo '<tr>';
              echo '<td>' . $row['destinationFull'] . '</td>';

              $stmt = $db->conn->prepare("SELECT * FROM origin WHERE originShipper = ? ORDER BY originShort ASC");
              $stmt->bind_param('s', $shipper);
              $stmt->execute();
              $resultOrigin = $stmt->get_result();

                while($rowOrigin = $resultOrigin->fetch_assoc())
                {
                  $origin = $rowOrigin['originShort'];

                  $stmt = $db->conn->prepare("SELECT * FROM ssco a INNER JOIN contract b ON b.contractSsco = a.sscoShort WHERE b.contractShipper = ? ORDER BY sscoShort");
                  $stmt->bind_param('s', $shipper);
                  $stmt->execute();
                  $resultSsco = $stmt->get_result();

                  $rates = array();

                  while($rowSsco = $resultSsco->fetch_assoc()) {
                    $ssco = $rowSsco['sscoShort'];
                    $perContainer = $rowSsco['sscoPerContainer'];

                    $stmt = $db->conn->prepare("SELECT * FROM oceanfreight
                    WHERE oceanfreightSsco = ?
                    AND oceanfreightShipper = ?
                    AND oceanfreightOrigin = ?
                    AND oceanfreightDestination = ? ORDER BY oceanfreightID DESC LIMIT 1");
                    $stmt->bind_param('ssss', $ssco, $shipper, $origin, $destination);
                    $stmt->execute();
                    $resultRate = $stmt->get_result();

                    while($rateData = $resultRate->fetch_assoc())
                    {
                      $rowData = new stdClass();
                      $rowData->ssco = $ssco;
                      $rowData->rate = $rateData['oceanfreightRate'] + $perContainer;
                      $rates[] = $rowData;

                    }
                  }
                  usort($rates, "cmp");
                  echo "<td>";
                  foreach ($rates as $key => $value) {
                    echo '<div class="row"><div class="col-auto" style="width: 50px;">' . $value->ssco . '</div><div class="col">$' . $value->rate . '</div></div>';
                  }
                  echo "</td>";
                }

            echo '</tr>';
          }
        }
        echo "</table>";
}
if ($method == "full")
{
  echo "<h3>Contract Information</h3>";
}
if ($method == "contracts" || $method == "full")
{
?>
      <table>
        <thead>
          <tr>
            <th>Shipping Line</th>
            <th>Contract Number</th>
            <th>Effective Date</th>
            <th>Expiry Date</th>
          </tr>
        </thead>
<?php
      $stmt = $db->conn->prepare("SELECT * FROM contract WHERE contractShipper = ? ORDER BY contractSsco");
      $stmt->bind_param('s', $shipper);
      $stmt->execute();
      $result = $stmt->get_result();

      $resultData = array();
      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          $rowData = new stdClass();
          $rowData->contractSsco = $row['contractSsco'];
          $rowData->contractNumber = $row['contractNumber'];
          if ($row['contractStart'] > 0) {
            $rowData->contractStart = date_format(date_create($row['contractStart']), 'M d Y');
          } else {
            $rowData->contractStart = "";
          }
          if ($row['contractEnd'] > 0) {
            $rowData->contractEnd = date_format(date_create($row['contractEnd']), 'M d Y');
          } else {
            $rowData->contractEnd = "";
          }
          $resultData[] = $rowData;
        }
      }
      foreach ($resultData as $key => $value) {
        $result = $db->getSSCO($value->contractSsco);
        while($row = $result->fetch_assoc()){
          $ssco = $row['sscoFull'];
        }
        echo "<tr><td>" . $ssco . "</td><td>" . $value->contractNumber . "</td><td>" . $value->contractStart . "</td><td>" . $value->contractEnd . "</td></tr>";
      }
}
?>
     </table>
    </section>
  </body>
</html>
<?php
}
else {
  header("HTTP/1.0 403 Forbidden");
}
?>
