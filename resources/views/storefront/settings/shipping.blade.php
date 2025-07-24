<div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingFour">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour" aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
            Pengaturan Pengiriman
        </button>
    </h2>
    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingFour">
        <div class="accordion-body">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Aktifkan Pengiriman?
                </label>
                <div class="col-sm-10">
                <div class="deliveryStatus form-check form-switch" data-method="Delivery" checked>
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="delivery" {{@$setting->delivery == 1 ? 'checked' : ''}}>
                    </div>
                </div>
                <div id="shippingOption">
                  <label class="col-sm-2 col-form-label">
                      Pengiriman
                  </label>
                  <div class="col-sm-10">
                      <div class="row" id="shippings">
                          <div class="col-sm-6">
                              <div class="form-check shipping" data-method="POS">
                                <input class="form-check-input" type="checkbox" id="POS">
                                <label class="form-check-label" for="POS">POS Indonesia (POS)</label>
                              </div>
                              <div class="form-check shipping" data-method="LION">
                                <input class="form-check-input" type="checkbox" id="LION">
                                <label class="form-check-label" for="LION">Lion Parcel (LION)</label>
                              </div>
                              <div class="form-check shipping" data-method="NINJA">
                                <input class="form-check-input" type="checkbox" id="NINJA">
                                <label class="form-check-label" for="NINJA">Ninja Xpress (NINJA)</label>
                              </div>
                              <div class="form-check shipping" data-method="IDE">
                                <input class="form-check-input" type="checkbox" id="IDE">
                                <label class="form-check-label" for="IDE">ID Express (IDE)</label>
                              </div>
                              <div class="form-check shipping" data-method="SICEPAT">
                                <input class="form-check-input" type="checkbox" id="SICEPAT">
                                <label class="form-check-label" for="SICEPAT">SiCepat Express (SICEPAT)</label>
                              </div>
                              <div class="form-check shipping" data-method="SAP">
                                <input class="form-check-input" type="checkbox" id="SAP">
                                <label class="form-check-label" for="SAP">SAP Express (SAP)</label>
                              </div>
                              <div class="form-check shipping" data-method="ANTERAJA">
                                <input class="form-check-input" type="checkbox" id="ANTERAJA">
                                <label class="form-check-label" for="ANTERAJA">AnterAja (ANTERAJA)</label>
                              </div>
                              <div class="form-check shipping" data-method="JNE">
                                <input class="form-check-input" type="checkbox" id="JNE">
                                <label class="form-check-label" for="JNE">Jalur Nugraha Ekakurir (JNE)</label>
                              </div>
                              <div class="form-check shipping" data-method="TIKI">
                                <input class="form-check-input" type="checkbox" id="TIKI">
                                <label class="form-check-label" for="TIKI">Citra Van Titipan Kilat (TIKI)</label>
                              </div>
                              <div class="form-check shipping" data-method="WAHANA">
                                <input class="form-check-input" type="checkbox" id="WAHANA">
                                <label class="form-check-label" for="WAHANA">Wahana Prestasi Logistik (WAHANA)</label>
                              </div>
                              <div class="form-check shipping" data-method="JNT">
                                <input class="form-check-input" type="checkbox" id="JNT">
                                <label class="form-check-label" for="JNT">J&T Express (J&T)</label>
                              </div>
                              <div class="form-check shipping" data-method="PAHALA">
                                <input class="form-check-input" type="checkbox" id="PAHALA">
                                <label class="form-check-label" for="PAHALA">Pahala Kencana Express (PAHALA)</label>
                              </div>
                              <div class="form-check shipping" data-method="NCS">
                                <input class="form-check-input" type="checkbox" id="NCS">
                                <label class="form-check-label" for="NCS">Nusantara Card Semesta (NCS)</label>
                              </div>

                          </div>
                          <div class="col-sm-6">
                              <div class="form-check shipping" data-method="REX">
                                <input class="form-check-input" type="checkbox" id="REX">
                                <label class="form-check-label" for="REX">Royal Express Indonesia (REX)</label>
                              </div>
                              <div class="form-check shipping" data-method="JTL">
                                <input class="form-check-input" type="checkbox" id="JTL">
                                <label class="form-check-label" for="JTL">JTL Express (JTL)</label>
                              </div>
                              <div class="form-check shipping" data-method="SENTRAL">
                                <input class="form-check-input" type="checkbox" id="SENTRAL">
                                <label class="form-check-label" for="SENTRAL">Sentral Cargo (SENTRAL)</label>
                              </div>
                              <div class="form-check shipping" data-method="RPX">
                                <input class="form-check-input" type="checkbox" id="RPX">
                                <label class="form-check-label" for="RPX">RPX Holding (RPX)</label>
                              </div>
                              <div class="form-check shipping" data-method="PANDU">
                                <input class="form-check-input" type="checkbox" id="PANDU">
                                <label class="form-check-label" for="PANDU">Pandu Logistics (PANDU)</label>
                              </div>
                              <div class="form-check shipping" data-method="SLIS">
                                <input class="form-check-input" type="checkbox" id="SLIS">
                                <label class="form-check-label" for="SLIS">Solusi Ekspres (SLIS)</label>
                              </div>
                              <div class="form-check shipping" data-method="EXPEDITO">
                                <input class="form-check-input" type="checkbox" id="EXPEDITO">
                                <label class="form-check-label" for="EXPEDITO">Expedito* (EXPEDITO)</label>
                              </div>
                              <div class="form-check shipping" data-method="RAY">
                                <input class="form-check-input" type="checkbox" id="RAY">
                                <label class="form-check-label" for="RAY">Rayspeed* (RAY)</label>
                              </div>
                              <div class="form-check shipping" data-method="DSE">
                                <input class="form-check-input" type="checkbox" id="DSE">
                                <label class="form-check-label" for="DSE">21 Express (DSE)</label>
                              </div>
                              <div class="form-check shipping" data-method="FIRST">
                                <input class="form-check-input" type="checkbox" id="FIRST">
                                <label class="form-check-label" for="FIRST">First Logistics (FIRST)</label>
                              </div>
                              <div class="form-check shipping" data-method="STAR">
                                <input class="form-check-input" type="checkbox" id="STAR">
                                <label class="form-check-label" for="STAR">Star Cargo (STAR)</label>
                              </div>
                              <div class="form-check shipping" data-method="IDL">
                                <input class="form-check-input" type="checkbox" id="IDL">
                                <label class="form-check-label" for="IDL">IDL Cargo (IDL)</label>
                              </div>
                          </div>
                          <div id="jsonOutputShipping" hidden=""></div>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
