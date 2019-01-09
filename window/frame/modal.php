<div class="modal fade" id="smallModal" tabindex="-1" role="dialog" aria-labelledby="smallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="alert" role="alert" id="smallModalAlert" style="display: none;">
      </div>
      <div class="modal-header bg-navy text-white">
        <h5 class="modal-title" id="smallModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="smallModalContent">
        <p>...</p>
      </div>
      <div class="modal-footer" id="smallModalFooter">
        ...
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="regularModal" tabindex="-1" role="dialog" aria-labelledby="regularModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="alert" role="alert" id="regularModalAlert" style="display: none;">
      </div>
      <div class="modal-header bg-navy text-white">
        <h5 class="modal-title" id="regularModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="regularModalContent">
        <p>...</p>
      </div>
      <div class="modal-footer" id="regularModalFooter">
        ...
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog" aria-labelledby="largeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="alert" role="alert" id="largeModalAlert" style="display: none;">
      </div>
      <div class="modal-header bg-navy text-white">
        <h5 class="modal-title" id="largeModalLabel">...</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="largeModalContent">
        <p>...</p>
      </div>
      <div class="modal-footer" id="largeModalFooter">
        ...
      </div>
    </div>
  </div>
</div>
<div class="modal fade right" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-full-height modal-right" role="document">
    <div class="modal-content bg-dark text-white card">
      <div class="alert" role="alert" id="notificaitonModalAlert" style="display: none;">
      </div>
      <div class="card-header">
        <h5 class="card-title" id="notificationModalLabel">Notifications</h5>
        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="">
          <span aria-hidden="true">&times;</span>
        </button> -->
      </div>
      <div class="card-body" id="notificationModalContent" style="overflow: auto;">
      </div>
      <div class="card-footer" id="notificationModalFooter">
        <button type='button' class='btn btn-grey' data-dismiss='modal'>Close</button>
        <button type='button' class='btn btn-grey' onclick="clearNotificationPane()" data-dismiss='modal'>Clear</button>
      </div>
    </div>
  </div>
</div>
<div id="toastModal">
</div>
