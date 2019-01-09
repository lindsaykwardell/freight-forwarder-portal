function refreshOptions() {
  localStorage.setItem("shipper", $("#shipperSelect").val());
  updateOriginList();
  updateDestinationList();
}

function openGRI() {
  var ssco = new Array();
  var origin = new Array();

  $.get("data/ssco/getSscoList.php", function(data) {
    ssco = JSON.parse(data);
  }).then(function() {
    $.get("data/originlist/getOriginList.php", function(data) {
      origin = JSON.parse(data);
    }).then(function() {
      var content = "<div class='form-group'>";
      content += "<label for='sscoSelect'>Shipping Line</label>";
      content +=
        "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='sscoSelect'>";
      content += "<option>Choose...</option>";
      for (var i = 0; i < ssco.length; i++) {
        content +=
          '<option value="' +
          ssco[i].sscoShort +
          '">' +
          ssco[i].sscoFull +
          "</option>";
      }
      content += "</select></div><div class='form-group'>";
      content += "<label for='OriginSelect2'>Origin Port</label>";
      content +=
        "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='originSelect2'>";
      content += "<option value='All'>All Origins</option>";
      for (var i = 0; i < origin.length; i++) {
        if (i == 0) {
          content +=
            '<option value="STATE-' +
            origin[i].originState +
            '">' +
            origin[i].originState +
            "</option>";
        } else if (origin[i].originState != origin[i - 1].originState) {
          content +=
            '<option value="STATE-' +
            origin[i].originState +
            '">' +
            origin[i].originState +
            "</option>";
        }
        content +=
          '<option value="' +
          origin[i].originShort +
          '">&nbsp;&nbsp;&nbsp;' +
          origin[i].originFull +
          "</option>";
      }
      content += "</select></div><div class='form-group'>";
      content += "<label for='griInput'>Enter GRI amount</label>";
      content +=
        "<input type='number' class='form-control' id='griInput' placeholder='0'>";
      content += "</div>";
      content +=
        "<button class='btn btn-primary' onclick='runGRI()'>Apply GRI</button>";
      content += "<div id='returnResponse'></div>";

      $("#adminDisplay").html(content);
    });
  });
}

function runGRI() {
  var gri = parseInt($("#griInput").val(), 10);
  var ssco = $("#sscoSelect").val();
  var origin = $("#originSelect2").val();

  if (isNaN(gri) == false) {
    $.post(
      "data/oceanfreight/applyGRI.php",
      { ssco: ssco, gri: gri, origin: origin },
      function(data) {
        if (JSON.parse(data) == true) {
          $("#returnResponse").html("GRI applied successfully.");
        } else {
          $("#returnResponse").html("An error occured.");
        }
      }
    );
  } else {
    $("#returnResponse").html("Please type a number into the field.");
  }
}

function contractExpireReport() {
  var url = "data/contract/getContractList.php";
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var contracts = new Array();
    for (var i = 0; i < result.length; i++) {
      var range = new Date();
      range.setMonth(range.getMonth() + 1);
      if (
        Date.parse(result[i].contractEnd) < range ||
        result[i].contractEnd == ""
      ) {
        contracts.push(result[i]);
      }
    }
    var content =
      "<button class='btn btn-success btn-sm hideMe' onclick='printReport(\"Contracts to Expire\")'>Print</button><br /><table><tr><th>Carrier</th><th>Shipper</th><th>Contract Number</th><th>Expiration Date</th></tr>";

    for (var i = 0; i < contracts.length; i++) {
      content +=
        "<tr class='border-bottom'><td style='min-width: 125px;'>" +
        contracts[i].contractSsco +
        "</td><td style='min-width: 125px;'>" +
        contracts[i].contractShipper +
        "</td><td style='min-width: 150px;'>" +
        contracts[i].contractNumber +
        "</td><td style='min-width: 125px;'>" +
        contracts[i].contractEnd +
        "</td></tr>";
    }
    content += "</table>";

    $("#adminDisplay").html(content);

    allowSortTable();
  });
}

function openContractsBySsco() {
  var ssco = new Array();

  $.get("data/ssco/getSscoList.php", function(data) {
    ssco = JSON.parse(data);
  }).then(function() {
    var content = "<div class='form-group hideMe'>";
    content += "<label for='sscoSelect'>Shipping Line</label>";
    content +=
      "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='sscoSelect'>";
    content += "<option>Choose...</option>";
    for (var i = 0; i < ssco.length; i++) {
      content +=
        '<option value="' +
        ssco[i].sscoShort +
        '">' +
        ssco[i].sscoFull +
        "</option>";
    }
    content += "</select></div>";
    content +=
      "<button class='btn btn-primary hideMe' onclick='contractsBySscoReport()'>Get Contracts by Carrier</button>";
    content += "<div id='returnResponse'></div>";

    $("#adminDisplay").html(content);
  });
}

function contractsBySscoReport() {
  var ssco = $("#sscoSelect").val();
  var url = "data/contract/getContractList.php?ssco=" + ssco;
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var contracts = new Array();
    for (var i = 0; i < result.length; i++) {
      contracts.push(result[i]);
    }
    var content =
      "<button class='btn btn-success btn-sm hideMe' onclick='printReport(\"Contracts by Carrier\")'>Print</button><br /><table><tr><th>Carrier</th><th>Shipper</th><th>Contract Number</th><th>Expiration Date</th></tr>";

    for (var i = 0; i < contracts.length; i++) {
      content +=
        "<tr class='border-bottom'><td style='min-width: 125px;'>" +
        contracts[i].contractSsco +
        "</td><td style='min-width: 125px;'>" +
        contracts[i].contractShipper +
        "</td><td style='min-width: 150px;'>" +
        contracts[i].contractNumber +
        "</td><td style='min-width: 125px;'>" +
        contracts[i].contractEnd +
        "</td></tr>";
    }
    content += "</table>";

    $("#returnResponse").html(content);

    allowSortTable();
  });
}

function compareShipperRates() {
  var ssco = new Array();
  var origin = new Array();
  var destination = new Array();

  $.get("data/ssco/getSscoList.php", function(data) {
    ssco = JSON.parse(data);
  }).then(function() {
    $.get("data/originlist/getOriginList.php", function(data) {
      origin = JSON.parse(data);
    }).then(function() {
      $.get("data/destlist/getDestList.php", function(data) {
        destination = JSON.parse(data);
      }).then(function() {
        var content = "<div class='form-group'>";
        content += "<label for='sscoSelect'>Shipping Line</label>";
        content +=
          "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='sscoSelect'>";
        content += "<option>Choose...</option>";
        for (var i = 0; i < ssco.length; i++) {
          content +=
            '<option value="' +
            ssco[i].sscoShort +
            '">' +
            ssco[i].sscoFull +
            "</option>";
        }
        content += "</select></div><div class='form-group'>";
        content += "<label for='originSelect2'>Origin Port</label>";
        content +=
          "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='originSelect2'>";
        for (var i = 0; i < origin.length; i++) {
          content +=
            '<option value="' +
            origin[i].originShort +
            '">' +
            origin[i].originFull +
            "</option>";
        }
        content += "</select></div><div class='form-group'>";
        content += "<label for='destinationSelect2'>Destination Port</label>";
        content +=
          "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='destinationSelect2'>";
        for (var i = 0; i < destination.length; i++) {
          content +=
            '<option value="' +
            destination[i].destinationShort +
            '">' +
            destination[i].destinationFull +
            "</option>";
        }
        content += "</select></div>";
        content +=
          "<button class='btn btn-primary' onclick='getRatesForComparison()'>Search Rates</button>";
        content += "<div id='returnResponse' class='mt-4'></div>";

        $("#adminDisplay").html(content);
      });
    });
  });
}

function getRatesForComparison() {
  var ssco = $("#sscoSelect").val();
  var origin = $("#originSelect2").val();
  var destination = $("#destinationSelect2").val();
  var shippers = new Array();
  $("#returnResponse").html("");

  $.get("data/shipper/getShipperList.php", function(data) {
    shippers = JSON.parse(data);
  }).then(function() {
    for (var i = 0; i < shippers.length; i++) {
      var rate = new Array();
      var url =
        "data/oceanfreight/getRate.php?ssco=" +
        ssco +
        "&shipper=" +
        shippers[i].shipperShort +
        "&origin=" +
        origin +
        "&destination=" +
        destination;
      $.get(url, function(data) {
        rate = JSON.parse(data);
        if (rate) {
          for (var s = 0; s < shippers.length; s++) {
            if (shippers[s].shipperShort == rate[0].shipper) {
              var content =
                "<div class='row border-bottom'><div class='col-lg-3 col-md-4 col-sm-6'>" +
                shippers[s].shipperFull +
                "</div><div class='col-1'>" +
                rate[0].rate +
                "</div></div>";
              $("#returnResponse").append(content);
            }
          }
        }
      });
    }
  });
}

function ratesOverTime() {
  var origin = new Array();
  var destination = new Array();

  $.get("data/originlist/getOriginList.php", function(data) {
    origin = JSON.parse(data);
  }).then(function() {
    $.get("data/destlist/getDestList.php", function(data) {
      destination = JSON.parse(data);
    }).then(function() {
      var content =
        "<div class='row'><div class='col-6'><div class='form-group'>";
      content += "<label for='originSelect2'>Origin Port</label>";
      content +=
        "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='originSelect2'>";
      for (var i = 0; i < origin.length; i++) {
        content +=
          '<option value="' +
          origin[i].originShort +
          '">' +
          origin[i].originFull +
          "</option>";
      }
      content +=
        "</select></div></div><div class='col-6'><div class='form-group'>";
      content += "<label for='destinationSelect2'>Destination Port</label>";
      content +=
        "<select class='col custom-select mb-2 mr-sm-2 mb-sm-0' id='destinationSelect2'>";
      for (var i = 0; i < destination.length; i++) {
        content +=
          '<option value="' +
          destination[i].destinationShort +
          '">' +
          destination[i].destinationFull +
          "</option>";
      }
      content += "</select></div></div></div>";
      content +=
        "<button class='btn btn-primary' onclick='getAverageRate()'>Search Rates</button>";
      content +=
        "<div class='form-check'><input type='checkbox' class='form-check-input' id='saveAverageRate'><label class='form-check-label' for='saveAverageRate'>Save Result</label></div>";
      content +=
        "<div class='row'><div id='returnResponse' class='col-3 ymt-4'></div></div>";

      $("#adminDisplay").html(content);
    });
  });
}

function getAverageRate() {
  var origin = $("#originSelect2").val();
  var destination = $("#destinationSelect2").val();
  var result = new Array();

  $.get(
    db +
      "oceanfreight/getAllRatesForRoute.php?origin=" +
      origin +
      "&destination=" +
      destination,
    function(data) {
      if (data) result = JSON.parse(data);
    }
  ).then(function() {
    var total = 0;
    var count = result.length;

    for (var i = 0; i < result.length; i++) {
      total += parseInt(result[i], 10);
    }

    var average = Math.floor(total / count);

    var averages = new Array();

    if ($("#saveAverageRate").prop("checked")) {
      $.post(db + "oceanfreight/addAverageRate.php", {
        origin: origin,
        destination: destination,
        rate: average
      }).then(function() {
        $.get(
          db +
            "oceanfreight/loadAverageRates.php?origin=" +
            origin +
            "&destination=" +
            destination,
          function(data) {
            if (data) averages = JSON.parse(data);
          }
        ).then(function() {
          var content = "<p>The current average rate is " + average + "</p>";
          var prevRate = 0;
          for (var i = 0; i < averages.length; i++) {
            var diff = prevRate - averages[i].rate;
            var showDiff = "";
            if (i > 0) {
              if (diff < 0) showDiff = "Reduced by " + Math.abs(diff);
              if (diff > 0) showDiff = "Increased by " + diff;
              if (diff == 0) showDiff = "No change.";
            }
            content +=
              averages[i].date +
              ": " +
              averages[i].rate +
              " | " +
              showDiff +
              "<br/>";
            prevRate = averages[i].rate;
          }

          $("#returnResponse").html(content);
        });
      });
    } else {
      $.get(
        db +
          "oceanfreight/loadAverageRates.php?origin=" +
          origin +
          "&destination=" +
          destination,
        function(data) {
          if (data) averages = JSON.parse(data);
        }
      ).then(function() {
        var content = "<p>The current average rate is " + average + "</p>";
        var prevRate = average;
        for (var i = 0; i < averages.length; i++) {
          var diff = prevRate - averages[i].rate;
          var showDiff = "";
          if (diff < 0) showDiff = "Reduced by " + Math.abs(diff);
          if (diff > 0) showDiff = "Increased by " + diff;
          if (diff == 0) showDiff = "No change.";
          content +=
            averages[i].date +
            ": " +
            averages[i].rate +
            " | " +
            showDiff +
            "<br/>";
          prevRate = averages[i].rate;
        }

        $("#returnResponse").html(content);
      });
    }
  });
}

function viewAdmLog() {
  var log;
  $.get(db + "general/readLog.php?len=200", function(data) {
    if (data) log = JSON.parse(data);
  }).then(function() {
    var content =
      "<div class='mb-3'><a class='blueLink' onclick='downloadAdmLog()'>Download log</a></div>";
    for (var i = log.length - 1; i >= 0; i--) {
      content += log[i] + "<br />";
    }
    $("#adminDisplay").html(content);
  });
}

function downloadAdmLog() {
  var log;
  $.get(db + "general/readLog.php", function(data) {
    if (data) log = JSON.parse(data);
  }).then(function() {
    var printLog = window.open(
      "",
      "_blank",
      "top=50,left=50,width=750,height=800,scrollbars=yes"
    );
    var logPage = printLog.document.body;
    for (var i = log.length - 1; i >= 0; i--) {
      $(logPage).prepend(log[i] + "<br />");
    }
  });
}

function printReport(name) {
  var printDialog = window.open(
    "",
    "_blank",
    "top=50,left=50,width=750,height=800,scrollbars=yes"
  );
  var reportPage = printDialog.document.body;
  $.post(
    dialog + "printReport.php",
    { title: name.toUpperCase(), header: name },
    function(data) {
      reportPage.innerHTML = data + $("#adminDisplay").html();
      setTimeout(function() {
        printDialog.print();
        printDialog.close();
      }, 200);
    }
  );
}

function newShipperForm() {
  $("#adminDisplay").load(page + "partial/admin/shipper.html", function() {
    $("#admShipperTitle").html("New Shipper");
    $("#saveShipperBtn").on("click", function() {
      $("#returnResponse").html("");
      addNewShipper();
    });
    loadEmpList("assignEmp");
  });
}

function addNewShipper() {
  var shipperShort = $("#shipperShort").val();
  var shipperFull = $("#shipperFull").val();
  var shipperPhone = $("#shipperPhone").val();
  var shipperAddress = $("#shipperAddress").val();
  var shipperNote = $("#shipperNote").val();
  var shipperAssigned = $("#assignEmp").val();

  $.post(
    db + "shipper/addNewShipper.php",
    {
      shipperShort: shipperShort,
      shipperFull: shipperFull,
      shipperPhone: shipperPhone,
      shipperAddress: shipperAddress,
      shipperNote: shipperNote,
      shipperAssigned: shipperAssigned
    },
    function(data) {
      if (JSON.parse(data) === true) {
        $("#returnResponse").html(
          "Shipper " + shipperFull + " has been added successfully."
        );
      } else {
        $("#returnResponse").html("An error occurred.");
      }
    }
  ).then(function() {
    loadShipperList();
  });
}

function modifyShipperForm() {
  $("#adminDisplay").load(page + "partial/admin/shipper.html", function() {
    $("#admShipperTitle").html("Update Shipper");
    $("#saveShipperBtn").on("click", function() {
      $("#returnResponse").html("");
      updateShipper();
    });
    loadShipperDataForEdit();
    $("#shipperSelect").on("change", function() {
      loadShipperDataForEdit();
    });
    loadEmpList("assignEmp");
  });
}

function loadShipperDataForEdit() {
  var shipper = $("#shipperSelect").val();

  $.get(db + "shipper/getShipperDetails.php?shipper=" + shipper, function(
    data
  ) {
    if (data) shipper = JSON.parse(data);
  }).then(function() {
    $("#shipperShort")
      .val(shipper.Short)
      .prop("disabled", true);
    $("#shipperFull").val(shipper.Full);
    $("#shipperPhone").val(shipper.Phone);
    $("#shipperAddress").val(shipper.Address);
    $("#shipperNote").val(shipper.Note);
    setTimeout(function() {
      $("#assignEmp").val(shipper.AssignedTo);
    }, 200);
  });
}

function updateShipper() {
  var shipperShort = $("#shipperShort").val();
  var shipperFull = $("#shipperFull").val();
  var shipperPhone = $("#shipperPhone").val();
  var shipperAddress = $("#shipperAddress").val();
  var shipperNote = $("#shipperNote").val();
  var shipperAssigned = $("#assignEmp").val();

  $.post(
    db + "shipper/updateShipper.php",
    {
      shipperShort: shipperShort,
      shipperFull: shipperFull,
      shipperPhone: shipperPhone,
      shipperAddress: shipperAddress,
      shipperNote: shipperNote,
      shipperAssigned: shipperAssigned
    },
    function(data) {
      if (JSON.parse(data) === true) {
        $("#returnResponse").html(
          "Shipper " + shipperFull + " has been updated successfully."
        );
      } else {
        $("#returnResponse").html("An error occurred.");
      }
    }
  ).then(function() {
    loadShipperList();
  });
}

function deleteShipperForm() {
  var content = "";
  content += "<select id='deleteShipperList' class='custom-select'></select>";
  content +=
    "<button id='deleteShipperButton' class='btn btn-outline-danger'>Delete Shipper</button>";
  content += "<div id='returnResponse'></div>";
  $("#adminDisplay").html(content);
  $("#deleteShipperButton").on("click", function() {
    confirmDeleteShipper();
  });
  renderShipperList("#deleteShipperList");
}

function confirmDeleteShipper() {
  $("#deleteShipperButton")
    .removeClass("btn-outline-danger")
    .addClass("btn-danger")
    .html("Confirm Delete")
    .off()
    .on("click", function() {
      deleteShipper();
    });
}

function deleteShipper() {
  var shipper = $("#deleteShipperList").val();

  $.post(db + "shipper/deleteShipper.php", { shipper: shipper }, function(
    data
  ) {
    if (data) {
      $("#deleteShipperButton")
        .removeClass("btn-danger")
        .addClass("btn-outline-danger")
        .html("Delete Shipper")
        .off()
        .on("click", function() {
          confirmDeleteShipper();
        });
      $("#returnResponse").html("Shipper " + shipper + " has been deleted.");
      renderShipperList("#deleteShipperList");
    }
  });
}

function attachShipperAccountForm() {
  var content = "";
  content += "<div class='row'>";
  content += "<div class='col'><label for='attachAccountList'>Account</label>";
  content +=
    "<select id='attachAccountList' class='custom-select'></select></div>";
  content += "<div class='col'><label for='attachShipperList'>Shipper</label>";
  content +=
    "<select id='attachShipperList' class='custom-select'></select></div>";
  content += "</div>";
  content +=
    "<button id='attachShipperButton' class='btn btn-primary'>Attach Account</button>";
  content += "<div id='returnResponse'></div>";
  $("#adminDisplay").html(content);
  $("#attachAccountList").on("change", function() {
    var id = $("#attachAccountList").val();
    var selected;
    $.get(db + "users/getUserAssignedAccount.php?id=" + id, function(data) {
      if (data) selected = JSON.parse(data);
    }).then(function() {
      $("#attachShipperList").val(selected);
    });
  });
  $("#attachShipperButton").on("click", function() {
    // Save new pairing of shipper/account to account.
    var shipper = $("#attachShipperList").val();
    var account = $("#attachAccountList").val();

    $.post(
      db + "users/pairToShipper.php",
      { shipper: shipper, account: account },
      function(data) {
        if (data) {
          $("#returnResponse").html("Account and Shipper paired successfully.");
        } else {
          $("#returnResponse").html("An error occurred");
        }
      }
    );
  });
  renderShipperList("#attachShipperList");
  renderShipperAccounts("#attachAccountList");
}

function newSscoForm() {
  $("#adminDisplay").load(page + "partial/admin/ssco.html", function() {
    $("#admSscoTitle").html("New Carrier");
    $("#admSscoSelectCol").css("display", "none");
    $("#saveSscoBtn").on("click", function() {
      $("#returnResponse").html("");
      addNewSsco();
    });
  });
}

function addNewSsco() {
  var sscoShort = $("#sscoShort").val();
  var sscoFull = $("#sscoFull").val();
  var perContainer = $("#perContainer").val();
  var sscoNote = $("#sscoNote").val();

  $.post(
    db + "ssco/addNewSsco.php",
    {
      sscoShort: sscoShort,
      sscoFull: sscoFull,
      perContainer: perContainer,
      sscoNote: sscoNote
    },
    function(data) {
      if (JSON.parse(data) === true) {
        $("#returnResponse").html(
          "Carrier " + sscoFull + " has been added successfully."
        );
      } else {
        $("#returnResponse").html("An error occurred.");
      }
    }
  );
}

function modifySscoForm() {
  $("#adminDisplay").load(page + "partial/admin/ssco.html", function() {
    $("#admSscoTitle").html("Update Carrier");
    $("#sscoShort").prop("disabled", true);
    $("#saveSscoBtn").on("click", function() {
      $("#returnResponse").html("");
      updateSsco();
    });
    loadSscoList("ALL", "#admSscoSelect");
    $("#admSscoSelect").on("change", function() {
      loadSscoDataForEdit();
    });
  });
}

function loadSscoDataForEdit() {
  var ssco = $("#admSscoSelect").val();

  $.get(db + "ssco/getSscoDetails.php?ssco=" + ssco, function(data) {
    if (data) ssco = JSON.parse(data);
  }).then(function() {
    $("#sscoShort").val(ssco.Short);
    $("#sscoFull").val(ssco.Full);
    $("#perContainer").val(ssco.PerContainer);
    $("#sscoNote").val(ssco.Note);
  });
}

function updateSsco() {
  var sscoShort = $("#sscoShort").val();
  var sscoFull = $("#sscoFull").val();
  var perContainer = $("#perContainer").val();
  var sscoNote = $("#sscoNote").val();

  $.post(
    db + "ssco/updateSsco.php",
    {
      sscoShort: sscoShort,
      sscoFull: sscoFull,
      perContainer: perContainer,
      sscoNote: sscoNote
    },
    function(data) {
      if (JSON.parse(data) === true) {
        $("#returnResponse").html(
          "Carrier " + sscoFull + " has been updated successfully."
        );
      } else {
        $("#returnResponse").html("An error occurred.");
      }
    }
  ).then(function() {
    loadSscoList("ALL", "#admSscoSelect");
  });
}

function deleteSscoForm() {
  var content = "";
  content += "<select id='deleteSscoList' class='custom-select'></select>";
  content +=
    "<button id='deleteSscoButton' class='btn btn-outline-danger'>Delete Carrier</button>";
  content += "<div id='returnResponse'></div>";
  $("#adminDisplay").html(content);
  $("#deleteSscoButton").on("click", function() {
    confirmDeleteSsco();
  });
  loadSscoList("ALL", "#deleteSscoList");
}

function confirmDeleteSsco() {
  $("#deleteSscoButton")
    .removeClass("btn-outline-danger")
    .addClass("btn-danger")
    .html("Confirm Delete")
    .off()
    .on("click", function() {
      deleteSsco();
    });
}

function deleteSsco() {
  var ssco = $("#deleteSscoList").val();

  $.post(db + "ssco/deleteSsco.php", { ssco: ssco }, function(data) {
    if (data) {
      $("#deleteSscoButton")
        .removeClass("btn-danger")
        .addClass("btn-outline-danger")
        .html("Delete Ssco")
        .off()
        .on("click", function() {
          confirmDeleteSsco();
        });
      $("#returnResponse").html("Carrier " + ssco + " has been deleted.");
      loadSscoList("ALL", "#deleteSscoList");
    }
  });
}
