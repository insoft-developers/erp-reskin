<div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingThree">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
            Pengaturan Pembayaran
        </button>
    </h2>
    <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
        <div class="accordion-body">
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Pembayaran
                </label>
                <div class="col-sm-10" id="paymentMethods">
                    <div class="paymentMethod form-check form-switch" data-method="Cash" checked>
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="Cash"> Cash
                    </div>
                    <div class="paymentMethod form-check form-switch" data-method="Online-Payment">
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="Online-Payment"> Online Payment
                    </div>
                    <div class="paymentMethod form-check form-switch" data-method="Transfer">
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="Transfer"> Transfer
                        <div class="banks d-none" id="banks">
                            <hr />
                          <div class="bank" data-bank="Bank BCA">
                              <div class="bankDetails row mt-3">
                                <div class="col-sm-3">
                                    <input type="checkbox" class="bankCheckbox" id="BankBCA"> Bank BCA
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="bankOwner form-control" placeholder="Nama Pemilik Rekening" id="BankBCAOwner" />
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="bankAccountNumber form-control" placeholder="Nomor Rekening" id="BankBCAAccount" />
                                </div>
                            </div>
                          </div>
                          <div class="bank" data-bank="Bank Mandiri">
                              <div class="bankDetails row mt-3">
                                <div class="col-sm-3">
                                    <input type="checkbox" class="bankCheckbox" id="BankMandiri"> Bank Mandiri
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="bankOwner form-control" placeholder="Nama Pemilik Rekening" id="BankMandiriOwner" />
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="bankAccountNumber form-control" placeholder="Nomor Rekening" id="BankMandiriAccount" />
                                </div>
                            </div>
                          </div>
                          <div class="bank" data-bank="Bank BNI">
                              <div class="bankDetails row mt-3">
                                <div class="col-sm-3">
                                    <input type="checkbox" class="bankCheckbox" id="BankBNI"> Bank BNI
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="bankOwner form-control" placeholder="Nama Pemilik Rekening" id="BankBNIOwner" />
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="bankAccountNumber form-control" placeholder="Nomor Rekening" id="BankBNIAccount" />
                                </div>
                            </div>
                          </div>
                          <div class="bank" data-bank="Bank BRI">
                              <div class="bankDetails row mt-3">
                                <div class="col-sm-3">
                                    <input type="checkbox" class="bankCheckbox" id="BankBRI"> Bank BRI
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="bankOwner form-control" placeholder="Nama Pemilik Rekening" id="BankBRIOwner" />
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="bankAccountNumber form-control" placeholder="Nomor Rekening" id="BankBRIAccount" />
                                </div>
                            </div>
                          </div>
                          <!-- Add more banks as needed -->
                           <hr />
                        </div>
                    </div>
                    <div class="paymentMethod form-check form-switch" data-method="COD">
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="COD"> Cash on Delivery (COD)
                    </div>
                    <div class="paymentMethod form-check form-switch" data-method="Marketplace">
                        <input type="checkbox" class="paymentCheckbox form-check-input" type="checkbox" id="Marketplace"> Marketplace
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
