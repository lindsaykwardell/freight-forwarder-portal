$(function() {
  $('[data-toggle="tooltip"]').tooltip();
});

$(document).ready(function() {
  $("#pendingBkgReqRange")
    .daterangepicker({
      showWeekNumbers: true,
      autoApply: true,
      startDate: moment(),
      endDate: moment().add(14, "weeks"),
      opens: "right"
    })
    .change(function() {
      loadPendingBookings();
    });
  $("#deleteSelectedBkgReqBtn").on("click", function() {
    initDeleteSelectedBkgReq();
  });
});

class BkgReqFilters {
  constructor() {
    this.Status = ["NEW", "REQ"];
    this.DateReq = [];
    this.Assigned = [];
    this.Shipper = [];
    this.ShipperRef = [];
    this.Ssco = [];
    this.Origin = [];
    this.Destination = [];
    this.Consignee = [];
  }

  getPrevFilters() {
    var filters = {
      Status: $("#filterStatus").val(),
      DateReq: $("filterDateReq").val(),
      Assigned: $("#filterAssigned").val(),
      Shipper: $("#filterShipper").val(),
      ShipperRef: $("#filterShipperRef").val(),
      Ssco: $("#filterSsco").val(),
      Origin: $("#filterOrigin").val(),
      Destination: $("#filterDestination").val(),
      Consignee: $("#filterConsignee").val()
    };
    return filters;
  }

  clearFilters() {
    $(".filter-control").each(function() {
      $(this).html("");
    });
  }

  addFilters(item) {
    if (addToArray(this.Status, item.Status)) this.Status.push(item.Status);
    if (addToArray(this.DateReq, item.DateRequested))
      this.DateReq.push(item.DateRequested);
    if (addToArray(this.Assigned, item.AssignedTo))
      this.Assigned.push(item.AssignedTo);
    if (addToArray(this.Shipper, item.Shipper)) this.Shipper.push(item.Shipper);
    if (addToArray(this.ShipperRef, item.Ref)) this.ShipperRef.push(item.Ref);
    if (addToArray(this.Ssco, item.Ssco)) this.Ssco.push(item.Ssco);
    if (addToArray(this.Origin, item.Origin)) this.Origin.push(item.Origin);
    if (addToArray(this.Destination, item.Destination))
      this.Destination.push(item.Destination);
    if (addToArray(this.Consignee, item.Consignee))
      this.Consignee.push(item.Consignee);
  }

  sortFilters() {
    $.each(this, function() {
      this.sort(function(a, b) {
        return (a > b) - (a < b);
      });
    });
  }

  renderFilters(prevFilter) {
    $.each(this, function(key) {
      $("#filter" + key).append(
        "<option style='text-transform: capitalize;'>ALL</option>"
      );
      for (var i = 0; i < this.length; i++) {
        var selected = "";
        if (prevFilter[key] == this[i]) selected = " selected";
        $("#filter" + key).append(
          "<option style='text-transform: capitalize;'" +
            selected +
            ">" +
            this[i] +
            "</option>"
        );
      }
    });
  }
}

function refreshOptions() {
  localStorage.setItem("shipper", $("#shipperSelect").val());
  updateSscoList();
  loadBookingTemplates();
  updateOriginList();
  updateDestinationList();
  loadBookingOriginList($("#shipperSelect").val(), "#bookingOriginList");
  loadBookingDestinationList(
    $("#shipperSelect").val(),
    "#bookingDestinationList"
  );
  loadPendingBookings();

  $("#bookingDateRange").daterangepicker({
    showWeekNumbers: true,
    //"autoApply": true,
    dateLimit: {
      days: 6
    },
    startDate: moment()
      .startOf("week")
      .add(8, "days"),
    endDate: moment()
      .startOf("week")
      .add(12, "days"),
    minDate: moment().subtract(1, "days"),
    opens: "left",
    ranges: {
      "Two Weeks Out": [
        moment()
          .startOf("week")
          .add(15, "days"),
        moment()
          .startOf("week")
          .add(19, "days")
      ],
      "Three Weeks Out": [
        moment()
          .startOf("week")
          .add(22, "days"),
        moment()
          .startOf("week")
          .add(26, "days")
      ],
      "Four Weeks Out": [
        moment()
          .startOf("week")
          .add(29, "days"),
        moment()
          .startOf("week")
          .add(35, "days")
      ],
      "Five Weeks Out": [
        moment()
          .startOf("week")
          .add(36, "days"),
        moment()
          .startOf("week")
          .add(42, "days")
      ],
      "Six Weeks Out": [
        moment()
          .startOf("week")
          .add(43, "days"),
        moment()
          .startOf("week")
          .add(49, "days")
      ]
    }
  });
  $("#bookingDateSingle").daterangepicker({
    singleDatePicker: true,
    showWeekNumbers: true,
    autoApply: true,
    startDate: moment()
      .startOf("week")
      .add(8, "days"),
    minDate: moment().subtract(1, "days"),
    opens: "left",
    showWeekNumbers: true,
    autoApply: true
  });

  $("#bookingDateRange")
    .css("display", "none")
    .data("active", "false");
  $("#bookingDateSingle")
    .css("display", "block")
    .data("active", "true");

  $(".toggle").toggles({
    text: {
      on: "RANGE", // text for the ON position
      off: "SINGLE" // and off
    },
    width: 80 // width used if not set in css
  });

  $(".toggle").on("toggle", function(e, active) {
    if (active) {
      $("#bookingDateRange")
        .css("display", "block")
        .data("active", "true");
      $("#bookingDateSingle")
        .css("display", "none")
        .data("active", "false");
    } else {
      $("#bookingDateRange")
        .css("display", "none")
        .data("active", "false");
      $("#bookingDateSingle")
        .css("display", "block")
        .data("active", "true");
    }
  });
}

// Pulls an estimated rate from the Rate matrix for the origin/destination pair. Currently not in use.
function getRateEstimate() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();
  var origin = $("#bookingOrigin").val();
  var destination = $("#bookingDestination").val();

  var url =
    db +
    "oceanfreight/getRate.php?shipper=" +
    shipper +
    "&ssco=" +
    ssco +
    "&origin=" +
    origin +
    "&destination=" +
    destination;
  var rate;

  $.get(url, function(data) {
    if (data) rate = JSON.parse(data);
  }).then(function() {
    if (rate.length > 0) {
      $("#ratePrediction").html(rate[0].rate);
    } else {
      $("#ratePrediction").html("...");
    }
  });
}

// Loads a list of origins for a specific shipper into a datalist
function loadBookingOriginList(shipper, target, select) {
  var url = db + "origin/getOrigins.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < result.length; i++) {
      var selected = select === result[i].originShort ? " selected" : "";
      content +=
        '<option value="' +
        result[i].originShort +
        '"' +
        selected +
        ">" +
        result[i].originFull +
        "</option>";
    }
    $(target).html(content);
  });
}

// Loads a list of destinations for a specific shipper into a datalist
function loadBookingDestinationList(shipper, target, select) {
  var url = db + "destination/getDestinations.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < result.length; i++) {
      var selected = select === result[i].destinationShort ? " selected" : "";
      content +=
        '<option value="' +
        result[i].destinationShort +
        '"' +
        selected +
        ">" +
        result[i].destinationFull +
        "</option>";
    }
    $(target).html(content);
  });
}

function togglePrompt(val) {
  $("#bookingDateSingle").prop("disabled", val);
  $("#bookingDateRange").prop("disabled", val);
}

// Add a booking request to the database.
function addBookingRequest() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();
  if ($("#sscoSelect").val() == "Choose...") ssco = "Best Fit";
  var consignee = $("#bookingConsignee").val();
  var cntrCount = $("#bookingContainerCount").val();
  var cntrType = $("#bookingContainerType").val();
  var refNum = $("#bookingShipperRef").val();
  var origin = $("#bookingOriginList").val();
  var destination = $("#bookingDestinationList").val();
  var dateType = $("input[name=dateTypeOptions]:checked").val();
  var date;
  if (dateType == "PROMPT") {
    date = moment()
      .add(7, "days")
      .format();
  } else {
    if ($("#bookingDateRange").data("active") == "true") {
      date = $("#bookingDateRange").val();
    } else {
      date = $("#bookingDateSingle").val();
    }
  }
  var product = $("#bookingProduct").val();
  var notes = $("#bookingNotes").val();
  var count = $("#numberToRequest").val();

  if (
    origin.length <= 0 ||
    destination.length <= 0 ||
    date.length <= 0 ||
    cntrCount <= 0 ||
    count <= 0
  ) {
    renderToast(
      formatNotification("Form is not complete. Please revise and resend.")
    );
    if (origin.length <= 0)
      $("#bookingOriginList").addClass("incompleteFormElem");
    if (destination.length <= 0)
      $("#bookingDestinationList").addClass("incompleteFormElem");
    // Currently does nothing.
    // if (date.length <= 0) $("#bookingDate").addClass("incompleteFormElem");
    if (cntrCount <= 0)
      $("#bookingContainerCount").addClass("incompleteFormElem");
    if (count <= 0) $("#numberToRequest").addClass("incompleteFormElem");
    initCorrectionListener();
    return;
  }

  for (var i = 0; i < parseInt(count, 10); i++) {
    $.post(
      db + "bookingrequest/addBookingRequest.php",
      {
        shipper: shipper,
        ssco: ssco,
        consignee: consignee,
        cntrCount: cntrCount,
        cntrType: cntrType,
        refNum: refNum,
        origin: origin,
        destination: destination,
        dateType: dateType,
        date: date,
        product: product,
        notes: notes
      },
      function(data) {
        if (JSON.parse(data) == true) {
          // Need to change this to just load the new row i/o the whole table.
          loadPendingBookings("shipper");
          var toast =
            "<div class='alert alert-success'>Booking requested successfully!</div>";
          $("#bkgReqAlerts").html(toast);
          setTimeout(function() {
            $("#bkgReqAlerts").html("");
          }, 5000);
        }
      }
    );
  }

  sendNewBkgReqNotification("NEWBKG", shipper);

  $("#numberToRequest").val("1");

  if ($("#saveAsTemplate").is(":checked")) {
    saveAsTemplate();
  }
}

function loadBookingTemplates() {
  var shipper = $("#shipperSelect").val();

  var url = db + "bookingrequest/loadBookingTemplates.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "<option>Choose...</option>";
    for (var i = 0; i < result.length; i++) {
      content +=
        "<option value='" + result[i].id + "'>" + result[i].Name + "</option>";
    }
    $("#templateSelect").html(content);
  });
}

function manageTemplates() {
  renderTemplateList();
  $("#smallModalLabel").html("Manage Templates");
  $("#smallModalFooter").html(
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>"
  );
}

function renderTemplateList() {
  var shipper = $("#shipperSelect").val();

  var url = db + "bookingrequest/loadBookingTemplates.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < result.length; i++) {
      content +=
        "<p><button id='" +
        result[i].id +
        "button' type='button' class='close deleteIcon' onclick='deleteTemplate(\"" +
        result[i].id +
        "\")'>&times;</button>&nbsp;" +
        result[i].Name +
        "</p>";
    }
    $("#smallModalContent").html(content);
  });
}

function deleteTemplate(id) {
  post(db + "bookingrequest/deleteTemplate.php", { id }).then(data => {
    renderTemplateList();
    loadBookingTemplates();
  });
  // $.post(db + "bookingrequest/deleteTemplate.php", {id: id}).then(function(){
  //   renderTemplateList();
  //   loadBookingTemplates();
  // })
}

function applyTemplate() {
  var id = $("#templateSelect").val();

  var url = db + "bookingrequest/applyTemplate.php?id=" + id;
  var result = {};

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    if (result.Ssco != "Best Fit") {
      $("#sscoSelect").val(result.Ssco);
    } else {
      $("#sscoSelect").val("Choose...");
    }
    $("#bookingContainerCount").val(result.CntrCount);
    $("#bookingContainerType").val(result.CntrType);
    $("#bookingConsignee").val(result.Consignee);
    $("#bookingOriginList").val(result.Origin);
    $("#bookingDestinationList").val(result.Destination);

    if (result.Date == "range") {
      $("#bookingDateRange")
        .css("display", "block")
        .data("active", "true");
      $("#bookingDateSingle")
        .css("display", "none")
        .data("active", "false");
      $(".toggle").toggles({
        on: true,
        text: {
          on: "RANGE",
          off: "SINGLE"
        },
        width: 80
      });
    } else {
      $("#bookingDateRange")
        .css("display", "none")
        .data("active", "false");
      $("#bookingDateSingle")
        .css("display", "block")
        .data("active", "true");
      $(".toggle").toggles({
        on: false,
        text: {
          on: "RANGE",
          off: "SINGLE"
        },
        width: 80
      });
    }

    $("#" + result.DateType).prop("checked", true);

    $("#bookingShipperRef").val("");
    $("#bookingDate").val("");
    $("#bookingProduct").val("");
    $("#bookingNotes").val("");
  });
}

function saveAsTemplate() {
  var shipper = $("#shipperSelect").val();
  var ssco = $("#sscoSelect").val();
  if ($("#sscoSelect").val() == "Choose...") ssco = "Best Fit";
  var cntrCount = $("#bookingContainerCount").val();
  var cntrType = $("#bookingContainerType").val();
  var consignee = $("#bookingConsignee").val();
  var origin = $("#bookingOriginList").val();
  var destination = $("#bookingDestinationList").val();
  var date;
  if ($("#bookingDateRange").data("active") == "true") {
    date = "range";
  } else {
    date = "single";
  }
  var dateType = $("input[name=dateTypeOptions]:checked").val();

  $.post(
    db + "bookingrequest/saveAsTemplate.php",
    {
      shipper: shipper,
      ssco: ssco,
      cntrCount: cntrCount,
      cntrType: cntrType,
      consignee: consignee,
      origin: origin,
      destination: destination,
      date: date,
      dateType: dateType
    },
    function(data) {
      if (JSON.parse(data) == true) {
        loadBookingTemplates();
        renderToast(
          "<div class='alert alert-success'>Template saved successfully!</div>"
        );
        $("#saveAsTemplate").prop("checked", false);
      }
    }
  );
}

function clearBkgReqForm() {
  $("#sscoSelect").prop("selectedIndex", 0);
  $("#bookingContainerType").prop("selectedIndex", 2);
  $("#bookingContainerCount").val("5");
  $("#bookingDateRange")
    .css("display", "none")
    .data("active", "false");
  $("#bookingDateRange")
    .data("daterangepicker")
    .setStartDate(
      moment()
        .startOf("week")
        .add(8, "days")
    );
  $("#bookingDateRange")
    .data("daterangepicker")
    .setEndDate(
      moment()
        .startOf("week")
        .add(12, "days")
    );
  $("#bookingDateSingle")
    .css("display", "block")
    .data("active", "true");
  $("#bookingDateSingle")
    .data("daterangepicker")
    .setStartDate(
      moment()
        .startOf("week")
        .add(8, "days")
    );
  $(".toggle").toggles({
    on: false,
    text: {
      on: "RANGE",
      off: "SINGLE"
    },
    width: 80
  });

  $("#bookingDateTypes > div > input:radio:first").prop("checked", true);

  $("#bookingShipperRef").val("");
  $("#bookingConsignee").val("");
  $("#bookingDate").val("");
  $("#bookingProduct").val("");
  $("#bookingNotes").val("");
}

function refreshPendingBkgReqRange() {
  $("#pendingBkgReqRange")
    .data("daterangepicker")
    .setStartDate(moment());
  $("#pendingBkgReqRange")
    .data("daterangepicker")
    .setEndDate(moment().add(14, "weeks"));
  loadPendingBookings();
}

function loadPendingBookings(query, input) {
  displayLoading("#pendingBookingsList");

  var url = pendingBookingsURL(query, input);
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    renderBkgReqTable(result);
  });
}

function pendingBookingsURL(query, input) {
  var method = query;
  var data = input;
  var val = "";

  if (!sessionStorage.getItem("pendingBkgMode") && method == undefined)
    method = "shipper";

  var switched = false;
  while (!switched) {
    switch (method) {
      case "shipper":
        var shipper = $("#shipperSelect").val();
        val = "?shipper=" + shipper;
        switched = true;
        $("#newBookingRequest").slideDown();
        sessionStorage.setItem("pendingBkgMode", "shipper");
        $("#bkgReqCardLabel").html("Booking Requests");
        break;
      case "assigned":
        val = "?username=" + data;
        sessionStorage.setItem("pendingBkgData", data);
        switched = true;
        sessionStorage.setItem("pendingBkgMode", "assigned");
        $("#newBookingRequest").slideUp();
        $("#bkgReqCardLabel").html("My Booking Requests");
        break;
      case "deleted":
        val = "?deleted=true";
        switched = true;
        sessionStorage.setItem("pendingBkgMode", "deleted");
        $("#newBookingRequest").slideUp();
        $("#bkgReqCardLabel").html("Deleted Booking Requests");
        break;
      case "all":
        val = "";
        switched = true;
        sessionStorage.setItem("pendingBkgMode", "all");
        $("#newBookingRequest").slideUp();
        $("#bkgReqCardLabel").html("All Booking Requests");
        break;
      default:
        method = sessionStorage.getItem("pendingBkgMode");
        data = sessionStorage.getItem("pendingBkgData");
    }
  }

  if ($("#getCompletedRequests").is(":checked")) {
    //toggleRangeSelector(true);
    if (val.length > 0) {
      val = val + "&getCompleted=true";
    } else {
      val = "?getCompleted=true";
    }
  } else {
    //toggleRangeSelector(false);
  }

  var range = $("#pendingBkgReqRange").val();
  if (val.length > 0) {
    val = val + "&range=" + range;
  } else {
    val = "?range=" + range;
  }

  return db + "bookingrequest/getPendingBookings.php" + val;
}

function renderBkgReqTable(result) {
  var displayMode = 0;

  $.get(db + "users/getDisplayMode.php", function(data) {
    if (data) displayMode = JSON.parse(data);
  }).then(function() {
    var content = '<table id="pendingBkgReq" style="width: 100%;"><tr>';
    content += '<th class="hideMe"></th>';
    content += "<th>Status</th>";
    content += '<th class="d-lg-table-cell d-none">Req.</th>';
    content += '<th class="d-md-table-cell d-none">Assn.</th>';
    content +=
      '<th class="d-md-table-cell d-none" style="white-space: nowrap;">B<span class="d-lg-inline d-none">ooking</span><span class="d-lg-none d-inline">kg</span> #</th>';
    if (displayMode == 1) {
      content += "<th>Shipper</th>";
    } else {
      content += "<th></th>";
    }
    content += "<th>Carrier</th>";
    content += "<th>FCL</th>";
    content += "<th>Origin</th>";
    content += "<th>Dest.</th>";
    content += "<th>Date</th>";
    content +=
      '<th style="white-space: nowrap;">Ref<span class="d-lg-inline d-none">erence</span> #</th>';
    content += "<th>Consignee</th>";
    content += "<th>Product</th>";
    // content += '<th class="pendingBookingNotes d-md-table-cell d-none">Notes</th>';
    content += "</tr>";

    var filter = new BkgReqFilters();
    var prevFilter = filter.getPrevFilters();
    filter.clearFilters();

    for (var i = 0; i < result.length; i++) {
      var unreadStatus = "";
      var freshReq = "";
      var hidden = "";
      if (displayMode == 1) {
        if (!result[i].IsRead) unreadStatus = " freshReq";
        // if (!result[i].IsRead && result[i].CommentCount <= 0 && result[i].Status == 'NEW') freshReq = " freshReq";
      }

      var btnType = "";
      switch (result[i].Status) {
        case "NEW":
          btnType = " btn-success";
          break;
        case "REQ":
          btnType = " btn-orange";
          break;
        case "MOD":
          btnType = " btn-indigo";
          break;
        case "DONE":
          btnType = " btn-grey";
          hidden = " flag-hidden";
          break;
        default:
          btnType = " btn-dark";
      }

      if (result[i].DateType == "PROMPT") result[i].Date = "";

      result[i].Shipper = result[i].Shipper.toLowerCase();

      var product = result[i].Product;
      var consignee = result[i].Consignee;

      var assignedTo = result[i].AssignedTo.indexOf(" ");
      result[i].AssignedTo = result[i].AssignedTo.slice(0, assignedTo);

      filter.addFilters(result[i]);

      content +=
        "<tr id='bkgreq-" +
        result[i].id +
        "' class='" +
        freshReq +
        unreadStatus +
        hidden +
        "' style='cursor: pointer;' data-toggle='modal' data-target='#largeModal' oncontextmenu=\"toggleRow('" +
        result[i].id +
        "'); return false;\" onclick='openBookingRequest(\"" +
        result[i].id +
        "\")'>";
      content +=
        "<td class='hideMe'><input type='checkbox' id='bkgreq-" +
        result[i].id +
        "-check' class='bkgreq-selector' value='" +
        result[i].id +
        "' onclick=\"rowIsClicked(this, '" +
        result[i].id +
        "'); event.stopPropagation();\"></td>";
      if (displayMode == 1) {
        content +=
          "<td style='width: 60px'><button id='" +
          result[i].id +
          "-statusBtn' data-status='" +
          result[i].Status +
          "' class='statusBtn btn btn-sm" +
          btnType +
          "' onclick='event.stopPropagation();' ondblclick =\"nextBkgReqStatus('" +
          result[i].id +
          "');\" style='width: 50px; padding: 5px;'>" +
          result[i].Status +
          "</button></td>";
      } else {
        content +=
          "<td style='width: 60px'><button id='" +
          result[i].id +
          "-statusBtn' data-status='" +
          result[i].Status +
          "' class='statusBtn btn btn-sm" +
          btnType +
          "' style='width: 50px; padding: 5px;'>" +
          result[i].Status +
          "</button></td>";
      }
      content +=
        "<td style='width: 40px' class='d-lg-table-cell d-none'>" +
        result[i].DateRequested +
        "</td>";
      content +=
        "<td style='width: 60px;' id='bkgreq-" +
        result[i].id +
        "-assignedTo' class='d-md-table-cell d-none'>" +
        result[i].AssignedTo +
        "</td>";
      content +=
        "<td id='bkgreq-" +
        result[i].id +
        "-number' class='d-md-table-cell d-none'>" +
        result[i].BookingNumber +
        "</td>";
      if (displayMode == 1) {
        content +=
          "<td style='width: 75px; text-transform: capitalize;'>" +
          result[i].Shipper +
          "</td>";
      } else {
        content += "<td></td>";
      }
      content += "<td style='width: 60px'>" + result[i].Ssco + "</td>";
      content +=
        "<td style='width: 70px'>" +
        result[i].CntrCount +
        " " +
        result[i].CntrType +
        "</td>";
      content += "<td style='width: 50px'>" + result[i].Origin + "</td>";
      content += "<td style='width: 50px'>" + result[i].Destination + "</td>";

      if (result[i].DateRange.length > 0) {
        result[i].Date += "-" + result[i].DateRange;
      }

      content += "<td>" + result[i].DateType + " " + result[i].Date + "</td>";
      content += "<td>" + result[i].Ref + "</td>";
      content += "<td>" + consignee + "</td>";
      content += "<td>" + product; //+ "</td>";
      // if (product.length > 0 && consignee.length > 0) {
      //   content += "<td class='d-md-table-cell d-none'>" + consignee + "/" + product;
      // } else if (product.length > 0 && consignee.length == 0) {
      //   content += "<td class='d-md-table-cell d-none'>" + product;
      // } else if (product.length == 0 && consignee.length > 0) {
      //   content += "<td class='d-md-table-cell d-none'>" + consignee;
      // } else {
      //   content += "<td class='d-md-table-cell d-none'>";
      // }
      // if (product.length + consignee.length > 0) content += "<br />";
      if (product.length > 0) content += "<br />";
      if (result[i].LatestComment !== null) {
        content +=
          "<i id='bkgreq-" +
          result[i].id +
          "-notes' style='color: green'>" +
          result[i].LatestComment +
          "</i>";
      } else {
        content +=
          "<i id='bkgreq-" + result[i].id + "-notes' style='color: green'></i>";
      }
      content += "</td></tr>";
      //content += "</tr>";
    }
    content += "</table>";
    $("#pendingBookingsList").html(content);

    filter.sortFilters();
    filter.renderFilters(prevFilter);
    filterBkgReq();

    flagHidden(displayMode);

    allowSortTable();
  });
}

function flagHidden(displayMode) {
  if (
    displayMode == 1 &&
    !$("#getCompletedRequests").is(":checked") &&
    $("#filterStatus").val() != "DONE"
  ) {
    $(".flag-hidden").css("display", "none");
  }
}

function refreshPendingBookings(val) {
  var pendingBkgMode = sessionStorage.getItem("pendingBkgMode");
  switch (pendingBkgMode) {
    case "all":
      loadPendingBookings("all");
      break;
    case "assigned":
      loadPendingBookings("assigned", val);
      break;
    case "shipper":
      loadPendingBookings("shipper");
  }
}

function toggleRow(id) {
  if ($("#bkgreq-" + id).hasClass("highlightedRow")) {
    $("#bkgreq-" + id).removeClass("highlightedRow");
    $("#bkgreq-" + id + "-check").prop("checked", false);
  } else {
    $("#bkgreq-" + id).addClass("highlightedRow");
    $("#bkgreq-" + id + "-check").prop("checked", true);
  }
  disarmDeleteSelectedBkgReq();
}

function toggleRangeSelector(activate) {
  getDisplayMode().then(displayMode => {
    if (displayMode == 2 && activate) {
      document.getElementById("pendingBkgReqRange").disabled = false;
    } else if (displayMode == 2 && !activate) {
      document.getElementById("pendingBkgReqRange").disabled = true;
    }
  });
}

function filterBkgReq() {
  var displayMode = 0;

  $.get(db + "users/getDisplayMode.php", function(data) {
    if (data) displayMode = JSON.parse(data);
  }).then(function() {
    var filters = [];
    $(".filter-control").each(function() {
      if ($(this).val() != "ALL") {
        filters.push({ val: $(this).val(), col: $(this).data("column") });
      }
    });
    var table = document.getElementById("pendingBkgReq");
    var rowLength = table.rows.length;

    for (var i = 1; i < rowLength; i += 1) {
      var row = table.rows[i];
      var id = table.rows[i].id;
      $("#" + id).css("display", "table-row");
      for (var f = 0; f < filters.length; f++) {
        var target;
        switch (filters[f].col) {
          case 1:
            target = row.cells[1].getElementsByTagName("button")[0].innerHTML;
            break;
          case 2:
            target = row.cells[2].innerHTML;
            break;
          case 3:
            target = row.cells[3].innerHTML;
            break;
          case 5:
            target = row.cells[5].innerHTML;
            break;
          case 6:
            target = row.cells[6].innerHTML;
            break;
          case 8:
            target = row.cells[8].innerHTML;
            break;
          case 9:
            target = row.cells[9].innerHTML;
            break;
          case 10:
            target = row.cells[10].innerHTML;
            var indexOf = target.indexOf(" ");
            target = target.slice(0, indexOf);
            break;
          case 11:
            target = row.cells[11].innerHTML;
            break;
          case 12:
            target = row.cells[12].innerHTML;
            break;
        }
        // console.log(target + " and " + filters[f].val);
        if (target != filters[f].val) {
          $("#" + id).css("display", "none");
        }
      }
    }
    flagHidden(displayMode);
  });
}

function clearBkgReqFilters() {
  $(".filter-control").each(function() {
    $(this).prop("selectedIndex", 0);
  });
  filterBkgReq();
}

function nextBkgReqStatus(id) {
  switch ($("#" + id + "-statusBtn").data("status")) {
    case "NEW":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "REQ" },
        function(data) {
          if (JSON.parse(data) == true)
            $("#" + id + "-statusBtn")
              .html("REQ")
              .addClass("btn-orange")
              .removeClass("btn-success")
              .data("status", "REQ");
          addBookingRequestComment(id, "Requested.");
          renderToast(
            "<div class='alert alert-primary'>Request marked as Requested</div>"
          );
        }
      );
      break;
    case "REQ":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "DONE" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#" + id + "-statusBtn")
              .html("DONE")
              .addClass("btn-grey")
              .removeClass("btn-orange")
              .data("status", "DONE");
          }
          addBookingRequestComment(id, "Completed.");
          renderToast(
            "<div class='alert alert-primary'>Request marked as Completed.</div>"
          );
        }
      );
      break;
    case "DONE":
    default:
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "END" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#bkgreq-" + id).slideUp();
            $("#" + id + "-statusBtn")
              .html("END")
              .addClass("btn-dark")
              .removeClass("btn-grey")
              .data("status", "END");
          }
          addBookingRequestComment(id, "Request closed.");
          renderToast(
            "<div class='alert alert-primary'>Request has been closed.</div>"
          );
        }
      );
      break;
  }
}

function assignBkgReq() {
  var userID = $("#assignBkgReq").val();
  var check = false;
  $(".bkgreq-selector:checkbox:checked").each(function() {
    check = true;
    var username;
    var id = $(this).val();
    toggleRow(id);
    $.post(
      db + "bookingrequest/assignBkgReq.php",
      { id: id, target: userID },
      function(data) {
        if (data) username = JSON.parse(data);
      }
    ).then(function() {
      $("#displayAssignedTo").html("Assigned to " + username);

      addBookingRequestComment(
        id,
        username + " has been assigned this booking."
      );

      var assignedTo = username.indexOf(" ");
      assignedTo = username.slice(0, assignedTo);

      $("#bkgreq-" + id + "-assignedTo").html(assignedTo);

      sendDirectNotification("ASGNBKG", userID);
    });
  });
  if (check) {
    content = formatNotification("The selected bookings have been reassigned.");
    renderToast(content);
  }
  setTimeout(function() {
    $("#assignBkgReq").prop("selectedIndex", 0);
  }, 100);
}

function initDeleteSelectedBkgReq() {
  $("#deleteSelectedBkgReqBtn")
    .html("Confirm Delete")
    .addClass("btn-danger")
    .removeClass("btn-outline-danger")
    .off()
    .on("click", function() {
      deleteSelectedBkgReq();
    });
}

function disarmDeleteSelectedBkgReq() {
  $("#deleteSelectedBkgReqBtn")
    .html("Delete Selected")
    .addClass("btn-outline-danger")
    .removeClass("btn-danger")
    .off()
    .on("click", function() {
      initDeleteSelectedBkgReq();
    });
}

function deleteSelectedBkgReq() {
  $(".bkgreq-selector:checkbox:checked").each(function() {
    var id = $(this).val();
    deleteBkgReq(id);
  });
  disarmDeleteSelectedBkgReq();
}

// Open a booking to work on.
function openBookingRequest(id) {
  var displayMode = 0;

  $("#largeModalLabel").html("Booking Request");
  displayLoading("#largeModalContent");

  $.get(db + "users/getDisplayMode.php", function(data) {
    if (data) displayMode = JSON.parse(data);
  }).then(function() {
    var url = db + "bookingrequest/getBookingRequest.php?id=" + id;
    var result = {};

    $.get(url, function(data) {
      if (data) result = JSON.parse(data);
    }).then(function() {
      var content =
        "<div id='bkgReqReadState' class='card p-2' style='background: #ffffcc'>";
      content += renderBookingRequest(result, displayMode);
      content += "</div>";

      content += "<div id='bkgReqEditState' style='display: none'></div>";

      var assignedTo = "Assigned to " + result.AssignedTo;
      if (result.AssignedTo.length <= 0) assignedTo = "Not Assigned";

      content += "<div class='row'>";
      if (displayMode == 1)
        content +=
          "<div class='col-auto'><button id='bkgReqToggleState' data-toggle='off' class='btn btn-sm btn-orange mt-3' onclick=\"editBookingRequest('" +
          id +
          "')\">Edit Request</button></div>";
      content +=
        "<div class='col-auto'><button class='btn btn-sm btn-success mt-3' onclick=\"printBookingRequest('" +
        result.requestID +
        "')\">Print</button></div>";
      content +=
        "<div id='displayAssignedTo' class='col mt-4 text-right'><i>Requested " +
        result.reqDate +
        ".<br />" +
        assignedTo +
        ". </i></div></div><hr />";

      content += renderStatusBar(result.Status);

      if (displayMode == 1) {
        content += '<label for="bkgReqComment">Add a comment</label>';
        content +=
          "<form id='addBookingRequestCommentForm' onsubmit=\"addBookingRequestComment('" +
          result.requestID +
          "', document.getElementById('bkgReqComment').value); return false;\"><div class='input-group mb-2'>";
        content +=
          "<input type='text' id='bkgReqComment' class='form-control'>";
        content +=
          "<button class='btn btn-outline-primary' type='button' onclick=\"addBookingRequestComment('" +
          result.requestID +
          "', document.getElementById('bkgReqComment').value)\">Add Comment</button></div></form>";
        content += "<div id='commentsContainer'></div>";
      }

      var footer =
        "<button id='bkgReqDeleteButton' type='button' class='btn btn-outline-danger'>Delete Request</button>";
      if (displayMode == 1) {
        footer +=
          "<div class='btn-group dropup'><button type='button' class='btn btn-grey dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Update Status</button><div class='dropdown-menu'>";
        footer +=
          "<a class='dropdown-item btn-success' onclick=\"updateBkgReqStatus('" +
          result.requestID +
          "', 'NEW')\">Mark as New</a>";
        footer +=
          "<a class='dropdown-item btn-orange' onclick=\"updateBkgReqStatus('" +
          result.requestID +
          "', 'REQ')\">Mark as Requested</a>";
        footer +=
          "<a class='dropdown-item btn-indigo' onclick=\"updateBkgReqStatus('" +
          result.requestID +
          "', 'MOD')\">Mark as Modifying</a>";
        footer +=
          "<a class='dropdown-item btn-grey' onclick=\"updateBkgReqStatus('" +
          result.requestID +
          "', 'DONE')\">Mark as Completed</a>";
        footer +=
          "<a class='dropdown-item btn-dark' onclick=\"updateBkgReqStatus('" +
          result.requestID +
          "', 'END')\">Close Request</a>";
        footer += "</div></div>";
      }
      footer +=
        "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";

      $("#largeModalContent").html(content);
      $("#largeModalFooter").html(footer);

      $("#bkgReqEditState").load(
        page + "partial/bookingRequests/editBkgReq.html"
      );

      $("#bkgReqDeleteButton").on("click", function() {
        initDeleteBkgReq(result.requestID);
      });

      if (displayMode == 1) {
        displayLoading("#commentsContainer");
        loadBookingRequestComments(result.requestID);
      }

      if (result.Ssco != "Best Fit") {
        var url =
          db +
          "contract/getContract.php?ssco=" +
          result.Ssco +
          "&shipper=" +
          result.Shipper;
        var contractInfo = new Array();

        $.get(url, function(data) {
          contractInfo = JSON.parse(data);
        }).then(function() {
          $("#contractNum").html(
            "<i>(" + contractInfo.contractNumber + ")</i>"
          );
        });
      }
    });
  });
}

function renderBookingRequest(result, displayMode) {
  var shipper = result.Shipper;
  var shipperList = new Array();
  $("#shipperSelect option").each(function(i) {
    shipperList[i] = $(this).val();
  });
  for (var i = 0; i < shipperList.length; i++) {
    if (result.Shipper == shipperList[i])
      shipper = $("#shipperSelect option:eq(" + i + ")").text();
  }

  var origin = result.Origin;
  var destination = result.Destination;
  var ssco = result.Ssco;

  for (var i = 0; i < originList.length; i++) {
    if (result.Origin == originList[i].originShort)
      origin = originList[i].originFull;
  }
  for (var i = 0; i < destList.length; i++) {
    if (result.Destination == destList[i].destinationShort)
      destination = destList[i].destinationFull;
  }
  for (var i = 0; i < sscoList.length; i++) {
    if (result.Ssco == sscoList[i].sscoShort) ssco = sscoList[i].sscoFull;
  }

  if (result.DateRange.length > 0) result.Date += " - " + result.DateRange;
  if (result.DateType == "PROMPT") result.Date = "";

  var content = "";
  if (displayMode == 1) {
    content +=
      "<div class='row'><div class='col'><h3>" + shipper + "</h3></div>";
    content +=
      "<div class='col'><div class='text-right'><label class='d-none d-sm-inline mr-sm-2' for='bookingNumber'>Booking Number</label><input style='width: 150px' type='text' placeholder='Booking #' class='form-control float-right' id='bookingNumber' maxlength='16' onchange=\"addBookingNumber('" +
      result.requestID +
      "')\" value=" +
      result.BookingNumber +
      "></div></div>";
    content += "</div><hr />";
  }
  // content += "<div class='row mb-3 justify-content-center'>";
  // content += "<div class='col-4'>";
  // content += ssco + "&nbsp;<span id='contractNum'></span><br />";
  // content += origin + " / " + destination + "<br />";
  // content += result.DateType + " " + result.Date + "<br />";
  // content += result.CntrCount + " x " + result.CntrType;
  // content += "</div>";
  // content += "<div class='col-4'>";
  // content += result.Ref + "<br />";
  // content += result.Consignee + "<br />";
  // content += result.Product;
  // content += "</div>";
  // content += "</div>";
  // content += "<div><b>Notes:</b> <br />" + result.Notes + "</div>";

  content += "<div class='row mb-3'>";
  content += "<div class='col'>" + origin + " / " + destination + "</div>";
  content +=
    "<div class='col'>" + result.DateType + " " + result.Date + "</div>";
  content +=
    "<div class='col'>" + result.CntrCount + " x " + result.CntrType + "</div>";
  content += "<div class='col'>" + ssco + "</div>";
  content += "</div><div class='row mb-3'>";
  content += "<div class='col'>" + result.Ref + "</div>";
  content += "<div class='col'>" + result.Consignee + "</div>";
  content += "<div class='col'>" + result.Product + "</div>";
  content += "<div class='col'><div id='contractNum'></div></div>";
  content += "</div>";
  content += "<div><b>Notes:</b> <br />" + result.Notes + "</div>";

  // content += "<div class='row mb-3'>";
  // content += "<div class='col'>" + origin + " / " + destination + "</div>";
  // content += "<div class='col'>" + result.DateType + " " + result.Date + "</div>";
  // content += "<div class='col'>" + result.CntrCount + " x " + result.CntrType + "</div>";
  // content += "</div><div class='row mb-3'>";
  // content += "<div class='col'>" + result.Ref + "</div>";
  // content += "<div class='col'>" + result.Consignee + "<br />" + result.Product + "</div>";
  // content += "<div class='col'>" + ssco + "<br /><div id='contractNum'></div></div>";
  // content += "</div>";
  // content += "<div><b>Notes:</b> <br />" + result.Notes + "</div>";

  return content;
}

function renderStatusBar(v) {
  var statusPer;
  var statusColor;
  var status;
  switch (v) {
    case "NEW":
      statusPer = 25;
      statusColor = "bg-success";
      status = "Request received";
      break;
    case "REQ":
      statusPer = 50;
      statusColor = "orange";
      status = "Booking requested";
      break;
    case "MOD":
      statusPer = 75;
      statusColor = "indigo";
      status = "Processing Booking";
      break;
    case "DONE":
    case "END":
      statusPer = 100;
      statusColor = "grey";
      status = "Request completed";
      break;
  }
  return (
    "<div class='progress'><div id='bkgReqProgress' class='progress-bar progress-bar-striped progress-bar-animated " +
    statusColor +
    "' role='progressbar' style='width: " +
    statusPer +
    "%; font-weight: bold' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'>" +
    status +
    "</div></div>"
  );
}

function printBookingRequest(id) {
  var url = db + "bookingrequest/getBookingRequest.php?id=" + id;
  var result = {};

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var shipperList = new Array();
    $("#shipperSelect option").each(function(i) {
      shipperList[i] = $(this).val();
    });
    for (var i = 0; i < shipperList.length; i++) {
      if (result.Shipper == shipperList[i])
        result.Shipper = $("#shipperSelect option:eq(" + i + ")").text();
    }
    for (var i = 0; i < originList.length; i++) {
      if (result.Origin == originList[i].originShort)
        result.Origin = originList[i].originFull;
    }
    for (var i = 0; i < destList.length; i++) {
      if (result.Destination == destList[i].destinationShort)
        result.Destination = destList[i].destinationFull;
    }
    for (var i = 0; i < sscoList.length; i++) {
      if (result.Ssco == sscoList[i].sscoShort)
        result.Ssco = sscoList[i].sscoFull;
    }

    var printRequest = window.open(
      "",
      "_blank",
      "top=50,left=50,width=750,height=800,scrollbars=yes"
    );
    var requestPage = printRequest.document.body;
    $.post(
      dialog + "bookingRequests/printRequest.php",
      { request: JSON.stringify(result) },
      function(data) {
        requestPage.innerHTML = data;
        setTimeout(function() {
          printRequest.print();
          printRequest.close();
        }, 200);
      }
    );
  });
}

function editBookingRequest(id) {
  var state = $("#bkgReqToggleState").data("toggle");
  var newState = state == "off" ? "on" : "off";

  switch (newState) {
    case "on":
      $("#bkgReqReadState").css("display", "none");
      $("#bkgReqEditState").css("display", "block");
      loadEditBkgReq(id);
      $("#bkgReqToggleState")
        .addClass("btn-success")
        .removeClass("btn-orange")
        .html("Save Changes");
      break;
    case "off":
      $("#bkgReqReadState").css("display", "block");
      $("#bkgReqEditState").css("display", "none");
      $("#bkgReqToggleState")
        .addClass("btn-orange")
        .removeClass("btn-success")
        .html("Edit Request");

      var displayMode = 0;
      updateBkgReq(id);
      $.get(db + "users/getDisplayMode.php", function(data) {
        if (data) displayMode = JSON.parse(data);
      }).then(function() {
        var url = db + "bookingrequest/getBookingRequest.php?id=" + id;
        var result = {};

        $.get(url, function(data) {
          if (data) result = JSON.parse(data);
        }).then(function() {
          $("#bkgReqReadState").html(renderBookingRequest(result, displayMode));
        });
      });
      break;
  }

  $("#bkgReqToggleState").data("toggle", newState);
}

function loadEditBkgReq(id) {
  var url = db + "bookingrequest/getBookingRequest.php?id=" + id;
  var result = {};

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    loadSscoList(result.Shipper, "#editBkgReqSsco", result.Ssco);
    loadBookingOriginList(result.Shipper, "#editBkgReqOrigin", result.Origin);
    loadBookingDestinationList(
      result.Shipper,
      "#editBkgReqDestination",
      result.Destination
    );
    $("#editBkgReqCntrCount").val(result.CntrCount);
    $("#editBkgReqShipperRef").val(result.Ref);
    $("#editBkgReqConsignee").val(result.Consignee);
    var dateRange;

    if (result.DateRange.length > 0) {
      dateRange = result.DateRange;
      $("#editBkgReqDateRange")
        .css("display", "block")
        .data("active", "true");
      $("#editBkgReqSingleDate")
        .css("display", "none")
        .data("active", "false");
      $("#editBkgReqDateToggle").toggles({
        on: true,
        text: {
          on: "RANGE",
          off: "SINGLE"
        },
        width: 80
      });
    } else {
      dateRange = result.Date;
      $("#editBkgReqDateRange")
        .css("display", "none")
        .data("active", "false");
      $("#editBkgReqSingleDate")
        .css("display", "block")
        .data("active", "true");
      $("#editBkgReqDateToggle").toggles({
        on: false,
        text: {
          on: "RANGE",
          off: "SINGLE"
        },
        width: 80
      });
    }
    $("#editBkgReqDateRange").daterangepicker({
      showWeekNumbers: true,
      autoApply: true,
      dateLimit: {
        days: 6
      },
      startDate: result.Date,
      endDate: dateRange,
      minDate: moment().subtract(1, "days"),
      opens: "left"
    });
    $("#editBkgReqSingleDate").daterangepicker({
      singleDatePicker: true,
      showWeekNumbers: true,
      autoApply: true,
      startDate: result.Date,
      minDate: moment().subtract(1, "days"),
      opens: "left",
      showWeekNumbers: true,
      autoApply: true
    });
    $("#editBkgReqDateToggle").on("toggle", function(e, active) {
      if (active) {
        $("#editBkgReqDateRange")
          .css("display", "block")
          .data("active", "true");
        $("#editBkgReqSingleDate")
          .css("display", "none")
          .data("active", "false");
      } else {
        $("#editBkgReqDateRange")
          .css("display", "none")
          .data("active", "false");
        $("#editBkgReqSingleDate")
          .css("display", "block")
          .data("active", "true");
      }
    });
    $("#editBkgReq" + result.DateType).prop("checked", true);
    $("#editBkgReqProduct").val(result.Product);
    $("#editBkgReqNotes").val(result.Notes);
  });
}

function updateBkgReq(id) {
  var ssco = $("#editBkgReqSsco").val();
  if ($("#editBkgReqSsco").val() == "Choose...") ssco = "Best Fit";
  var consignee = $("#editBkgReqConsignee").val();
  var cntrCount = $("#editBkgReqCntrCount").val();
  var cntrType = $("#editBkgReqCntrType").val();
  var refNum = $("#editBkgReqShipperRef").val();
  var origin = $("#editBkgReqOrigin").val();
  var destination = $("#editBkgReqDestination").val();
  var dateType = $("input[name=editBkgReqDateTypeOptions]:checked").val();
  var date;
  if ($("#editBkgReqDateRange").data("active") == "true") {
    date = $("#editBkgReqDateRange").val();
  } else {
    date = $("#editBkgReqSingleDate").val();
  }
  var product = $("#editBkgReqProduct").val();
  var notes = $("#editBkgReqNotes").val();

  if (
    origin.length <= 0 ||
    destination.length <= 0 ||
    date.length <= 0 ||
    cntrCount <= 0
  ) {
    renderToast(
      formatNotification("Form is not complete. Please revise and resend.")
    );
    if (origin.length <= 0)
      $("#editBkgReqOrigin").addClass("incompleteFormElem");
    if (destination.length <= 0)
      $("#editBkgReqDestination").addClass("incompleteFormElem");
    if (cntrCount <= 0)
      $("#editBkgReqCntrCount").addClass("incompleteFormElem");
    initCorrectionListener();
    return;
  }

  $.post(
    db + "bookingrequest/updateBookingRequest.php",
    {
      id: id,
      ssco: ssco,
      consignee: consignee,
      cntrCount: cntrCount,
      cntrType: cntrType,
      refNum: refNum,
      origin: origin,
      destination: destination,
      dateType: dateType,
      date: date,
      product: product,
      notes: notes
    },
    function(data) {
      if (JSON.parse(data) == true) {
        // Need to change this to just load the new row i/o the whole table.
        loadPendingBookings();
        addBookingRequestComment(id, "Request has been modified");
        sendNewBkgReqNotification("EDITBKG", $("#shipperSelect").val());
        // Load this data into the read view.
      }
    }
  );
}

function addBookingNumber(id) {
  var bkgNum = $("#bookingNumber").val();
  $.post(
    db + "bookingrequest/addBookingNumber.php",
    { id: id, number: bkgNum },
    function(data) {
      if (JSON.parse(data)) {
        $("#bkgreq-" + id + "-number").html(htmlEntities(bkgNum));
        addBookingRequestComment(id, "Booking number updated: " + bkgNum);
        renderToast(
          "<div class='alert alert-primary'>Booking number updated: " +
            htmlEntities(bkgNum) +
            "</div>"
        );
      }
    }
  ).then(function() {
    if ($("#" + id + "-statusBtn").data("status") == "NEW") {
      updateBkgReqStatus(id, "REQ");
    }
  });
}

function updateBkgReqStatus(id, status) {
  switch (status) {
    case "NEW":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "NEW" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#bkgreq-" + id).css("display", "table-row");
            $("#" + id + "-statusBtn")
              .html("NEW")
              .addClass("btn-success")
              .removeClass("btn-orange")
              .removeClass("btn-indigo")
              .removeClass("btn-grey")
              .removeClass("btn-dark")
              .data("status", "NEW");
            addBookingRequestComment(id, "Request marked as New.");
            renderToast(
              "<div class='alert alert-primary'>Request marked as New.</div>"
            );
          }
        }
      );
      break;
    case "REQ":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "REQ" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#bkgreq-" + id).css("display", "table-row");
            $("#" + id + "-statusBtn")
              .html("REQ")
              .addClass("btn-orange")
              .removeClass("btn-success")
              .removeClass("btn-indigo")
              .removeClass("btn-grey")
              .removeClass("btn-dark")
              .data("status", "REQ");
            addBookingRequestComment(id, "Requested.");
            renderToast(
              "<div class='alert alert-primary'>Request marked as requested.</div>"
            );
          }
        }
      );
      break;
    case "MOD":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "MOD" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#bkgreq-" + id).css("display", "table-row");
            $("#" + id + "-statusBtn")
              .html("MOD")
              .addClass("btn-indigo")
              .removeClass("btn-success")
              .removeClass("btn-orange")
              .removeClass("btn-grey")
              .removeClass("btn-dark")
              .data("status", "MOD");
            addBookingRequestComment(id, "Modifying.");
            renderToast(
              "<div class='alert alert-primary'>Request marked as Modifying.</div>"
            );
          }
        }
      );
      break;
    case "DONE":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "DONE" },
        function(data) {
          if (JSON.parse(data) == true) {
            $("#bkgreq-" + id).css("display", "table-row");
            $("#" + id + "-statusBtn")
              .html("DONE")
              .addClass("btn-grey")
              .removeClass("btn-success")
              .removeClass("btn-orange")
              .removeClass("btn-indigo")
              .removeClass("btn-dark")
              .data("status", "DONE");
            addBookingRequestComment(id, "Completed.");
            renderToast(
              "<div class='alert alert-primary'>Request marked as Completed.</div>"
            );
          }
        }
      );
      break;
    case "END":
      $.post(
        db + "bookingrequest/updateStatus.php",
        { id: id, status: "END" },
        function(data) {
          if (JSON.parse(data) == true) {
            if (!$("#getCompletedRequests").is(":checked"))
              $("#bkgreq-" + id).css("display", "none");
            $("#" + id + "-statusBtn")
              .html("END")
              .addClass("btn-dark")
              .removeClass("btn-success")
              .removeClass("btn-orange")
              .removeClass("btn-indigo")
              .removeClass("btn-grey")
              .data("status", "END");
            addBookingRequestComment(id, "Closed.");
            renderToast(
              "<div class='alert alert-primary'>Request has been closed.</div>"
            );
          }
        }
      );
      break;
  }
}

function initDeleteBkgReq(id) {
  $("#bkgReqDeleteButton")
    .html("Confirm Delete")
    .addClass("btn-danger")
    .removeClass("btn-outline-danger")
    .off()
    .on("click", function() {
      deleteBkgReq(id);
    });
}

function deleteBkgReq(id) {
  $.post(db + "bookingrequest/deleteBookingRequest.php", { id: id }, function(
    data
  ) {
    console.log(data);
    if (JSON.parse(data)) {
      $("#bkgreq-" + id).remove();
      content = formatNotification("Booking request has been deleted.");
      renderToast(content);
      $("#bkgReqReadState")
        .css({ background: "white", border: "1px solid red", color: "red" })
        .html("Booking Request has been deleted.");
    }
  });
}

function loadBookingRequestComments(id) {
  var result = new Array();

  $.get(db + "bkgreqcomment/getComments.php?id=" + id, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < result.length; i++) {
      content +=
        "<div class='commentCard' id='comment-" +
        result[i].CommentID +
        "'><span class='float-right'>" +
        result[i].Date +
        "<i class='fas fa-trash ml-2 close' onclick='deleteBkgReqComment(\"" +
        result[i].CommentID +
        "\")' style='color: red'></i></span><div><b>" +
        result[i].User +
        " said:</b><br />" +
        result[i].Comment +
        "</div></div>";
    }

    $("#commentsContainer").html(content);
    markBkgReqCommentsRead(id);
  });
}

function markBkgReqCommentsRead(id) {
  $.post(db + "bkgreqcomment/markCommentsRead.php", { id: id }, function(data) {
    if (JSON.parse(data) == true) {
      $("#bkgreq-" + id).removeClass("unread");
      $("#bkgreq-" + id).removeClass("freshReq");
    }
  });
}

function addBookingRequestComment(id, comment) {
  $("#bkgReqComment").val("");

  $.post(
    db + "bkgreqcomment/addComment.php",
    { bkgreqID: id, comment: comment },
    function(data) {
      if (JSON.parse(data) == true) {
        loadBookingRequestComments(id);
        updateBkgReqReadState(id);
        $("#bkgreq-" + id + "-notes").html(htmlEntities(comment));
      }
    }
  );
}

function updateBkgReqReadState(id) {
  $.post(db + "bkgreqcomment/updateReadState.php", { id: id });
}

function deleteBkgReqComment(id) {
  var bkg;
  var comment;
  $.post(db + "bkgreqcomment/deleteComment.php", { id: id }, function(data) {
    if (data) bkg = JSON.parse(data);
  }).then(function() {
    $.get(db + "bkgreqcomment/getLatestComment.php?id=" + bkg, function(data) {
      if (data) comment = JSON.parse(data);
    }).then(function() {
      if (comment != undefined) {
        $("#bkgreq-" + bkg + "-notes").html(comment);
      } else {
        $("#bkgreq-" + bkg + "-notes").html("");
      }
      var prevCount = parseInt(
        $("#bkgreq-" + bkg + "-commentCount").html(),
        10
      );
      prevCount--;
      $("#bkgreq-" + bkg + "-commentCount").html(prevCount);
      $("#comment-" + id).fadeOut();
    });
  });
}

function printBkgReqList() {
  var printDialog = window.open("", "_blank", "top=50,left=50,scrollbars=yes");
  var reportPage = printDialog.document.body;
  $.post(
    dialog + "bookingRequests/printPendingBkgReq.php",
    { title: "Pending Booking Requests", header: "Pending Booking Requests" },
    function(data) {
      reportPage.innerHTML = data + $("#pendingBookingsList").html();
      setTimeout(function() {
        printDialog.print();
        printDialog.close();
      }, 500);
    }
  );
}
