<?php
if (login_check($db))
{
?>
  <main>
  <script>document.getElementById("resourcesLink").className += " active";</script>
    <div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-3 col-md-12">
            <div class="card mb-2">
              <div class="card-header bg-navy text-white">
                <h5 class="card-title">Contents</h5>
              </div>
              <div id="stickyList">
              </div>
            </div>
          </div>
          <div class="col-lg-9 col-md-12">
            <div id="resourceItems">
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
<?php
}
?>
