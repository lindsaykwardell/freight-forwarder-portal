<?php
if(login_check($db) && $accountType == 1)
{
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border border-bottom-0">
  <a class="navbar-brand" href="#">Administration</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse justify-content-end" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" onclick="openGRI()" href="#">Enter GRI</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Shippers
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" onclick="newShipperForm()">New Shipper</a>
          <a class="dropdown-item" onclick="modifyShipperForm()">Modify a Shipper</a>
          <a class="dropdown-item" onclick="attachShipperAccountForm()">Attach Shipper Account</a>
          <a class="dropdown-item" onclick="deleteShipperForm()">Delete a Shipper</a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Shipping Lines
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" onclick="newSscoForm()">New Shipping Line</a>
          <a class="dropdown-item" onclick="modifySscoForm()">Modify a Shipping Line</a>
          <a class="dropdown-item" onclick="deleteSscoForm()">Delete a Shipping Line</a>
        </div>
      </li>
      <!-- <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Ports
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="#">New Origin</a>
          <a class="dropdown-item" href="#">Modify an Origin</a>
          <a class="dropdown-item" href="#">Delete an Origin</a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="#">New Destiantion</a>
          <a class="dropdown-item" href="#">Modify a Destination</a>
          <a class="dropdown-item" href="#">Delete a Destination</a>
        </div>
      </li> -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          Reports
        </a>
        <div class="dropdown-menu">
          <a class="dropdown-item" onclick="contractExpireReport()">Contracts to Expire</a>
          <a class="dropdown-item" onclick="openContractsBySsco()">Contracts by Carrier</a>
          <a class="dropdown-item" onclick="compareShipperRates()">Compare Shipper Rates</a>
          <a class="dropdown-item" onclick="ratesOverTime()">Rates Over Time</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" onclick="viewAdmLog()">View Log</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?php echo $dir; ?>register">Register new user</a>
      </li>
    </ul>
  </div>
</nav>
<?php
}
else {
  header("HTTP/1.0 404 Not Found");
} ?>
