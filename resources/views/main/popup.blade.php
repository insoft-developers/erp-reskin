<div class="modal-content">
    <div class="modal-body table-responsive" style="padding: 15px;">
        <button type="button" data-bs-dismiss="modal" onclick="setNotifPopup()" aria-label="Close" style="position: absolute;right: 3px;color: white;border-radius: 50%;background-color: red;border-color: unset;top: 3px;"><i class="fas fa-close" style="font-size: 30px; padding: 5px;"></i></button>

        <a href="{{ notifPopup()->link }}" target="_blank">
            <img src="{{ asset('storage/'.notifPopup()->image) }}" alt="" width="100%">
        </a>
        
        <div class="d-flex justify-content-center mt-1 mb-1">
            <input class="form-check-input me-2" type="checkbox" id="showPopup" style="padding: 10px;">
            <label class="form-check-label" style="margin: 3px 0px;" for="showPopup">Jangan Tampilkan Lagi</label>
        </div>
    </div>
</div>

<script>
    function setNotifPopup() {
        if ($('#showPopup').is(':checked')) {
            $.ajax({
                url: "{{ route('notification.hidePopup') }}", 
                type: 'GET', 
                dataType  : 'json',
            })
            .done(function(data) {
                console.log(data);
            })
            .fail(function() {
                alert('Load data failed.');
            });
        }
    }
</script>