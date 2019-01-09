/**********************************
 *               CORE              *
 ***********************************/

// Refresh lists and buttons for the home page.
function refreshOptions() {
  localStorage.setItem("shipper", $("#shipperSelect").val());
  updateOriginList();
  updateDestinationList();
  updateSscoList();
  loadContractList();
  loadContractDetails();
  clearSscoRates();
  disableAllowEditRates();
  enablePrintMatrix();
  localStorage.setItem("viewContractDetails", false);
}

function loadContractList() {
  var shipper = $("#shipperSelect").val();
  var url = db + "contract/getContractList.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var content = "<table style='width: 100%'>";
    for (var i = 0; i < result.length; i++) {
      var ssco = result[i].contractSsco;
      for (var s = 0; s < sscoList.length; s++) {
        if (result[i].contractSsco == sscoList[s].sscoShort)
          ssco = sscoList[s].sscoFull;
      }
      content +=
        "<tr><td>" +
        result[i].contractSsco +
        "</td><td><a class='blueLink' onclick=\"openRates('" +
        result[i].contractSsco +
        "')\">" +
        result[i].contractNumber +
        "</a></td></tr>";
    }
    content += "</table>";

    $("#quickContractsList").html(content);
  });
}

function loadContractDetails() {
  var shipper = $("#shipperSelect").val();
  var url = db + "contract/getContractList.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var contracts = new Array();
    for (var i = 0; i < result.length; i++) {
      contracts.push(result[i]);
    }

    var content =
      "<div class='card-header bg-navy text-white'><h5 class='card-title'>Contract Details</h5></div><div class='card-body'><table><tr><th>Carrier</th><th>Contract Number</th><th>Expiration Date</th></tr>";

    for (var i = 0; i < contracts.length; i++) {
      content +=
        "<tr class='border-bottom'><td style='min-width: 125px;'>" +
        contracts[i].contractSsco +
        "</td><td style='min-width: 150px;'>" +
        contracts[i].contractNumber +
        "</td><td style='min-width: 125px;'>" +
        contracts[i].contractEnd +
        "</td></tr>";
    }
    content += "</table></div>";

    $("#rateMatrix").html(content);
  });
}

/***********************************
 *           VIEW RATES            *
 ***********************************/
function openRates(ssco) {
  $("#sscoSelect").val(ssco);
  loadRates();
}

function loadRates() {
  if (localStorage.getItem("viewContractDetails") == "false") {
    var content =
      '<div id="editRatesAlert"></div><div id="ratesContractInfoBox"></div><div id="ratesLastUpdated" class="text-right mr-2"></div><div id="innerRateMatrix" class="ml-1"><div class="row" id="ratesOrigins" style="width: 100%"></div><div id="ratesDestinations"></div><div id="ratesMatrix"></div></div>';
    $("#rateMatrix").html(content);
  }
  viewSscoRates();
  enableAllowEditRates();
  localStorage.setItem("viewContractDetails", true);
}

// Search rates based on shipper and SSCO
function viewSscoRates() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();

  if (ssco != "Choose...") {
    displayLoading("#ratesMatrix");
    displayContract(ssco, shipper);
    displayRates(ssco, shipper);
    disallowEditRates();
  } else {
    clearSscoRates();
  }
}

// Clear rates from the screen
function clearSscoRates() {
  $("#ratesContractInfoBox").html("");
  $("#ratesOrigins").html("");
  $("#ratesDestinations").html("");
  $("#ratesMatrix").html("");
  $("#ratesLastUpdated").html("");
  disallowEditRates();
}

// Load Contract information
function displayContract(ssco, shipper) {
  var url =
    db + "contract/getContract.php?ssco=" + ssco + "&shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var content =
      "<div class='card-header bg-navy text-white'><div class='row'>";
    content +=
      "<div id='fullSscoName' class='col-4' style='overflow: hidden; white-space: nowrap;'><h1>" +
      result.sscoFull +
      "</h1></div>";
    content +=
      "<div class='col'>Contract: <strong><span id='contractNumberVal'>" +
      result.contractNumber +
      "</span></strong><br />Effective: <span id='contractStartVal'>" +
      result.contractStart +
      "</span><br />Expires: &nbsp;&nbsp;<span id='contractEndVal'>" +
      result.contractEnd +
      "</span></div>";
    content += "<div class='col' id='perContainer'></div>";
    content += "<div class='col'><div class='btn-group'>";
    content +=
      "<button id='editRatesButton' type='button' class='btn btn-warning' onclick='enterEditMode();applyPerContainer(false)'>Edit Rates</button>";
    content +=
      "<button id='saveRatesButton' type='button' class='btn btn-success' onclick='exitEditMode();applyPerContainer(true)' style='display: none;'>Finish</button>";
    content += "</div></div></div></div>";
    $("#ratesContractInfoBox").html(content);
    initPerContainer();
  });
}

//Load origins
function displayOrigins(shipper) {
  return new Promise((resolve, reject) => {
    api.get("origin/getOrigins.php?shipper=" + shipper).then(origins => {
      var content = "<div style='margin-left: 15px;width: 120px;'>&nbsp;</div>";
      for (var i = 0; i < origins.length; i++) {
        content +=
          "<div id='" +
          origins[i].originShort +
          "' style='width: 120px;'><button id='" +
          origins[i].originShort +
          "button' type='button' class='close deletePort' onclick='removeOrigin(\"" +
          origins[i].originShort +
          "\")'>&times;</button>" +
          origins[i].originFull +
          "</div>";
      }
      $("#ratesOrigins").html(content);
      resolve(origins);
    });
  });
}

// Always display origins, even after scrolling.
$(window).scroll(function() {
  if ($("#innerRateMatrix")) {
    var t = $("#innerRateMatrix").offset();
    t = t.top;

    var s = $(window).scrollTop();

    var d = t - s;

    if (d < 0) {
      var height = $("#ratesOrigins").height();
      $("#ratesOrigins").addClass("ratesOriginsFixed");
      $("#innerRateMatrix").css({ "padding-top": height });
    } else {
      $("#ratesOrigins").removeClass("ratesOriginsFixed");
      $("#innerRateMatrix").css({ "padding-top": "0px" });
    }
  }
});

//Load destinations
function displayDestinations(shipper) {
  return new Promise((resolve, reject) => {
    api
      .get("destination/getDestinations.php?shipper=" + shipper)
      .then(destinations => {
        var content = "";
        for (var i = 0; i < destinations.length; i++) {
          content +=
            "<div id='" +
            destinations[i].destinationShort +
            "' class='row' style='height: 30px;border-bottom: 1px solid #ccc;'><button id='" +
            destinations[i].destinationShort +
            "button' type='button' class='deletePort close' onclick='removeDestination(\"" +
            destinations[i].destinationShort +
            "\")'>&times;</button>" +
            destinations[i].destinationFull +
            "</div>";
        }
        $("#ratesDestinations").html(content);
        resolve(destinations);
      });
  });
}

//Load rates
async function displayRates(ssco, shipper) {
  var origins = await displayOrigins(shipper);
  var destinations = await displayDestinations(shipper);
  var perContainer = await api.get("ssco/getPerContainer.php?ssco=" + ssco);
  perContainer = parseInt(perContainer, 10);

  var originsShort = [];
  for (let i = 0; i < origins.length; i++) {
    originsShort.push(origins[i].originShort);
  }
  var destinationsShort = [];
  for (let i = 0; i < destinations.length; i++) {
    destinationsShort.push(destinations[i].destinationShort);
  }
  var rates = await api.get(
    "oceanfreight/getRates.php?ssco=" +
      ssco +
      "&shipper=" +
      shipper +
      "&origins=" +
      JSON.stringify(originsShort) +
      "&destinations=" +
      JSON.stringify(destinationsShort)
  );

  var content = "";
  var lastUpdated = "";
  var lastUpdatedBy = "";

  for (var o = 0; o < origins.length; o++) {
    content += "<div style='width: 120px; float: left;'>";
    var newRow = "";

    for (var d = 0; d < destinations.length; d++) {
      var rate = "";
      var rateID = origins[o].originShort + destinations[d].destinationShort;
      for (var r = 0; r < rates.length; r++) {
        if (
          destinations[d].destinationShort == rates[r].destination &&
          origins[o].originShort == rates[r].origin
        ) {
          rate = parseInt(rates[r].rate, 10) + perContainer;
          if (rates[r].updated > lastUpdated) {
            lastUpdated = rates[r].updated;
            lastUpdatedBy = rates[r].updatedBy;
          }
        }
      }
      newRow +=
        "<div class='row' style='height: 30px; border-bottom: 1px solid #ccc;'><input id= '" +
        rateID +
        "' class='rateField' type='number' data-origin='" +
        origins[o].originShort +
        "' data-destination='" +
        destinations[d].destinationShort +
        "' onblur='updateRate(\"" +
        rateID +
        "\")' value='" +
        rate +
        "' disabled></div>";
    }

    content += newRow;
    content += "</div>";
  }

  $("#ratesMatrix").html(content);
  var informLastUpdated = "Rates last updated " + lastUpdated;
  if (lastUpdatedBy.length > 0) informLastUpdated += " by " + lastUpdatedBy;
  $("#ratesLastUpdated").html(informLastUpdated);
}

//Load the per container list into SSCO details.
function initPerContainer() {
  var ssco = $("#sscoSelect").val();
  var result = "";
  $.get(db + "ssco/getPerContainer.php?ssco=" + ssco, function(data) {
    result = JSON.parse(data);
    $("#perContainer").html("<strong>Additional Fees</strong><br />");
    if (result != 0) {
      $("#perContainer").append("$" + result + " per container");
    }
  });
}

// Get per-container charges
function applyPerContainer(toggle) {
  var ssco = $("#sscoSelect").val();
  var result = "";
  $.get(db + "ssco/getPerContainer.php?ssco=" + ssco, function(data) {
    result = JSON.parse(data);
    if (toggle == true) {
      $(".rateField").each(function() {
        if ($(this).val() != "") {
          var value = $(this).val();
          value = parseInt(value, 10);
          value += parseInt(result, 10);
          $(this).val(value);
        }
      });
    } else if (toggle == false) {
      $(".rateField").each(function() {
        if ($(this).val() != "") {
          var value = $(this).val();
          value = parseInt(value, 10);
          value -= parseInt(result, 10);
          $(this).val(value);
        }
      });
    }
  });
}

/**********************************
 *           EDIT RATES            *
 ***********************************/

// Enable the Edit Rates button
function enableAllowEditRates() {
  $("#editRatesButton").prop("disabled", false);
}

// Disable the Edit Rates button
function disableAllowEditRates() {
  $("#editRatesButton").prop("disabled", true);
}

function enterEditMode() {
  allowEditRates();
}

function exitEditMode() {
  disallowEditRates();
}

// Begin editing rates
function allowEditRates() {
  $("#editRatesAlert").html(
    "<div class='row alert alert-warning' role='alert'>EDITING RATES</div>"
  );
  $("#editRatesButton").css({ display: "none" });
  $("#saveRatesButton").css({ display: "block" });
  $(".rateField").prop("disabled", false);
  $(".deletePort").css({ display: "inline" });
}

function disallowEditRates() {
  $("#editRatesAlert").html("");
  $("#editRatesButton").css({ display: "block" });
  $("#saveRatesButton").css({ display: "none" });
  $(".rateField").prop("disabled", true);
  $(".deletePort").css({ display: "none" });
}

// Edit particular rate (new method)
function updateRate(rate) {
  var that = "#" + rate;
  var origin = $(that).data("origin");
  var destination = $(that).data("destination");
  var newRate = $(that).val();
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();

  if (newRate.length == 0 || parseInt(newRate, 10) <= 0 || newRate == "0") {
    $(that).val("");
    $.post(
      db + "oceanfreight/deleteRate.php",
      {
        shipper: shipper,
        ssco: ssco,
        origin: origin,
        destination: destination
      },
      function(data) {}
    );
  } else {
    $.post(
      db + "oceanfreight/addRate.php",
      {
        shipper: shipper,
        ssco: ssco,
        origin: origin,
        destination: destination,
        rate: newRate
      },
      function(data) {
        $("#ratesLastUpdated").html("Rates just updated.");
      }
    );
  }
}

/**********************************
 *           RATE MATRIX           *
 ***********************************/

// Enable the Print Rate Matrix button
function enablePrintMatrix() {
  $(".printRateMatrixButton").prop("disabled", false);
}

// Open printable Rate Matrix
function printMatrix(method) {
  var shipper = $("#shipperSelect").val();

  var printMatrix = window.open(
    "",
    "_blank",
    "top=50,left=50,width=750,height=800,scrollbars=yes"
  );
  var matrixPage = printMatrix.document.body;
  $.get(
    dialog + "rateMatrix.php?shipper=" + shipper + "&method=" + method,
    function(data) {
      matrixPage.innerHTML = data;
    }
  );
}

/**********************************
 *             ORIGINS             *
 ***********************************/
// Display modal with option to add new Origin
function newOriginModal() {
  $("#smallModalLabel").html("Add Origin");
  displayLoading("#smallModalContent");
  var shipper = $("#shipperSelect").val();
  var url = db + "originlist/getOriginList.php?shipper=" + shipper;
  var result = new Array();

  if (shipper != "Choose...") {
    $.get(url, function(data) {
      result = JSON.parse(data);
    }).then(function() {
      if (result == "LimitReached") {
        var content =
          "<h5>Origin limit reached.</h5><br /> An origin must be deleted before another can be added.";
        var footer =
          "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
      } else {
        var content = "<div class='form-group'>";
        content += "<label class='mr-sm-2' for='newOriginShort'>Origin</label>";
        content += "<select class='form-control' id='addOriginSelect'>";
        content += "<option>Choose...</option>";
        for (var i = 0; i < result.length; i++) {
          content +=
            "<option value='" +
            result[i].originShort +
            "'>" +
            result[i].originFull +
            "</option>";
        }
        content += "</select></div>";
        var footer =
          "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
        footer +=
          "<button type='button' class='btn btn-primary' onclick='addOrigin()'>Add Origin</button>";
      }

      $("#smallModalContent").html(content);
      $("#smallModalFooter").html(footer);
    });
  } else {
    var content = "Please select a shipper before proceeding.";
    var footer =
      "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
    $("#smallModalContent").html(content);
    $("#smallModalFooter").html(footer);
  }
}

// Adds new origin to shipper's list
function addOrigin() {
  var shipper = $("#shipperSelect").val();
  var origin = $("#addOriginSelect").val();

  $.post(
    db + "origin/addOrigin.php",
    { shipper: shipper, origin: origin },
    function(data) {}
  ).then(function() {
    if ($("#sscoSelect").val() != "Choose...") {
      viewSscoRates();
    }
    updateOriginList();
    newOriginModal();
  });
}

// Delete destination from shipper list
function removeOrigin(origin) {
  shipper = $("#shipperSelect").val();

  $.post(
    db + "origin/removeOrigin.php",
    { shipper: shipper, origin: origin },
    function(data) {
      $("#" + origin).css({ "text-decoration": "line-through", color: "red" });
      $("#" + origin + "button").html("&#43;");
      $("#" + origin + "button").css({ color: "green" });
      $("#" + origin + "button").attr({
        onclick: 'unremoveOrigin("' + origin + '")'
      });
      updateOriginList();
    }
  );
}

// Unremove destination from shipper's list
function unremoveOrigin(origin) {
  shipper = $("#shipperSelect").val();

  $.post(
    db + "origin/addOrigin.php",
    { shipper: shipper, origin: origin },
    function(data) {
      $("#" + origin).css({ "text-decoration": "none", color: "black" });
      $("#" + origin + "button").html("&times;");
      $("#" + origin + "button").css({ color: "red" });
      $("#" + origin + "button").attr({
        onclick: 'removeOrigin("' + origin + '")'
      });
      updateOriginList();
    }
  );
}

/**********************************
 *           DESTINATIONS          *
 ***********************************/

// Display modal with option to add new Destination
function newDestinationModal() {
  $("#smallModalLabel").html("Add Destination");
  displayLoading("#smallModalContent");
  var shipper = $("#shipperSelect").val();
  var url = db + "destlist/getDestList.php?shipper=" + shipper;
  var result = new Array();

  if (shipper != "Choose...") {
    $.get(url, function(data) {
      result = JSON.parse(data);
    }).then(function() {
      var content = "<div class='form-group'>";
      content +=
        "<label class='mr-sm-2' for='newDestinationShort'>Destination</label>";
      content += "<select class='form-control' id='addDestinationSelect'>";
      content += "<option>Choose...</option>";
      for (var i = 0; i < result.length; i++) {
        content +=
          "<option value='" +
          result[i].destinationShort +
          "'>" +
          result[i].destinationFull +
          "</option>";
      }
      content += "</select></div>";
      var footer =
        "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
      footer +=
        "<button type='button' class='btn btn-primary' onclick='addDestination()'>Add Destination</button>";

      $("#smallModalContent").html(content);
      $("#smallModalFooter").html(footer);
    });
  } else {
    var content = "Please select a shipper before proceeding.";
    var footer =
      "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
    $("#smallModalContent").html(content);
    $("#smallModalFooter").html(footer);
  }
}

// Adds new destination to shipper's list
function addDestination() {
  shipper = $("#shipperSelect").val();
  destination = $("#addDestinationSelect").val();

  $.post(
    db + "destination/addDestination.php",
    { shipper: shipper, destination: destination },
    function(data) {}
  ).then(function() {
    if ($("#sscoSelect").val() != "Choose...") {
      viewSscoRates();
    }
    updateDestinationList();
    newDestinationModal();
  });
}

// Delete destination from shipper list
function removeDestination(destination) {
  shipper = $("#shipperSelect").val();

  $.post(
    db + "destination/removeDestination.php",
    { shipper: shipper, destination: destination },
    function(data) {
      $("#" + destination).css({
        "text-decoration": "line-through",
        color: "red"
      });
      $("#" + destination + "button").html("&#43;");
      $("#" + destination + "button").css({ color: "green" });
      $("#" + destination + "button").attr({
        onclick: 'unremoveDestination("' + destination + '")'
      });
      updateDestinationList();
    }
  );
}

// Unremove destination from shipper's list
function unremoveDestination(destination) {
  shipper = $("#shipperSelect").val();

  $.post(
    db + "destination/addDestination.php",
    { shipper: shipper, destination: destination },
    function(data) {
      $("#" + destination).css({ "text-decoration": "none", color: "black" });
      $("#" + destination + "button").html("&times;");
      $("#" + destination + "button").css({ color: "red" });
      $("#" + destination + "button").attr({
        onclick: 'removeDestination("' + destination + '")'
      });
      updateDestinationList();
    }
  );
}

/**********************************
 *            CONTRACTS            *
 ***********************************/

// Display modal with option to add new Contact
function newContractModal() {
  $("#smallModalLabel").html("Add Contract");
  displayLoading("#smallModalContent");
  var shipper = $("#shipperSelect").val();
  var url = db + "ssco/getSscoList.php?shipper=" + shipper;
  var result = new Array();
  if (shipper != "Choose...") {
    $.get(url, function(data) {
      result = JSON.parse(data);
    }).then(function() {
      var content = "<div class='form-group'>";
      content +=
        "<label class='mr-sm-2' for='newSscoSelect'>Shipping Line<span class='requiredFormElem'></span></label>";
      content += "<select class='form-control' id='newSscoSelect' required>";
      content += "<option>Choose...</option>";
      for (var i = 0; i < result.length; i++) {
        content +=
          "<option value='" +
          result[i].sscoShort +
          "'>" +
          result[i].sscoFull +
          "</option>";
      }
      content += "</select></div>";
      content += '<div class="form-group">';
      content +=
        '<label class="mr-sm-2" for="newContractNumber">Contract Number<span class="requiredFormElem"></span></label>';
      content +=
        '<input type="text" class="form-control" id="newContractNumber" placeholder="S/C #" maxlength="20" style="text-transform: uppercase;" required>';
      content += "</div>";
      content += '<div class="form-group">';
      content +=
        '<label class="mr-sm-2" for="newContractStartDate">Start Date</label>';
      content +=
        '<input type="text" class="form-control" placeholder="yyyy/mm/dd" id="newContractStartDate">';
      content += "</div>";
      content += '<div class="form-group">';
      content +=
        '<label class="mr-sm-2" for="newContractEndDate" onchange="clearAlerts()">Expiry Date</label>';
      content +=
        '<input type="text" class="form-control" placeholder="yyyy/mm/dd" id="newContractEndDate">';
      content += "</div>";
      var footer =
        "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
      footer +=
        "<button type='button' class='btn btn-primary' onclick='addContract()'>Add Contract</button>";

      $("#smallModalContent").html(content);
      $("#smallModalFooter").html(footer);

      $("#newContractStartDate").daterangepicker({
        singleDatePicker: true,
        showWeekNumbers: true,
        autoApply: true,
        startDate: moment(),
        // "minDate": "4/19/2018",
        opens: "left"
      });
      $("#newContractEndDate").daterangepicker({
        singleDatePicker: true,
        showWeekNumbers: true,
        autoApply: true,
        startDate: moment(),
        opens: "left"
      });
    });
  } else {
    var content = "Please select a shipper before proceeding.";
    var footer =
      "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
    $("#smallModalContent").html(content);
    $("#smallModalFooter").html(footer);
  }
}

// Adds new contract with SSCO for shipper
function addContract() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#newSscoSelect").val();
  var contract = $("#newContractNumber").val();
  contract = contract.toUpperCase();
  var startDate = $("#newContractStartDate").val();
  var endDate = $("#newContractEndDate").val();

  if (ssco == "Choose..." || contract.length <= 0) {
    renderToast(
      formatNotification("Form is not complete. Please revise and resend.")
    );
    if (ssco == "Choose...") $("#newSscoSelect").addClass("incompleteFormElem");
    if (contract.length <= 0)
      $("#newContractNumber").addClass("incompleteFormElem");
    initCorrectionListener();
    return;
  }

  $.post(
    db + "contract/addContract.php",
    {
      shipper: shipper,
      ssco: ssco,
      contract: contract,
      startDate: startDate,
      endDate: endDate
    },
    function(data) {}
  ).then(function() {
    updateSscoList();
    loadContractList();
    $("#newSscoSelect").prop("disabled", true);
    $("#newContractNumber").prop("disabled", true);
    $("#newContractStartDate").prop("disabled", true);
    $("#newContractEndDate").prop("disabled", true);
    var content =
      '<div class="alert alert-success" role="alert">Contract Added!</div>';
    $("#smallModalContent").prepend(content);
  });
}

// // Upload a new contract
// function uploadContract()
// {
//   var shipper = $("#shipperSelect").val();
//   var ssco = $("#sscoSelect").val();
//
//   var uploadDialog = window.open("", "_blank", "top=50,left=50,width=500,height=300,scrollbars=no");
//   var uploadPage = uploadDialog.document.body;
//   $.get(dialog + "uploadContract.php?shipper=" + shipper + "&ssco=" + ssco, function( data ){
//     $(uploadPage).append(data);
//   })
// }

// Confirm deletion of contractEnd
function deleteContractModal() {
  var footer =
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
  footer +=
    "<button type='button' class='btn btn-danger' onclick='deleteContract()'>Delete</button>";
  $("#smallModalLabel").html("Delete Contract");
  $("#smallModalContent").html(
    "Are you sure you want to delete this contract? All rates will be deleted."
  );
  $("#smallModalFooter").html(footer);
}

// Delete a contract
function deleteContract() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();

  $.post(
    db + "contract/removeContract.php",
    { shipper: shipper, ssco: ssco },
    function(data) {
      $("#smallModalContent").html("Contract Deleted!");
      $("#smallModalFooter").html(
        "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>"
      );
      updateSscoList();
      loadContractList();
      clearSscoRates();
    }
  );
}

// Edit a contract
function editContractModal() {
  $("#smallModalLabel").html("Edit Contract");
  displayLoading("#smallModalContent");
  var ssco = $("#sscoSelect").val();
  var shipper = $("#shipperSelect").val();
  var url =
    db +
    "contract/getContract.php?shipper=" +
    shipper +
    "&ssco=" +
    ssco +
    "&method=raw";
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var content = "<div class='form-group'>";
    content +=
      '<label class="mr-sm-2" for="editContractNumber">Contract Number<span class="requiredFormElem"></span></label>';
    content +=
      '<input type="text" class="form-control" id="editContractNumber" placeholder="S/C #" maxlength="20" value="' +
      result.contractNumber +
      '" style="text-transform: uppercase;" required>';
    content += "</div>";
    content += '<div class="form-group">';
    content +=
      '<label class="mr-sm-2" for="editContractStartDate">Start Date</label>';
    content +=
      '<input type="text" class="form-control" id="editContractStartDate" value="' +
      result.contractStart +
      '">';
    content += "</div>";
    content += '<div class="form-group">';
    content +=
      '<label class="mr-sm-2" for="editContractEndDate" onchange="clearAlerts()">Expiry Date</label>';
    content +=
      '<input type="text" class="form-control" id="editContractEndDate" value="' +
      result.contractEnd +
      '">';
    content += "</div>";
    var footer =
      "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
    footer +=
      "<button type='button' class='btn btn-primary' onclick='updateContract()'>Update</button>";

    $("#smallModalContent").html(content);
    $("#smallModalFooter").html(footer);

    $("#editContractStartDate").daterangepicker({
      singleDatePicker: true,
      showWeekNumbers: true,
      autoApply: true,
      startDate: moment(result.contractStart),
      // "minDate": "4/19/2018",
      opens: "left"
    });
    $("#editContractEndDate").daterangepicker({
      singleDatePicker: true,
      showWeekNumbers: true,
      autoApply: true,
      startDate: moment(result.contractEnd),
      opens: "left"
    });
  });
}

// Update contract record
function updateContract() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();
  var contract = $("#editContractNumber").val();
  contract = contract.toUpperCase();
  var startDate = $("#editContractStartDate").val();
  var endDate = $("#editContractEndDate").val();

  if (contract.length <= 0) {
    renderToast(
      formatNotification("Form is not complete. Please revise and resend.")
    );
    if (contract.length <= 0)
      $("#editContractNumber").addClass("incompleteFormElem");
    initCorrectionListener();
    return;
  }

  $.post(
    db + "contract/updateContract.php",
    {
      shipper: shipper,
      ssco: ssco,
      contract: contract,
      startDate: startDate,
      endDate: endDate
    },
    function(data) {}
  ).then(function() {
    displayContract(ssco, shipper);
    $("#editContractNumber").prop("disabled", true);
    $("#editContractStartDate").prop("disabled", true);
    $("#editContractEndDate").prop("disabled", true);
    var content =
      '<div class="alert alert-success" role="alert">Contract Updated!</div>';
    $("#smallModalContent").prepend(content);
    loadContractList();
  });
}
