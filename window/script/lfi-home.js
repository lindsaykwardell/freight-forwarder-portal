function refreshOptions(){
  // localStorage.setItem("shipper", $("#shipperSelect").val());
  // updateOriginList();
  // updateDestinationList();
  // loadContractList();
  // reloadDashboard();
  return 0;
}

function reloadDashboard() {
  // var mode = 'shipper';
  // if (sessionStorage.getItem("pendingBkgMode") !== null) mode = sessionStorage.getItem("pendingBkgMode");
  // loadPendingBookings('shipper');
  // loadPendingBookings(mode);
  // loadContractExpiringCount();
}

function loadContractExpiringCount()
{
  var shipper = $("#shipperSelect").val();
  var url = db + "contract/getContractList.php?shipper=" + shipper;
  var result = new Array();

  $.get( url, function( data ) {
    result = JSON.parse(data);
  }).then(function(){
    var contracts = new Array();
    for (var i = 0; i < result.length; i++) {
      var range = new Date();
      range.setMonth(range.getMonth() + 1);
      if (Date.parse(result[i].contractEnd) < range && result[i].contractEnd != "")
      {
        contracts.push(result[i]);
      }
    }
    updateDashboard("contractsToExpire", contracts.length);

  })
}

function updateDashboard(id, n) {
  $("#" + id + "Counter").html(n);
}

function openRates(ssco)
{
  localStorage.setItem("ssco", ssco);
  localStorage.setItem("loadingContract", "true");
  window.location = dir + "rates";
}
