<?php
if (login_check($db))
{
  include_once ("data/users/updatePassword.php");
?>
  <main>
  <?php include ($frame . "nav.php"); ?>
    <div class="border border-top-0">
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
            <button class="btn btn-primary" type="submit"
                   onclick="return updatePasswordHash(this.form,
                                   this.form.password,
                                   this.form.confirmpwd);">Save Details</button>
        </form>
      </div>
    </div>
  </main>
  <script type="text/JavaScript" src="window/script/sha512.js"></script>
  <script type="text/JavaScript" src="window/script/forms.js"></script>
<?php
}
?>
