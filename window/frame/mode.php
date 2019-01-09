<?php
if (login_check($db))
{
  if ($accountType == 1) {
?>
<!-- <label class="col-auto d-none d-sm-inline mr-sm-2" for="shipperSelect">Shipper</label> -->
<!-- <div class="btn-group mr-2">
  <button id="modeButton" type="button" style="width: 150px;" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Forwarder
  </button>
  <div class="dropdown-menu">
    <a class="dropdown-item" onclick="changeMode('Forwarder')">Forwarder</a>
    <a class="dropdown-item" onclick="changeMode('Shipper')">Shipper</a>
  </div>
</div> -->
<!-- <label class="d-none d-sm-inline mr-sm-2" for="shipperSelect">Shipper</label> -->
<select class="custom-select mb-2 mb-sm-0" id="shipperSelect" onchange="refreshOptions()">
</select>
<?php
  } elseif ($accountType == 2) {
?>
<select class="d-none" id="shipperSelect" onchange="refreshOptions()">
</select>
<?php
  }
}
?>
