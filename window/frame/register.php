<?php
if (login_check($db) && $accountType == 1)
{
include_once 'window/script/register.inc.php';
?>
    <script type="text/JavaScript" src="window/script/sha512.js"></script>
    <script type="text/JavaScript" src="window/script/forms.js"></script>
<div class="card" style="max-width: 600px; margin: 0 auto;">
  <div class="card-header">
    <h4 class="card-title">Register with us</h4>
  </div>
  <div class="card-body">
        <?php
        if (!empty($error_msg)) {
            echo $error_msg;
        }
        ?>

    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" name="registration_form">
      <div class="form-group">
        <label for="username">Username</label>
        <input type='text' class="form-control" name='username' id='username' placeholder="John Smith">
        <small id="usernameHelp" class="form-text text-muted">Usernames may contain only digits, upper and lowercase letters, spaces, and underscores.</small>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="your@address.here">
        <small id="emailHelp" class="form-text text-muted">Must have a valid email.</small>
      </div>
      <div class="form-group">
        <label for="accountType">Account Type</label>
        <select class='custom-select' name='accountType' id="accountType">
          <option value="1">Forwarder</option>
          <option value="2">Shipper</option>
        </select>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="password">
        <small id="passwordHelp" class="form-text text-muted">Must container at least one uppercase letter, one lowercase letter, and at least one number.</small>
      </div>
      <div class="form-group">
        <label for="confirmpwd">Confirm Password</label>
        <input type="password" class="form-control" name="confirmpwd" id="confirmpwd" placeholder="password (again)">
        <small id="confirmHelp" class="form-text text-muted">Your password must match.</small>
      </div>
        <button class="btn btn-primary" type="submit" value="Register"
               onclick="return regformhash(this.form,
                               this.form.username,
                               this.form.email,
                               this.form.password,
                               this.form.confirmpwd);">Register</button>
    </form>
    <p>Return to the <a href="home">main page</a>.</p>
  </div>
</div>
<?php
}
else {
  header("HTTP/1.0 404 Not Found");
}
?>
