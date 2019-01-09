<?php if (login_check($db) && $accountType == 1)
{
?>
<ul class="nav nav-pills card-header-pills">
  <li class="nav-item dropdown">
    <button id="contractOptions" class="btn btn-grey nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" href="#">Options</button>
    <div class="dropdown-menu dropdown-menu-right">
      <a class="dropdown-item" onclick='printMatrix("full")' href="#">Print Rate Matrix</a>
      <a class="dropdown-item" onclick='printMatrix("rates")' href="#">Print Rates Only</a>
      <a class="dropdown-item" onclick='printMatrix("contracts")' href="#">Print Contracts Only</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" data-toggle='modal' data-target='#smallModal' onclick="newContractModal()" href="#">New Contract</a>
      <a class="dropdown-item" data-toggle='modal' data-target='#smallModal' onclick="newOriginModal()" href="#">New Origin</a>
      <a class="dropdown-item" data-toggle='modal' data-target='#smallModal' onclick="newDestinationModal()" href="#">New Destination</a>
      <div class="dropdown-divider"></div>
      <a class="dropdown-item" onclick="editContractModal()" data-toggle='modal' data-target='#smallModal' href="#">Edit Contract Details</a>
      <a class="dropdown-item" onclick="deleteContractModal()" data-toggle='modal' data-target='#smallModal' href="#">Delete Contract</a>
    </div>
  </li>
</ul>
<?php
}
?>
