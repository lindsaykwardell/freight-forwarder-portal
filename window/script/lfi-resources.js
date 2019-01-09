function refreshOptions() {
  localStorage.setItem("shipper", $("#shipperSelect").val());
  updateOriginList();
  updateDestinationList();
}

$(document).ready(function() {
  getStickyList();
  getResources();
  $.get(db + "resource/newResourceCheck.php", function(data) {
    if (data == "0") {
      $.post(db + "resource/markViewed.php");
    }
  });
});

function getStickyList() {
  var result = new Array();
  $.get(db + "resource/getStickyList.php", function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    var content = "<ul class='list-group'>";
    var content =
      "<li class='resourceStickyList list-group-item list-group-item-action' onclick='getResources()'><h4>View All</h4></li>";
    for (var i = 0; i < result.length; i++) {
      if (i == 0 || result[i].Category != result[i - 1].Category) {
        if (i != 0) content += "</div>";
        content +=
          "<li class='resourceStickyList list-group-item list-group-item-action' onclick=\"$('#category-" +
          i +
          "').slideToggle()\"><h5>" +
          result[i].Category +
          "</h5></li>";
        content += "<div id='category-" + i + "' style='display: none'>";
      }
      content +=
        "<li class='resourceStickyList resourceItem list-group-item list-group-item-action' onclick=\"getResources('" +
        result[i].ID +
        "')\"><span class='ml-3'>";
      if (result[i].ExtLink != 0)
        content +=
          "<a href='" +
          result[i].ExtLink +
          "' target='_blank' onclick='event.stopPropagation();'><i class='fas fa-external-link-alt mr-2'></i></a>";
      content +=
        "<span style='font-variant: small-caps;'>" + result[i].Name + "</span>";
      content += "</span></li>";
    }
    content += "</div>";
    content += "</ul>";
    $("#stickyList").html(content);
  });
}

function getResources(key) {
  displayLoading("#resourceItems");
  if (key == undefined) key = "ALL";
  var result = new Array();
  $.get(db + "resource/getResource.php?key=" + key, function(data) {
    if (data) result = JSON.parse(data);
  }).then(function() {
    renderResources(result).then(content => {
      $("#resourceItems").html(content);
      if (key == "ALL") {
        var search = "<div class='input-group mb-3'>";
        search += "<div class='input-group-prepend'>";
        search +=
          "<span class='input-group-text' id='inputGroup-sizing-default'><i class='fas fa-search'></i>&nbsp;&nbsp;Search</span>";
        search += "</div>";
        search +=
          "<input id='searchResourcesBox' onchange='searchResources()' type='text' class='form-control' aria-label='Default' aria-describedby='inputGroup-sizing-default' placeholder='Enter a query, and press Enter'>";
        search += "</div>";
        $("#resourceItems").prepend(search);
      }
    });
  });
}

function searchResources() {
  var key = $("#searchResourcesBox").val();
  var result = new Array();
  $.post(db + "resource/searchResources.php", { key: key }, function(data) {
    if (data) result = JSON.parse(data);
  }).then(async function() {
    var content = await renderResources(result);
    $("#resourceItems").html(content);
    var search = "<div class='input-group mb-3'>";
    search += "<div class='input-group-prepend'>";
    search +=
      "<span class='input-group-text' id='inputGroup-sizing-default'><i class='fas fa-search'></i>&nbsp;&nbsp;Search</span>";
    search += "</div>";
    search +=
      "<input id='searchResourcesBox' onchange='searchResources()' type='text' class='form-control' aria-label='Default' aria-describedby='inputGroup-sizing-default' placeholder='Enter a query, and press Enter' value='" +
      key +
      "'>";
    search += "</div>";
    $("#resourceItems").prepend(search);
  });
}

function renderResources(result) {
  return new Promise((resolve, reject) => {
    getDisplayMode().then(displayMode => {
      var content = "";
      for (var i = 0; i < result.length; i++) {
        var extLinkDisplay = result[i].ExtLink;
        if (result[i].ExtLink.length > 50)
          extLinkDisplay = result[i].ExtLink.slice(0, 49) + "...";

        content += "<div class='card mb-3' id='resource-" + result[i].ID + "'>";
        content += "<div class='card-header bg-navy text-white'>";
        content +=
          "<h5 class='card-title d-inline mr-4'>" + result[i].Name + "</h5>";
        content += "<small class='d-inline'>" + result[i].Date + "</small>";
        if (displayMode == 1) {
          content += "<span class='float-right'>";
          content +=
            "<i class='fas fa-trash mr-4 text-danger' data-toggle='modal' data-target='#smallModal' onclick=\"confirmResourceDelete('" +
            result[i].ID +
            "')\" style='cursor: pointer'></i>";
          content +=
            "<i class='fas fa-pencil-alt mr-4 text-success' onclick=\"editResource('" +
            result[i].ID +
            "')\" style='cursor: pointer'></i>";
          content += result[i].Sticky
            ? "<i id='stickyStatus-" +
              result[i].ID +
              "' class='fas fa-thumbtack' style='cursor: pointer' onclick=\"toggleSticky('" +
              result[i].ID +
              "')\"></i>"
            : "<i id='stickyStatus-" +
              result[i].ID +
              "' class='fas fa-thumbtack text-muted' style='cursor: pointer' onclick=\"toggleSticky('" +
              result[i].ID +
              "')\"></i>";
          content += "</span>";
        }
        content += "</div>";
        content += "<div class='card-body'>";
        if (result[i].ExtLink != 0)
          content +=
            "<a href='" +
            result[i].ExtLink +
            "' target='_blank'><i class='fas fa-external-link-alt mr-2 mb-4'></i> " +
            extLinkDisplay +
            "</a>";
        content += result[i].Content;
        content += "</div>";
        content += "</div>";
      }
      resolve(content);
    });
  });
}

function renderNewResourceForm() {
  $("#resourceItems").prepend("<div id='newResourceForm'></div>");
  $("#newResourceForm").load(page + "partial/resources/newResourceForm.html");
  setTimeout(function() {
    $("#newResourceTextarea").ckeditor();
    $("#submitResource").on("click", function() {
      submitNewResource();
    });
    loadResourceCategories();
  }, 200);
}

function editResource(id) {
  $("#resource-" + id).load(page + "partial/resources/newResourceForm.html");
  setTimeout(function() {
    $("#newResourceTextarea").ckeditor();
    $("#submitResource").on("click", function() {
      updateResource(id);
    });
    loadResourceCategories();
    $.get(db + "resource/getResource.php?key=" + id, function(data) {
      if (data) result = JSON.parse(data);
    }).then(function() {
      $("#newResourceName").val(result[0].Name);
      $("#newResourceTextarea").val(result[0].Content);
      $("#resourceCategory").val(result[0].Category);
      if (result[0].ExtLink != 0) $("#resourceExtLink").val(result[0].ExtLink);
    });
  }, 200);
}

function loadResourceCategories() {
  var cats = [];
  $.get(db + "resource/getCategoryList.php", function(data) {
    if (data) cats = JSON.parse(data);
  }).then(function() {
    var content = "";
    for (var i = 0; i < cats.length; i++) {
      if (cats[i] != 0) content += "<option value='" + cats[i] + "'>";
    }
    $("#resourceCategoryList").html(content);
  });
}

function submitNewResource() {
  var name = $("#newResourceName").val();
  var extLink = $("#resourceExtLink").val();
  if (extLink.length > 0 && extLink.indexOf("http") === -1)
    extLink = "http://" + extLink;
  var category = $("#resourceCategory").val();
  var content = $("#newResourceTextarea").val();

  $.post(db + "resource/addNewResource.php", {
    name: name,
    extLink: extLink,
    category: category,
    content: content
  }).then(function() {
    $("#resourceItems").html("Resource added successfully");
    getStickyList();
  });
}

function updateResource(id) {
  var name = $("#newResourceName").val();
  var extLink = $("#resourceExtLink").val();
  if (extLink.length > 0 && extLink.indexOf("http") === -1)
    extLink = "http://" + extLink;
  var category = $("#resourceCategory").val();
  var content = $("#newResourceTextarea").val();

  $.post(db + "resource/updateResource.php", {
    id: id,
    name: name,
    extLink: extLink,
    category: category,
    content: content
  }).then(function() {
    $("#resourceItems").html("Resource updated successfully");
    getStickyList();
  });
}

function toggleSticky(id) {
  if ($("#stickyStatus-" + id).hasClass("text-muted")) {
    $("#stickyStatus-" + id).removeClass("text-muted");
    updateStickyList(id, "1");
  } else {
    $("#stickyStatus-" + id).addClass("text-muted");
    updateStickyList(id, "0");
  }
}

function updateStickyList(id, state) {
  $.post(
    db + "resource/updateStickyStatus.php",
    { id: id, state: state },
    function(data) {
      if (data) getStickyList();
    }
  );
}

function confirmResourceDelete(id) {
  $("#smallModalLabel").html("Confirm Deletion");
  displayLoading("#smallModalContent");
  var content =
    "Are you sure you want to delete this resource? This cannot be undone.";
  var footer =
    "<button type='button' class='btn btn-danger' data-dismiss='modal' onclick=\"deleteResource('" +
    id +
    "')\">Delete</button>";
  footer +=
    "<button type='button' class='btn btn-grey' data-dismiss='modal'>Close</button>";
  $("#smallModalContent").html(content);
  $("#smallModalFooter").html(footer);
}

function deleteResource(id) {
  $.post(db + "resource/deleteResource.php", { id: id }, function(data) {
    if (data) {
      getStickyList();
      $("#resourceItems").html("Resource deleted.");
    }
  });
}
