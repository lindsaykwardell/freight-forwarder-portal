<?php
// Connect to DB
include_once ("data/db.php");

function get_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
 
    return $ipaddress;
}

function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = FALSE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
    // Sets the session name to the one set above.
    session_name($session_name);
    session_start();            // Start the PHP session
    session_regenerate_id();    // regenerated the session, delete the old one.
}

function login($email, $password, $db) {
  if ($stmt = $db->conn->prepare("SELECT id, username, password FROM users WHERE email = ? LIMIT 1"))
  {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    $stmt->bind_result($user_id, $username, $db_password);
    $stmt->fetch();

    if ($stmt->num_rows == 1) {
      if (checkbrute($user_id, $db) == true) {
        return false;
      } else {
        if(password_verify($password, $db_password)) {
          // Password is correct!
          // Get the user-agent string of the user.
          $user_browser = $_SERVER['HTTP_USER_AGENT'];
          // XSS protection as we might print this value
          $user_id = preg_replace("/[^0-9]+/", "", $user_id);
          $_SESSION['user_id'] = $user_id;
          // XSS protection as we might print this value
          //$username = preg_replace("/[^a-zA-Z0-9_\-]+/",
          $username = preg_replace("/[^a-zA-Z0-9_ \-]+/",
                                                      "",
                                                      $username);
          $_SESSION['username'] = $username;
          $_SESSION['login_string'] = hash('sha512',
                    $db_password . $user_browser);
          $_SESSION['accountType'] = $db->getAccountType(htmlspecialchars($_SESSION['user_id']));
          // Login successful.
          return true;
        } else {
          // Password is not correct
          // We record this attempt in the database
          $now = time();
          $stmt = $db->conn->prepare("INSERT INTO login_attempts(userId, time, ip) VALUES (?, ?, ?)");
          $stmt->bind_param('iss', $user_id, $now, get_ip());
          $stmt->execute();

          return false;
        }
      }
    } else {
      // No user exists.
      return false;
    }
  }
}

function checkbrute($user_id, $db) {
  // Get timestamp of current time
  $now = time();

  // All login attempts are counted from the past 2 hours.
  $valid_attempts = $now - (2 * 60 * 60);

  if ($stmt = $db->conn->prepare("SELECT time FROM login_attempts WHERE userId = ? AND time > ?")) {
    $stmt->bind_param('is', $user_id, $valid_attempts);
    $stmt->execute();
    $stmt->store_result();

// If there have been more than 5 failed logins
    if ($stmt->num_rows > 5) {
      return true;
    } else {
      return false;
    }
  }
}

function login_check($db) {
  // Check if all session variables are set
  if (isset($_SESSION['user_id'],
                    $_SESSION['username'],
                    $_SESSION['login_string'])) {

    $user_id = $_SESSION['user_id'];
    $login_string = $_SESSION['login_string'];
    $username = $_SESSION['username'];

    // Get the user-agent string of the user.
    $user_browser = $_SERVER['HTTP_USER_AGENT'];

    if ($stmt = $db->conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1")) {
      $stmt->bind_param('i', $user_id);
      $stmt->execute();
      $stmt->store_result();

      if ($stmt->num_rows == 1) {
        // If the user exists get variables from result.
        $stmt->bind_result($password);
        $stmt->fetch();
        $login_check = hash('sha512', $password . $user_browser);

        if (hash_equals($login_check, $login_string) ){
            // Logged In!!!!
            return true;
        } else {
            // Not logged in
            return false;
        }
      } else {
          // Not logged in
          return false;
      }
    } else {
        // Not logged in
        return false;
    }
  } else {
      // Not logged in
      return false;
  }
}

function esc_url($url) {

    if ('' == $url) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }

    $url = str_replace(';//', '://', $url);

    $url = htmlentities($url);

    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}
// Set default time zone.
date_default_timezone_set('America/Los_Angeles');

// Get relative URI, then determine how many levels out are required for JS/CSS includes.
$url = $_SERVER['REQUEST_URI'];
$counter = explode('/', $url);
$thisPage = end($counter);
if ($thisPage == "")
{
  header('Location: ./bookingRequests');
  exit();
}
$counter = count($counter) - 3;
$dir = "";
for ($i=0; $i < $counter; $i++) {
  $dir .= "../";
}

// Does the selected page have an external JS file? Default to true.
$hasExternalJS = true;

// Routes to window elements
$page = "window/page/";
$frame = "window/frame/";
$dialog = "window/dialog/";
$script = "window/script/";
$style = "window/style/";
$image = "window/image/";

// Switches to active Modals
$modalSm = false;
$modalReg = false;
$modalLg = false;
?>
