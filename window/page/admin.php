<?php
if (login_check($db))
{
  if ($accountType == 1)
  {
?>
<main>
  <!-- <script>document.getElementById("adminLink").className += " active";</script> -->
<?php include ($frame . "adminNav.php"); ?>
  <div class="border border-top-0">
    <div class="card-body">
      <div id="adminDisplay">Select a task.</div>
    </div>
  </div>
</main>
<?php
  }
  else {
    header('Location: logout');
  }
}
?>
