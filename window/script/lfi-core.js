/**********************************
 *               CORE              *
 ***********************************/
// Set version number
var version = "b.4.6";

var thisPage = window.location.href;
thisPage = thisPage.slice(thisPage.lastIndexOf("/") + 1, thisPage.length);

var stateList = new Array();
var originList = new Array();
var destList = new Array();
var sscoList = new Array();

class API {
  constructor(route) {
    this.route = route;
  }

  get(url) {
    return new Promise((resolve, reject) => {
      fetch(this.route + url, { credentials: "same-origin" }).then(res => {
        resolve(res.json());
      });
    });
  }
  getCache(name, url) {
    return new Promise((resolve, reject) => {
      if (!localStorage.getItem(name)) {
        this.get(url).then(data => {
          localStorage.setItem(name, JSON.stringify(data));
          resolve(data);
        });
      } else {
        resolve(JSON.parse(localStorage.getItem(name)));
      }
    });
  }
  getSessionCache(name, url) {
    return new Promise((resolve, reject) => {
      if (!sessionStorage.getItem(name)) {
        this.get(url).then(data => {
          sessionStorage.setItem(name, JSON.stringify(data));
          resolve(data);
        });
      } else {
        resolve(JSON.parse(sessionStorage.getItem(name)));
      }
    });
  }
  post(url, obj) {
    let body = new FormData();
    for (var i in obj) {
      body.append(i, obj[i]);
    }
    return new Promise((resolve, reject) => {
      fetch(this.route + url, {
        method: "POST",
        body,
        credentials: "same-origin"
      }).then(res => {
        resolve(res.json());
      });
    });
  }
  purgeCache(type, name) {
    switch (type) {
      case "local":
        localStorage.removeItem(name);
        break;
      case "session":
        sessionStorage.removeItem(name);
        break;
    }
  }
}

const api = new API(db);

function get(url) {
  return new Promise((resolve, reject) => {
    fetch(url, { credentials: "same-origin" }).then(res => {
      resolve(res.json());
    });
  });
}

function post(url, obj) {
  let body = new FormData();
  for (var i in obj) {
    body.append(i, obj[i]);
  }
  return new Promise((resolve, reject) => {
    fetch(url, {
      method: "POST",
      body,
      credentials: "same-origin"
    }).then(res => {
      resolve(res.json());
    });
  });
}

$(document).ready(function() {
  if (thisPage != "login") {
    api.getCache("stateList", "static/stateList.json").then(data => {
      stateList = data;
    });

    api.getCache("originList", "originlist/getOriginList.php").then(data => {
      originList = data;
    });

    api.getCache("destList", "destlist/getDestList.php").then(data => {
      destList = data;
    });

    api.getSessionCache("sscoList", "ssco/getSscoList.php").then(data => {
      sscoList = data;
    });

    api
      .getSessionCache("shipperList", "shipper/getShipperList.php")
      .then(data => {
        var shippers = data;
        const shipperSelect = document.getElementById("shipperSelect");

        if (shippers.length > 1) {
          var content = "<option>Choose...</option>";
          for (var i = 0; i < shippers.length; i++) {
            content += `<option value="${shippers[i].shipperShort}">
                        ${shippers[i].shipperFull}
                      </option>`;
          }
          shipperSelect.innerHTML = content;
          shipperSelect.value = localStorage.getItem("shipper");
        } else {
          var content = "";
          for (var i = 0; i < shippers.length; i++) {
            content += `<option value="${shippers[i].shipperShort}">
                        ${shippers[i].shipperFull}
                      </option>`;
          }
          shipperSelect.innerHTML = content;
        }

        setTimeout(function() {
          refreshOptions();
        }, 50);
      });

    var notificationCount = 0;
    for (var i = 0, len = localStorage.length; i < len; ++i) {
      if (localStorage.key(i).indexOf("NTF-") == 0) {
        var data = localStorage.getItem(localStorage.key(i));
        var id = localStorage.key(i);
        var content = formatNotification(data);
        addToNotificationPane(id, content);
        notificationCount++;
      }
    }
    if (notificationCount > 0)
      $("#notificationCounter").html(notificationCount);

    setTimeout(function() {
      if (typeof EventSource !== "undefined") {
        var source = new EventSource(db + "general/notificationDaemon.php");
        source.onmessage = function(e) {
          if (!localStorage.getItem("NTF-" + e.lastEventId)) {
            localStorage.setItem("NTF-" + e.lastEventId, e.data);
            addNotification("NTF-" + e.lastEventId);
          }
        };
        // source.onerror = function(e) {
        //   console.log(e);
        //   source.close();
        //   $("#smallModal").modal("toggle");
        //   document.querySelector("#smallModalLabel").innerHTML = "ERROR";
        //   document.querySelector("#smallModalContent").innerHTML =
        //     "You are not signed in. Please refresh the page to sign in.";
        //   let footer =
        //     "<button class='btn btn-success' onclick='location.reload()'>Refresh Now</button>";
        //   document.querySelector("#smallModalFooter").innerHTML = footer;
        // };
      }
    }, 1000);

    if (document.getElementById("assignBkgReq")) {
      loadEmpList("assignBkgReq");
    }
    if (thisPage != "resources") {
      newResourceCheck();
    }

    // Auto logout
    setInterval(function() {
      var autoLogout = setAutoLogout();
      $("#smallModalLabel").html("Warning!");
      $("#smallModalContent").html(
        "You will be signed out in five minutes. Click 'Stay In' to remain signed in."
      );
      $("#smallModalFooter").html(
        "<button id='stayLoggedIn' class='btn btn-sm'>Stay In</button>"
      );
      $("#smallModal").modal("toggle");
      $("#stayLoggedIn").on("click", function() {
        clearTimeout(autoLogout);
        $("#smallModal").modal("toggle");
      });
    }, 36000000);
  }
});

function setAutoLogout() {
  return setTimeout(function() {
    window.location = "logout";
  }, 300000);
}

function loadEmpList(tar) {
  get(db + "users/getEmpList.php").then(data => {
    var empList = data;
    var content = "<option>Choose...</option>";
    for (var i = 0; i < empList.length; i++) {
      content +=
        "<option value='" +
        empList[i].UserID +
        "'>" +
        empList[i].UserName +
        "</option>";
    }
    document.getElementById(tar).innerHTML = content;
  });
}

function loadShipperList() {
  get(db + "shipper/getShipperList.php").then(data => {
    var shippers = data;
    const shipperSelect = document.getElementById("shipperSelect");

    if (shippers.length > 1) {
      var content = "<option>Choose...</option>";
      for (var i = 0; i < shippers.length; i++) {
        content += `<option value="${shippers[i].shipperShort}">
                        ${shippers[i].shipperFull}
                      </option>`;
      }
      shipperSelect.innerHTML = content;
      shipperSelect.value = localStorage.getItem("shipper");
    } else {
      var content = "";
      for (var i = 0; i < shippers.length; i++) {
        content += `<option value="${shippers[i].shipperShort}">
                        ${shippers[i].shipperFull}
                      </option>`;
      }
      shipperSelect.innerHTML = content;
    }
  });
}

function renderShipperList(tar) {
  var shippers = new Array();
  $.get(db + "shipper/getShipperList.php", function(data) {
    shippers = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < shippers.length; i++) {
      content +=
        '<option value="' +
        shippers[i].shipperShort +
        '">' +
        shippers[i].shipperFull +
        "</option>";
    }
    $(tar).html(content);
  });
}

function renderShipperAccounts(tar) {
  var shippers = new Array();
  $.get(db + "users/getShipperAccounts.php", function(data) {
    shippers = JSON.parse(data);
  }).then(function() {
    var content = "<option value='0'>Choose...</option>";
    for (var i = 0; i < shippers.length; i++) {
      content +=
        '<option value="' +
        shippers[i].UserID +
        '">' +
        shippers[i].UserName +
        " (" +
        shippers[i].Email +
        ")</option>";
    }
    $(tar).html(content);
  });
}

function newResourceCheck() {
  $.get(db + "resource/newResourceCheck.php", function(data) {
    if (data == "0") {
      $("#newResourceMarker").html(
        "<i class='fas fa-exclamation text-danger'></i>"
      );
    } else {
      $("#newResourceMarker").html("");
    }
  });
}

function getDisplayMode() {
  return new Promise((resolve, reject) => {
    $.get(db + "users/getDisplayMode.php", function(data) {
      resolve(JSON.parse(data));
    });
  });
}

$.ajaxSetup({
  // Disable caching of AJAX responses
  cache: false
});

function addRow(target, cellCount) {
  var newRow = target.insertRow(-1);
  var cells = [];
  for (var i = 0; i < cellCount; i++) {
    cells[i] = newRow.insertCell(i);
  }
  return cells;
}

function formatDate(date) {
  var monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
  ];

  var day = date.getDate();
  var monthIndex = date.getMonth();
  var year = date.getFullYear();

  return day + " " + monthNames[monthIndex] + " " + year;
}

function formatTime(date) {
  var hour = date.getHours();
  var min = date.getMinutes();
  var period;
  if (hour == 0) {
    hour = 12;
    period = "AM";
  } else if (hour <= 12) {
    period = "AM";
  } else if (hour > 12) {
    hour -= 12;
    period = "PM";
  }

  if (min.length < 2) min = "0" + min;

  return hour + ":" + min + " " + period;
}

function htmlEntities(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function displayLoading(tar) {
  $(tar).html(
    '<div class="text-center"><img src="' + image + 'loading.gif" /></div>'
  );
}

function sendDirectNotification(type, receiver) {
  // Sender is pulled from session data in PHP.
  $.post(db + "general/sendDirectNotification.php", {
    type: type,
    receiver: receiver
  });
}

function sendNewBkgReqNotification(type, shipper) {
  $.post(db + "bookingrequest/sendNotification.php", {
    type: type,
    shipper: shipper
  });
}

function addNotification(id) {
  localStorage.setItem("isNotificationViewed", "false");
  var notificationCount = $("#notificationCounter").html();
  var newCount = 0;
  if (notificationCount == "") {
    newCount = 1;
  } else {
    newCount = parseInt(notificationCount, 10) + 1;
  }
  $("#notificationCounter").html(newCount);
  var data = htmlEntities(localStorage.getItem(id));
  var content = formatNotification(data);
  addToNotificationPane(id, content);
  renderToast(content);
  if ("Notification" in window) {
    spawnNotification(id, data, image + "placeholder.jpg", "New Notification");
  }
  post(db + "general/logReceivedNotification.php", { content: data });
}

function formatNotification(data) {
  return "<div class='alert alert-dark'>" + data + "</div>";
}

function addToNotificationPane(id, content) {
  var rawDate = new Date();
  var date = formatDate(rawDate);
  var time = formatTime(rawDate);
  var notification =
    "<div id='" +
    htmlEntities(id) +
    "'>" +
    htmlEntities(date) +
    " " +
    htmlEntities(time) +
    "<br />" +
    content +
    "<br /><a class='float-right' onclick=\"markNotificationRead('" +
    htmlEntities(id) +
    "')\">Mark Read</a></div>";
  $("#notificationModalContent").prepend(notification);
  if (localStorage.getItem("isNotificationViewed") != "true")
    $("#notificationIcon")
      .addClass("fas")
      .removeClass("far")
      .css("color", "red");
}

function renderToast(content) {
  $("#toastModal").append(content);
  displayToast();
}

function displayToast() {
  $("#toastModal").fadeIn();
  setTimeout(function() {
    $("#toastModal").fadeOut(400, function() {
      $("#toastModal").html("");
    });
  }, 3000);
}

function spawnNotification(id, theBody, theIcon, theTitle) {
  var options = {
    body: theBody,
    icon: theIcon,
    requireInteraction: true
  };
  var n = new Notification(theTitle, options);
  // FOR LATER - pass what kind of notification this is,
  // then take to the relevant page.
  n.onclick = () => {
    window.focus();
    markNotificationViewed();
    markNotificationRead(id);
    if (thisPage == "bookingRequests") {
      loadPendingBookings();
    } else {
      window.location.href = "/bookingRequests";
    }
    n.close();
  };
  // setTimeout(n.close.bind(n), 5000);
}

function markNotificationViewed() {
  $("#notificationIcon")
    .addClass("far")
    .removeClass("fas")
    .css("color", "black");
  localStorage.setItem("isNotificationViewed", "true");
}

function markNotificationRead(ntf) {
  var id = ntf.slice(ntf.lastIndexOf("-") + 1, ntf.length);
  $.post(db + "general/markNotificationRead.php", { id: id }, function(data) {
    if (JSON.parse(data) == true) {
      localStorage.removeItem(ntf);
      $("#" + ntf).fadeOut();
      var notificationCount = $("#notificationCounter").html();
      newCount = notificationCount - 1;
      if (newCount > 0) {
        $("#notificationCounter").html(newCount);
      } else {
        newCount = "";
        $("#notificationCounter").html(newCount);
      }
    }
  });
}

function clearNotificationPane() {
  return 0; //$("#notificationModalContent").html("");
}

function initCorrectionListener() {
  var incompleteElems = document.getElementsByClassName("incompleteFormElem");
  for (var i = 0; i < incompleteElems.length; i++) {
    incompleteElems[i].addEventListener("change", function() {
      $(this).removeClass("incompleteFormElem");
    });
  }
}

// Prep a table for sorting. Comprises the next two lines.
var getCellValue = function(tr, idx) {
  return tr.children[idx].innerText || tr.children[idx].textContent;
};

var comparer = function(idx, asc) {
  return function(a, b) {
    return (function(v1, v2) {
      return v1 !== "" && v2 !== "" && !isNaN(v1) && !isNaN(v2)
        ? v1 - v2
        : v1.toString().localeCompare(v2);
    })(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
  };
};

function allowSortTable() {
  Array.from(document.querySelectorAll("th")).forEach(function(th) {
    th.addEventListener("click", function() {
      var table = th.closest("table");
      Array.from(table.querySelectorAll("tr:nth-child(n+2)"))
        .sort(
          comparer(
            Array.from(th.parentNode.children).indexOf(th),
            (this.asc = !this.asc)
          )
        )
        .forEach(function(tr) {
          table.appendChild(tr);
        });
    });
  });
  $("th").addClass("blueLink");
}

// About the app
function about() {
  var content = "<div class='text-center'><h3>Online Portal</h3>";
  content += "<i>Version " + version + "</i><br />";
  content += "Designed by <strong>John Wardell</strong><br />";
  content += "Copyright &copy; 2018 Freight Forwarder, Inc.</div>";
  var footer =
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
  $("#regularModalLabel").html("About");
  $("#regularModalContent").html(content);
  $("#regularModalFooter").html(footer);
}

// Terms of Service
function terms() {
  $("#largeModalLabel").html('Terms of Service ("Terms")');
  $("#largeModalContent").load(page + "partial/terms.html");
  var footer =
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
  $("#largeModalFooter").html(footer);
}

// Help
function help() {
  $("#largeModalLabel").html("Online Portal - Help");
  $("#largeModalContent").load(page + "partial/help.html");
  var footer =
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
  $("#largeModalFooter").html(footer);
}

// Change log
function changelog() {
  $("#regularModalLabel").html("Changelog");
  $("#regularModalContent").load(page + "partial/changelog.html");
  var footer =
    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
  $("#regularModalFooter").html(footer);
}

// Check to see if user is using IE.
function checkIE() {
  var ua = window.navigator.userAgent;
  var msie = ua.indexOf("MSIE ");

  if (msie > -1 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
    return true;
  } // If another browser, return 0
  else {
    return false;
  }
}

function checkChrome() {
  var isChromium = window.chrome;
  var winNav = window.navigator;
  var vendorName = winNav.vendor;
  var isOpera = typeof window.opr !== "undefined";
  var isIE = checkIE();
  var isEdge = winNav.userAgent.indexOf("Edge") > -1;
  var isIOSChrome = winNav.userAgent.match("CriOS");

  if (isIOSChrome) {
    return true;
  } else if (
    isChromium !== null &&
    typeof isChromium !== "undefined" &&
    vendorName === "Google Inc." &&
    isOpera === false &&
    isIE === false &&
    isEdge === false
  ) {
    return true;
  } else {
    return false;
  }
}

// Get the date.
function getDate() {
  var d = new Date();

  var month = d.getMonth() + 1;
  var day = d.getDate();

  var output =
    d.getFullYear() +
    "-" +
    (("" + month).length < 2 ? "0" : "") +
    month +
    "-" +
    (("" + day).length < 2 ? "0" : "") +
    day;

  return output;
}

function refreshOptions() {
  return 0;
}

/**********************************
 *       PORT TO PORT SEARCH       *
 ***********************************/

// Loads a list of origins for a specific shipper into a drop-down menu
function updateOriginList() {
  var shipper = $("#shipperSelect").val();

  var url = db + "origin/getOrigins.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "";
    content += "<option value='All'>All Origins</option>";
    for (var i = 0; i < result.length; i++) {
      var state = "";
      for (var s = 0; s < stateList.length; s++) {
        if (stateList[s].short == result[i].originState) {
          state = stateList[s].full;
        }
      }
      if (i == 0) {
        if (result[i].originStateFavorite == true) {
          content +=
            '<option value="STATE-' +
            result[i].originState +
            '" selected>' +
            state +
            "</option>";
        } else {
          content +=
            '<option value="STATE-' +
            result[i].originState +
            '">' +
            state +
            "</option>";
        }
      } else if (result[i].originState != result[i - 1].originState) {
        if (result[i].originStateFavorite == true) {
          content +=
            '<option value="STATE-' +
            result[i].originState +
            '" selected>' +
            state +
            "</option>";
        } else {
          content +=
            '<option value="STATE-' +
            result[i].originState +
            '">' +
            state +
            "</option>";
        }
      }
      if (result[i].originFavorite == true) {
        content +=
          '<option value="' +
          result[i].originShort +
          '" selected>&nbsp;&nbsp;&nbsp;' +
          result[i].originFull +
          "</option>";
      } else {
        content +=
          '<option value="' +
          result[i].originShort +
          '">&nbsp;&nbsp;&nbsp;' +
          result[i].originFull +
          "</option>";
      }
    }
    $("#originSelect").html(content);
  });
}

// Loads a list of destinations for a specific shipper into a drop-down menu
function updateDestinationList() {
  var shipper = $("#shipperSelect").val();

  var url = db + "destination/getDestinations.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "<option>Choose...</option>";
    for (var i = 0; i < result.length; i++) {
      if (result[i].destinationFavorite == true) {
        content +=
          '<option value="' +
          result[i].destinationShort +
          '" selected>' +
          result[i].destinationFull +
          "</option>";
      } else {
        content +=
          '<option value="' +
          result[i].destinationShort +
          '">' +
          result[i].destinationFull +
          "</option>";
      }
    }
    $("#destinationSelect").html(content);
  });
}

// Loads a list of SSCOs for a specific shipper into a drop-down menu
function updateSscoList() {
  var shipper = $("#shipperSelect").val();
  var url = db + "ssco/getShipperSSCO.php?shipper=" + shipper;
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var content = "<option>Choose...</option>";
    if (thisPage.indexOf("bookingRequests") !== -1)
      var content = "<option>Best Fit</option>";
    for (var i = 0; i < result.length; i++) {
      content +=
        '<option value="' +
        result[i].sscoShort +
        '">' +
        result[i].sscoFull +
        "</option>";
    }
    $("#sscoSelect").html(content);
    if (localStorage.getItem("ssco") != null) {
      $("#sscoSelect").val(localStorage.getItem("ssco"));
      loadRates();
      localStorage.removeItem("ssco");
    }
  });
}

// Loads a list of SSCOs for a specific shipper into a drop-down menu
function loadSscoList(shipper, target, select) {
  var url = "";
  if (shipper != "ALL") {
    url = db + "ssco/getShipperSSCO.php?shipper=" + shipper;
  } else {
    url = db + "ssco/getSscoList.php";
  }
  var result = new Array();

  $.get(url, function(data) {
    result = JSON.parse(data);
  }).then(function() {
    var content = "<option>Choose...</option>";
    if (thisPage.indexOf("bookingRequests") !== -1)
      var content = "<option>Best Fit</option>";
    for (var i = 0; i < result.length; i++) {
      var selected = select === result[i].sscoShort ? " selected" : "";
      content +=
        '<option value="' +
        result[i].sscoShort +
        '"' +
        selected +
        ">" +
        result[i].sscoFull +
        "</option>";
    }
    $(target).html(content);
  });
}

function addToArray(array, newEntry) {
  var add = true;
  for (var i = 0; i < array.length; i++) {
    if (array[i] == newEntry || newEntry == "") add = false;
  }
  return add;
}

class portToPort {
  constructor() {
    this.shipper = $("#shipperSelect").val();
    this.origin = $("#originSelect").val();
    this.destination = $("#destinationSelect").val();
    if ($("#saveSearch").is(":checked")) {
      this.saveSearch = true;
    } else {
      this.saveSearch = false;
    }
    this.rates = new Array();
  }

  getRates() {
    var url =
      db +
      "ssco/portToPort.php?shipper=" +
      this.shipper +
      "&origin=" +
      this.origin +
      "&destination=" +
      this.destination +
      "&saveSearch=" +
      this.saveSearch;

    return $.get(url, function(data) {
      this.rates = JSON.parse(data);
      this.rates.sort(function(a, b) {
        return parseFloat(a.rate) - parseFloat(b.rate);
      });
    });
  }

  loadRates() {
    var rates = this.rates;
    $("#regularModalLabel").html(
      "<h5 class='card-title'>Rate Search Results</h5>"
    );
    $("#regularModalContent").load(
      page + "partial/rates/portToPort.html",
      function() {
        var total = 0;
        var table = document.getElementById("portToPort");

        for (var i = 0; i < rates.length; i++) {
          var cells = addRow(table, 3);
          $(cells[0])
            .css({ minWidth: "70px", fontWeight: "bold" })
            .append(rates[i].ssco);
          $(cells[1]).append(rates[i].origin + "/" + rates[i].destination);
          $(cells[2])
            .addClass("portToPortRate")
            .append("$" + rates[i].rate);

          total += parseInt(rates[i].rate, 10);
        }

        var median = Math.ceil(rates.length / 2) - 1;

        // This used to display the average, now it's the median.
        // Just didn't want to rename the variable/elements.
        var average = rates[median].rate;

        if (!isNaN(average)) {
          if ($("#displayAverageRate").prop("checked") && rates.length > 1) {
            $("#averageRateContainer").css("display", "block");
            $("#averageRateField").html("$" + parseInt(average, 10));
          }
        } else {
          $("#averageRateContainer").css("display", "none");
          $("#rateMatrix").html(
            "<p>No rates were found with these criteria.</p>"
          );
        }

        $("#saveSearch").prop("checked", false);
      }
    );
  }
}

// Load results of Quick Search
function getPortToPort() {
  var result = new portToPort();
  displayLoading("#regularModalContent");
  result.getRates().then(result.loadRates);
}

function exportToExcel(table) {
  var url =
    "data:application/vnd.ms-excel," +
    encodeURIComponent($("#" + table).html());
  if (checkChrome()) {
    location.href = url;
    return false;
  } else {
    var exportDialog = window.open(
      url,
      "_blank",
      "top=50,left=50,width=10,height=10,scrollbars=no"
    );
    exportDialog.location.href = url;
  }
}
