<?php if (login_check($db) && $accountType == 1)
{
?>
<ul class="nav nav-pills card-header-pills">
  <li class="nav-item dropdown">
    <button id="contractOptions" class="btn btn-grey nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#">Views</button>
    <div class="dropdown-menu dropdown-menu-right">
      <a class="dropdown-item" href="#" onclick='loadPendingBookings("all")'>View All Requests</a>
      <a class="dropdown-item" href="#" onclick="loadPendingBookings('assigned', '<?php echo $_SESSION['username']; ?>')">View My Requests</a>
      <a class="dropdown-item" href="#" onclick='loadPendingBookings("shipper")'>Shipper View</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" href="#" onclick='loadPendingBookings("deleted")'>View Deleted Requests</a>
    </div>
  </li>
</ul>
<?php
}
?>
