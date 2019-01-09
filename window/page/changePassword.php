<?php
if (login_check($db))
{
  include_once ("data/users/updatePassword.php");
  $hasExternalJS = false;
?>
  <main>
  <script>document.getElementById("userOptions").className += " active";</script>
    <div class="border border-top-0">
      <div style="width: 600px; margin: 0 auto">
        <div class="card-body">
          <?php
          if (!empty($error_msg)) {
              echo $error_msg;
          }
          ?>
          <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" name="registration_form">
            <div class="form-group">
              <label for="password">New Password</label>
              <input type="password" class="form-control" name="password" id="password" placeholder="password">
              <small id="passwordHelp" class="form-text text-muted">Must container at least one uppercase letter, one lowercase letter, and at least one number.</small>
            </div>
            <div class="form-group">
              <label for="confirmpwd">Conform Password</label>
              <input type="password" class="form-control" name="confirmpwd" id="confirmpwd" placeholder="password (again)">
              <small id="confirmHelp" class="form-text text-muted">Your password must match.</small>
            </div>
              <button class="btn btn-primary" type="submit" value="Register"
                     onclick="return updatePasswordHash(this.form,
                                     this.form.password,
                                     this.form.confirmpwd);">Update Password</button>
               <small id="confirmHelp" class="form-text text-muted">You will be taken back to the login screen when you are done.</small>
          </form>
        </div>
      </div>
    </div>
  </main>
  <script type="text/JavaScript" src="window/script/sha512.js"></script>
  <script type="text/JavaScript" src="window/script/forms.js"></script>
<?php
}
?>
