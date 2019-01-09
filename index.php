<?php
/**********************************
*         FF Online Portal        *
*        By Lindsay Wardell       *
***********************************/

// Include base PHP functions
include_once ("php.php");

// Initiate secure session
sec_session_start();

// Get account permission level
if(login_check($db))
{
  $accountType = $_SESSION['accountType']; //$db->getAccountType(htmlspecialchars($_SESSION['user_id']));
}

?>
<!DOCTYPE html>
<html>
<head>
<script>
// Routes to non-index elements.
var dir = "<?php echo $dir; ?>";
var page = "<?php echo $dir.$page; ?>";
var frame = "<?php echo $dir.$frame; ?>";
var dialog = "<?php echo $dir.$dialog; ?>";
var script = "<?php echo $dir.$script; ?>";
var style = "<?php echo $dir.$style; ?>";
var image = "<?php echo $dir.$image; ?>";
var db = "<?php echo $dir; ?>data/";
</script>
<?php
// Include head.
include_once ($frame . "head.php"); ?>
</head>
<body>
  <div id="mainContent" >
    <header class="row">
      <div class="col-auto">
        <a href="/">
          <img src="<?php echo $dir.$image; ?>placeholder.jpg" class="d-none d-sm-block" style="height: 100px;">
          <img src="<?php echo $dir.$image; ?>placeholder.jpg" class="d-block d-sm-none" style="height: 100px;">
        </a>
      </div>
      <?php if (login_check($db) && $accountType == 1) { ?>
        <div class="col-sm-12 col-md order-last pt-4">
          <div>
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label class="mr-sm-2" for="originSelect">Origin</label>
                  <select class="form-control custom-select" id="originSelect">
                    <option>Choose...</option>
                  </select>
                </div>
              </div>
              <div class="col">
                <div class="form-group">
                  <label class="mr-sm-2" for="destinationSelect">Destination</label>
                  <select class="form-control custom-select" id="destinationSelect">
                    <option>Choose...</option>
                  </select>
                </div>
              </div>
              <div class="col-auto d-none">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="displayAverageRate" checked>
                  <label class="form-check-label" for="displayAverageRate">Display Median Rate</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="saveSearch">
                  <label class="form-check-label" for="saveSearch">Save Search Items</label>
                </div>
              </div>
              <div class="col-sm-auto col-xs-12">
                <div class="text-center mt-3">
                  <button id="portToPortButton" type='button' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#regularModal' onclick="getPortToPort()">Search Rates</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php }
      if (login_check($db) && $accountType == 1) {
      ?>
      <div id="userOptionsMenu" class="col-md-auto col-sm order-md-last">
      <?php } else { ?>
      <div id="userOptionsMenu" class="col">
      <?php }
        if (login_check($db)) { ?>
          <div class="float-right pt-2 mr-2">
            <i id="notificationIcon" style="cursor: pointer;" data-toggle="modal" data-target="#notificationModal" class="far fa-flag" onclick="markNotificationViewed()"></i>
            <span id="notificationCounter" class="pl-2"></span>
          </div>
          <ul class="navbar-nav float-right mr-2">
            <li id="userOptions" class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#"><?php echo $_SESSION['username']; ?></a>
              <div class="dropdown-menu dropdown-menu-right">
                <!-- <a class="dropdown-item disabled" href="<?php echo $dir; ?>details">Edit Details</a> -->
                <a class="dropdown-item" href="<?php echo $dir; ?>password">Change Password</a>
                <div class="dropdown-divider"></div>
        <?php
        if ($accountType == 1) {
        ?>
                <a class="dropdown-item" href="<?php echo $dir; ?>admin">Administration</a>
                <div class="dropdown-divider"></div>
        <?php
        }
        ?>
                <a class="dropdown-item" href="<?php echo $dir; ?>logout">Log Out</a>
              </div>
            </li>
          </ul>
        <?php } ?>
      </div>
    </header>
<?php
// Load requested page.
if (isset($_GET['404']))
{
  echo "ERROR 404 - FILE NOT FOUND";
}
if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)) {
  echo "<p class='py-4 text-center'>You are using an incompatible browser. Please consider using Google Chrome.<br /><a href='https://www.google.com/chrome/'>Download Google Chrome.</a></p>";
}
elseif (isset($_GET['login'])) {
  include_once ($frame . "login.php");
}
else {
  if (login_check($db))
  {
    // Include navigation bar.
      include_once ($frame . "nav.php");
      
    // If registering a new user.
    if(isset($_GET['register']))
    {
      // If registration is successful.
      if($_GET['register'] == 'success')
      {
        include_once ($frame . "register_success.php");
      }
      // If registration has not begun.
      else {
        include_once ($frame . "register.php");
      }
    }
    // If not registering a new user.
    else {

      // If a particular page is queried.
      if (isset($_GET['page']))
      {
        include_once ($page . $_GET['page'] . ".php");
      }
      // Else, load home page.
      else
      {
        header('Location: home');
        exit();
        //include_once ($page . "home.php");
      }
    }
  }
  // If not logged in, ask for login.
  else {
    //include_once ($frame . "login.php");
    header('Location: login');
  }
  // Add modal windows (via Bootstrap)
  include_once ($frame . "modal.php");
}
// Add footer.
include_once ($frame . "footer.php");
?>
</div>
</body>
</html>
