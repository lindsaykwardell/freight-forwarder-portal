<?php if (login_check($db) && $accountType == 1)
{
?>
<ul class="nav nav-pills card-header-pills">
  <li class="nav-item dropdown">
    <button id="contractOptions" class="btn btn-grey nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#">Options</button>
    <div class="dropdown-menu dropdown-menu-right">
      <a class="dropdown-item" onclick="renderNewResourceForm()">Add Item</a>
    </div>
  </li>
</ul>
<?php
}
?>
