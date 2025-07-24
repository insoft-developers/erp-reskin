<div class="modal fade" id="qrModalEdit" aria-labelledby="qrModalEditLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalEditLabel">Edit QR Code</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

        @csrf
        <input type="hidden" name="id" id="idEdit">
        <div class="modal-body">
            <table class="table">
                <tr>
                    <td>
                        Nama Meja:
                    </td>
                    <td>
                        <input type="text" class="form-control" id="noMejaEdit" required/>
                    </td>
                </tr>
                <tr>
                    <td>
                        Availability:
                    </td>
                    <td>
                        <select class="form-control" name="availability" id="availabilityEdit">
                            <option value="Reserved">Reserved</option>
                            <option value="Available">Available</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitEdit">Save changes</button>
        </div>

    </div>
  </div>
</div>
