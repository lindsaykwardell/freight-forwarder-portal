<?php
if (login_check($db))
{
?>
  <main>
  <script>document.getElementById("bookingsLink").className += " active";</script>
    <div>
      <div class="card-body">
        <div class="row">
          <div id="newBookingRequest" class="mb-2" style="display: none;">
            <div class="card">
              <div class="card-header bg-navy bkgReqBox text-white">
                <h5 class="card-title float-left">New Booking Request</h5>
                <div class="float-right" style="max-width: 500px;">
                  <div class="input-group">
                    <span class="d-none d-sm-inline mr-sm-2" for="templateSelect" style="margin-top: 13px;"><h6>Templates</h6></span>
                    <select class="custom-select mt-1 mr-sm-2 mb-sm-0" id="templateSelect" onchange="applyTemplate()">
                    </select>
                    <button class="input-group-append btn btn-sm btn-warning" data-toggle='modal' data-target='#smallModal' onclick="manageTemplates()">Manage</button>
                  </div>
                </div>
              </div>
              <div class="px-3 bkgReqBox">
                <div class="row py-0">
                  <div class="col-md-3 col-12 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="sscoSelect">Carrier</label><a style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="We will always strive to provide a booking with the best rate, or that otherwise best matches your request."><sup>&#9432;</sup></a>
                      <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="sscoSelect">
                        <option selected>Best Fit</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 col-6 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="bookingOrigin">Origin<span class='requiredFormElem'></span></label>
                      <select class="custom-select" id="bookingOriginList" required>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-3 col-6 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="bookingDestination">Destination<span class='requiredFormElem'></span></label>
                      <select class="custom-select" id="bookingDestinationList" required>
                      </select>
                    </div>
                  </div>
                  <div class="col-lg-1 col-md-1 col-4 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="originSelect">Count<span class='requiredFormElem'></span></label>
                      <input type="text" class="form-control" id="bookingContainerCount" maxlength="2" value="5" required>
                    </div>
                  </div>
                  <div class="col-md-2 col-8 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="originSelect">Type</label>
                      <select class="custom-select" id="bookingContainerType">
                        <option value="40'REEF">40'REEF</option>
                        <option value="40'STD">40'STD</option>
                        <option value="40'HC" selected>40'HC</option>
                        <option value="FLEX">FLEX (40'/40'HC)</option>
                        <option value="45'HC">45'HC</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="row py-0">
                  <div class="col-lg-3 col-md-2 col-6 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="bookingShipperRef">Shipper Ref #</label>
                      <input type="text" class="form-control" id="bookingShipperRef">
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-2 col-6 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="bookingConsignee">Consignee</label>
                      <input type="text" class="form-control" id="bookingConsignee">
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-4 col-sm-5 col-12 p-1">
                    <div class="form-group">
                      <div class='row'>
                        <div class='col'>
                          <label for="bookingDate">Shipment Date<span class='requiredFormElem'></span></label>
                        </div>
                        <div class='col-auto'>
                          <span class="toggle toggle-light"></span>
                        </div>
                      </div>
                      <input type="text" class="form-control" placeholder="yyyy/mm/dd" id="bookingDateSingle" data-active="true" required>
                      <input type="text" class="form-control" placeholder="yyyy/mm/dd" id="bookingDateRange" data-active="false" style="display: none" required>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-4 col-sm-7 col-12 text-sm-left text-center p-1">
                    <div id="bookingDateTypes" class="form-group ml-3 mt-3">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dateTypeOptions" id="CUT" value="CUT" onclick="togglePrompt(false)" checked>
                        <label class="radio-font form-check-label">CUTOFF</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dateTypeOptions" id="PROMPT" value="PROMPT" onclick="togglePrompt(true)">
                        <label class="radio-font form-check-label">PROMPT</label>
                      </div>
                      <br />
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dateTypeOptions" id="ETD" value="ETD" onclick="togglePrompt(false)">
                        <label class="radio-font form-check-label"style="margin-right: 26px;">ETD</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="dateTypeOptions" id="ETA" value="ETA" onclick="togglePrompt(false)">
                        <label class="radio-font form-check-label">ETA</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row py-0">
                  <div class="col-lg-3 col-md-4 col-12 p-1">
                    <div class="form-group">
                      <label class="mr-sm-2" for="bookingProduct">Product/Packaging</label>
                      <input type="text" class="form-control" id="bookingProduct">
                    </div>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12 p-1">
                    <div class="form-group">
                      <label for="bookingNotes" class="mr-2">Notes</label><a style="cursor: pointer;" data-toggle="tooltip" data-placement="top" title="Notes may include alternative carrier options, additional details, etc."><sup>&#9432;</sup></a>
                      <input type="text" class="form-control" id="bookingNotes">
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <button type='button' class='btn btn-primary' onclick='addBookingRequest()'>Submit Request</button>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="saveAsTemplate" value="true">
                    <label class="form-check-label" for="saveAsTemplate">Save as Template</label>
                  </div>
                  <div class="form-check-inline">
                    <input id="numberToRequest" class="form-control" style="width: 35px;" type="text" maxlength="1" value="1" required>&nbsp;
                    <label class="form-check-label" for="numberToRequest">Number to request<span class='requiredFormElem'></span></label>
                  </div>
                  <div class="float-right">
                    <button type='button' class='btn btn-grey btn-sm' onclick="clearBkgReqForm()">Clear Form</button>
                  </div>
                </div>
                <div id="bkgReqAlerts"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12 mb-2">
            <div class="card">
              <div class="card-header bg-navy">
                <h5 class="card-title text-white float-left"><span id="bkgReqCardLabel">Booking Requests</span></h5>
                <i class="fas fa-print float-right white-text ml-2" style="cursor: pointer;" onclick="printBkgReqList()"></i>
                <i class="fas fa-file-excel float-right white-text ml-2" style="cursor: pointer;" onclick="exportToExcel('pendingBookingsList')"></i>
              </div>
              <div class="card-body" style="overflow: auto;">
                <div class="row">
                  <div class="col-md-3 col-12 form-check form-check-inline">
                    <input class="form-check-input" onclick="loadPendingBookings()" type="checkbox" id="getCompletedRequests">
                    <label class="form-check-label" for="getCompletedRequests">Show all requests (including completed)</label>
                  </div>
                  <!-- <div class="col-md-3 col-12 form-group">
                    <div class="row">
                      <div class="col">
                        <label for="pendingBkgReqRange">Range</label>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-sync" class='close' style="color: green; cursor: pointer" onclick="refreshPendingBkgReqRange()"></i>
                      </div>
                    </div>
                    <input type="text" class="form-control form-control-sm" placeholder="yyyy/mm/dd" id="pendingBkgReqRange" <?php if($accountType == 2) echo "disabled" ?>>
                    <span class="form-check-inline">
                      <input class="form-check-input" onclick="loadPendingBookings()" type="checkbox" id="getCompletedRequests">
                      <label class="form-check-label" for="getCompletedRequests">Show all requests (including completed)</label>
                    </span>
                  </div> -->
                  <div class="col-md-4 col-sm-6 col-12 input-group" >
                    <?php if($accountType == 1) { ?>
                      <label class="d-sm-inline mt-2" for="assignBkgReq">Assign To: &nbsp;</label>
                      <select class="custom-select mt-1 mr-sm-2 mb-sm-0" id="assignBkgReq" onchange="assignBkgReq()">
                      </select>
                    <?php } ?>
                  </div>
                  <div class="col-md-4 col-sm-5 col-12 form-check-inline">
                    <button id="deleteSelectedBkgReqBtn" class="btn btn-sm btn-outline-danger" style='padding: 5px 10px;'>Delete Selected</button>  
                    <button class="btn btn-sm btn-success" onclick="loadPendingBookings()">Refresh List</button>
                    <button class="btn btn-sm btn-grey" onclick="clearBkgReqFilters()">Reset Filters</button>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-2 col-md-4 col-6 form-group">
                    <div class="row">
                      <div class="col">
                        <label for="pendingBkgReqRange">Range</label>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-sync" class='close' style="color: green; cursor: pointer" onclick="refreshPendingBkgReqRange()"></i>
                      </div>
                    </div>
                    <input type="text" class="form-control form-control-sm" placeholder="yyyy/mm/dd" id="pendingBkgReqRange">
                  </div>
                  <div class='col-lg-1 col-md-2 col-3'><label>Status</label>
                    <select class='filter-control form-control form-control-sm' id='filterStatus' data-column='1' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <!-- Hidden for now -->
                  <div class='col-lg-1 col-md-2 col-3 d-none'><label>Date Req.</label>
                    <select class='filter-control form-control form-control-sm' id='filterDateReq' data-column='2' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <div class='col-lg-2 col-md-4 col-4' style='max-width: 120px'><label>Assn.</label>
                    <select  class='filter-control form-control form-control-sm' id='filterAssigned' data-column='3' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <?php if ($accountType == 1) { ?>
                    <div class='col-lg-2 col-md-4 col-4' style='max-width: 160px'><label>Shipper</label>
                      <select class='filter-control form-control form-control-sm' id='filterShipper' style="text-transform: capitalize;" data-column='5' onchange="filterBkgReq()">
                        <option selected>ALL</option>
                      </select>
                    </div>
                  <?php } ?>
                  <div class='col-lg-1 col-md-2 col-3'><label>Carrier</label>
                    <select class='filter-control form-control form-control-sm' id='filterSsco' data-column='6' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <div class='col-lg-1 col-md-2 col-3'><label>Origin</label>
                    <select class='filter-control form-control form-control-sm' id='filterOrigin' data-column='8' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <div class='col-lg-1 col-md-2 col-3'><label>Dest.</label>
                    <select class='filter-control form-control form-control-sm' id='filterDestination' data-column='9' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <div class='col-lg-2 col-md-3 col-4' style='max-width: 160px;'><label>Shipper Ref</label>
                    <select class='filter-control form-control form-control-sm' id='filterShipperRef' data-column='11' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                  <div class='col-lg-2 col-md-4 col-4' style='max-width: 160px'><label>Consignee</label>
                    <select class='filter-control form-control form-control-sm' id='filterConsignee' data-column='12' onchange="filterBkgReq()">
                      <option selected>ALL</option>
                    </select>
                  </div>
                </div>
                <hr />
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
