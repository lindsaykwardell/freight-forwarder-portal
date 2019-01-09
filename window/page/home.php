<?php
if (login_check($db))
{
?>
  <main>
  <script>document.getElementById("homeLink").className += " active";</script>
    <div class="border border-top-0">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="form-group row">
              <?php include $frame . "mode.php"; ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div id="homeDashboard" class="col-lg-4 col-md-12 mb-2">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Dashboard</h5>
              </div>
              <div class="card-body">
                <span id="pendingBookingsCounter">##</span> Pending Bookings<br />
                <span id="contractsToExpireCounter">##</span> Contracts to Expire<br />
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Port-to-Port Rate Search</h5>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label class="mr-sm-2" for="originSelect">Origin</label>
                  <select class="form-control custom-select" id="originSelect">
                    <option>Choose...</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="mr-sm-2" for="destinationSelect">Destination</label>
                  <select class="form-control custom-select" id="destinationSelect">
                    <option>Choose...</option>
                  </select>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="displayAverageRate" checked>
                  <label class="form-check-label" for="displayAverageRate">Display Average Rate</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="saveSearch">
                  <label class="form-check-label" for="saveSearch">Save Search Items</label>
                </div>
                <div class="text-center mt-3">
                  <button type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#regularModal' onclick="portToPort()">Search Rates</button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 col-sm-12 mb-2 d-md-block d-none">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Contract List</h5>
              </div>
              <div id="quickContractsList" class="card-body">
                Select a shipper to see a list of contract numbers.
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-2">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title float-left">Pending Booking Requests</h5>
                <span class="float-right">
                  <div class="dropdown">
                    <button class="btn btn-info dropdown-toggle" type="button" id="filterBkgReq" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item" href="#" onclick='loadPendingBookings("all")'>Manage All Requests</a>
                      <a class="dropdown-item" href="#" onclick="loadPendingBookings('assigned', '<?php echo $_SESSION['username']; ?>')">Manage My Requests</a>
                      <a class="dropdown-item" href="#" onclick='loadPendingBookings("shipper")'>Shipper View</a>
                    </div>
                  </div>
                </span>
              </div>
              <div class="card-body" style=" overflow: auto;">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" onclick="loadPendingBookings()" type="checkbox" id="getCompletedRequests">
                  <label class="form-check-label" for="getCompletedRequests">Show all requests (including completed)</label>
                </div>
                <div class="mt-3" id="pendingBookingsList">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<?php
}
?>
