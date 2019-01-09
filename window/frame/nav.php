<?php
if(login_check($db))
{
?>
<div class="row navSection navbar-dark bg-dark mx-1">
  <nav class="navbar navbar-expand-md navbar-dark bg-dark col">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#primaryNavBar" aria-controls="primaryNavBar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="form-group mr-4">
      <?php include $frame . "mode.php"; ?>
    </div>
    <div class="collapse navbar-collapse" id="primaryNavBar">
      <ul class="navbar-nav flex-column flex-md-row">
        <!-- <li id="homeLink" class="nav-item">
          <a class="nav-link" href="<?php echo $dir; ?>home">Home</a>
        </li> -->
        <li id="bookingsLink" class="nav-item">
          <a class="nav-link" href="<?php echo $dir; ?>bookingRequests">Bookings</a>
        </li>
        <?php if ($accountType == 1) { ?>
          <li id="ratesLink" class="nav-item">
            <a class="nav-link" href="<?php echo $dir; ?>rates">Contracts</a>
          </li>
        <?php } ?>
        <li id="resourcesLink" class="nav-item">
          <a class="nav-link" href="<?php echo $dir; ?>resources">Resources <span id="newResourceMarker"></span></a>
        </li>
      </ul>
    </div>
  </nav>
  <div id="perPageOptions" class="col-auto navbar-dark bg-dark">
    <?php
    if (isset($_GET['page'])) {
      include ($page."partial/".$thisPage."/options.php");
    }
    ?>
  </div>
</div>
<?php
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
