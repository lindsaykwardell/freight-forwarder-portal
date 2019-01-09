<?php
if(login_check($db))
{
  $error_msg = "";

  if (isset($_POST['p'])) {
    // Sanitize and validate the data passed in
    $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
    if (strlen($password) != 128) {
      // The hashed pwd should be 128 characters long.
      // If it's not, something really odd has happened
      $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }

    // Username validity and password validity have been checked client side.
    // This should should be adequate as nobody gains any advantage from
    // breaking these rules.
    //

    if (empty($error_msg)) {
      echo "No errors.";
      // Create hashed password using the password_hash function.
      // This function salts it with a random salt and can be verified with
      // the password_verify function.
      $password = password_hash($password, PASSWORD_BCRYPT);

      // Insert the new user into the database
      $stmt = $db->conn->prepare("UPDATE users SET password = ? WHERE id = ?");
      $stmt->bind_param('si', $password, $_SESSION['user_id']);

      if (!$stmt->execute()) {
        //header('Location: ../error.php?err=Registration failure: INSERT');
        exit();
      }
      header('Location: home');
      exit();
    }
  }
}
