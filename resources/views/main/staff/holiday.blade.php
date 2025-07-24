<div class="accordion-item mb-3">
    <h2 class="accordion-header" id="panelsStayOpen-headingFour">
        <button class="accordion-button collapsed btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
            Pengaturan hari Libur
        </button>
    </h2>
    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingFour">
        <div class="accordion-body">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Aktifkan Hari Libur
                </label>
                <div class="col-sm-10">
                <div id="shippingOption">
                  <label class="col-sm-2 col-form-label">
                      Hari Libur
                  </label>
                  <div class="col-sm-10">
                      <div class="row">
                          <div class="col-sm-6">
                              <div class="form-check shipping" data-method="Senin">
                                <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Senin" value="Senin" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Senin', json_decode($data->holiday)) ? 'checked' : '') }}>
                                <label class="form-check-label" for="Senin">Senin</label>
                              </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Selasa">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Selasa" value="Selasa" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Selasa', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Selasa">Selasa</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Rabu">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Rabu" value="Rabu" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Rabu', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Rabu">Rabu</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Kamis">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Kamis" value="Kamis" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Kamis', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Kamis">Kamis</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Jumat">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Jumat" value="Jumat" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Jumat', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Jumat">Jumat</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Sabtu">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Sabtu" value="Sabtu" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Sabtu', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Sabtu">Sabtu</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check shipping" data-method="Minggu">
                              <input class="form-check-input" name="holiday[]" onclick="validateHoliday()" type="checkbox" id="Minggu" value="Minggu" {{ (isset($data->holiday) && is_array(json_decode($data->holiday, true)) && in_array('Minggu', json_decode($data->holiday)) ? 'checked' : '') }}>
                              <label class="form-check-label" for="Minggu">Minggu</label>
                            </div>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  function validateHoliday() {
      // const checkboxes = document.querySelectorAll('input[name="holiday[]"]:checked');
      // const checkedCount = checkboxes.length;

      // if (checkedCount == 0) {
      //     $('#submitBtn').prop('disabled',true);
      // } else {
      //     $('#submitBtn').prop('disabled',false);
      // }
  }
</script>