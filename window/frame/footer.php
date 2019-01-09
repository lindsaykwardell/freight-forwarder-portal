<div class="border-top-0 my-3 mx-5 text-muted" style="font-variant: small-caps;">
  <span class="float-left">Copyright &copy; <?php echo date('Y'); ?> Freight Forwarder, Inc.</span>
  <span class="float-right">
    <?php
    if (login_check($db)) {
      ?>
      [<a onclick="help()" data-toggle='modal' data-target='#largeModal' href="#">Help</a> |
      <a onclick="about()" data-toggle='modal' data-target='#regularModal' href="#">About</a> |
      <a onclick="terms()" data-toggle='modal' data-target='#largeModal' href="#">Legal</a> |
      <a onclick="changelog()" data-toggle='modal' data-target='#regularModal' href="#">Changelog</a>]
      <?php
    } else {
      ?>
      [<a onclick="about()" data-toggle='modal' data-target='#regularModal' href="#">About</a> |
      <a onclick="terms()" data-toggle='modal' data-target='#largeModal' href="#">Legal</a>]
      <?php
    }
     ?>
  </span>
</div>
<script src="<?php echo $dir.$script; ?>jquery.min.js"></script>
<script src="<?php echo $dir.$script; ?>popper.min.js"></script>
<script src="<?php echo $dir.$script; ?>bootstrap.min.js"></script>
<script src="<?php echo $dir.$script; ?>mdb.min.js"></script>
<script src="<?php echo $dir.$script; ?>moment.min.js"></script>
<script src="<?php echo $dir.$script; ?>daterangepicker.js"></script>
<script src="<?php echo $dir.$script; ?>toggles.min.js"></script>
<script src="<?php echo $dir.$script; ?>ckeditor/ckeditor.js"></script>
<script src="<?php echo $dir.$script; ?>ckeditor/adapters/jquery.js"></script>
<script>document.write('<script src="<?php echo $dir.$script; ?>lfi-core.js?dev=' + Math.floor(Math.random() * 100) + '"\><\/script>');</script>
<?php
if ($hasExternalJS == true) {
  if (isset($_GET['page']))
  {
    echo "<script>document.write('<script src=\"" . $dir.$script . "lfi-" . $_GET['page'] . ".js?dev=' + Math.floor(Math.random() * 100) + '\"\><\/script>');</script>";
  }
  else {
    // echo "<script>document.write('<script src=\"" . $dir.$script . "lfi-home.js?dev=' + Math.floor(Math.random() * 100) + '\"\><\/script>');</script>";
  }
}
?>
