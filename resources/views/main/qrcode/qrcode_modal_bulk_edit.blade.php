<div class="modal fade" id="qrModalBulkEdit" aria-labelledby="qrModalBulkEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalBulkEditLabel">Edit QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        @csrf
        <input type="hidden" name="id" id="idEdit">
        <div class="modal-body">
            <table class="table" id="bulkEditTable">

            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitEdit">Save changes</button>
        </div>

    </div>
  </div>
</div>
