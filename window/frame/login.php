<script type="text/JavaScript" src="window/script/sha512.js"></script>
<script type="text/JavaScript" src="window/script/forms.js"></script>
<div class="card" style="max-width: 400px; margin: 0 auto;">
  <div class="card-header">
    <h4 class="card-title">Online Portal Login</h4>
  </div>
  <div class="card-body">
    <form action="<?php echo $script; ?>process-login.php" method="post" name="login_form">
      <div class="form-group">
        <label for="emailInput">Email Address</label>
        <input type="email" class="form-control" id="emailInput" placeholder="you@example.com" name="email" >
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="password" >
      </div>
      <button id="loginButton" class="btn btn-primary" type="submit" value="Login" onclick="formhash(this.form, this.form.password);">Log In</button>
    </form>
    <?php
    if (isset($_GET['error'])) {
        echo '<p class="error">Error Logging In!</p>';
    }
    ?>
    <!-- <p>If you don't have a login, please <a href='?register=y'>register</a></p> -->
    <p><a href="/">Back to Home Page</a></p>
  </div>
</div>
<hr>
<div class="container">
  <p><strong>What is the Online Portal?</strong></p>
  <p>The Online Portal is a modern tool for managing booking requests. Submit, track, and manage your requests, all from one screen. You can also print and export your requests with ease, so your data goes where it needs to.</p>
  <p><strong>How do I sign up?</strong></p>
  <p>The Online Portal is for current customers only. If you are already a customer, please contact <a href="mailto:sales@freightforwarder.com">John Smith</a> to create your account.</p>
  <p>Not a customer? <a href="#">Click here</a> to request a quote!</p>
</div>
