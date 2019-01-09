<?php
if (login_check($db))
{
  if ($accountType == 1)
  {
?>
  <main>
  <script>document.getElementById("ratesLink").className += " active";</script>
  <select style="display: none;" class="col custom-select mb-2 mr-sm-2 mb-sm-0" id="sscoSelect" onchange="loadRates()">
    <option selected>Choose...</option>
  </select>
    <div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3 col-md-12">
            <div class="card mb-2">
              <div class="card-header bg-navy text-white">
                <h5 class="card-title">Contract List</h5>
              </div>
              <div id="quickContractsList" class="card-body">
                Select a shipper to see a list of contract numbers. Hover over the contract number to view additional details.
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-md-12">
            <div class="card" style="overflow: auto;">
              <div id="rateMatrix">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<?php
  }
}
?>
