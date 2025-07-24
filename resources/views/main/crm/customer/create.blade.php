<form method="POST" id="formCreate" action="{{ route('crm.customer.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Customer Baru</h5>
        </div>
        <div class="modal-body" id="addNewCustomer">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name') }}" placeholder="Input Name">
                    </input>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="email">Email <span class="text-danger"></span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" placeholder="Input Email">
                    </input>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="phone">Phone <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                        value="{{ old('phone') }}" placeholder="Input Phone">
                    </input>
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-span-4 mb-3">
                <label for="customer-province" class="col-form-label">Provinsi:</label>
                <select id="customer-province" name="province_id" class="form-control">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-span-4 mb-3">
                <label for="customer-city" class="col-form-label">Kota:</label>
                <select id="customer-city" name="city_id" class="form-control">
                    <option value=""></option>
                </select>
            </div>
            <div class="col-span-4 mb-3">
                <label for="customer-district" class="col-form-label">Kecamatan:</label>
                <select id="customer-district" name="district_id" class="form-control">
                    <option value=""></option>
                </select>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="kelurahan">Kelurahan</label>
                    <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" id="kelurahan"
                        name="kelurahan" value="{{ old('kelurahan') }}" placeholder="Input Kelurahan">
                    </input>
                    @error('kelurahan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" id="alamat" class="form-control" cols="30"
                    rows="10">{{ old('alamat') }}</textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var prov_id = 0;
    var city_id = 0;
    var dist_id = 0;

    $('#customer-province').select2({
        dropdownParent: $("#addNewCustomer"),
        ajax: {
            url: '/v1/administrative/provinces',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term // search term
                };
            },
            processResults: function(data) {
                console.log(data);
                apiResults = data.data.data.map(function(item) {
                    return {
                        text: item.province_name,
                        id: item.province_id
                    };
                });

                return {
                    results: apiResults
                };
            },
            cache: false
        },
    })
    $('#customer-province').on('change', function(e) {
        var selectedValue = $(this).val();
        // var selectedText = $(this).find("option:selected").text();

        prov_id = selectedValue
        onSelectCity()
    });

    function onSelectCity() {
        console.log(prov_id);
        $('#customer-city').select2({
            dropdownParent: $("#addNewCustomer"),
            ajax: {
                url: '/v1/administrative/cities?province_id=' + prov_id,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.data.map(function(item) {
                        return {
                            text: item.city_name,
                            id: item.city_id
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: false
            },
        })
        $('#customer-city').on('change', function(e) {
            var selectedValue = $(this).val();

            city_id = selectedValue
            onselectdistrict()
        });
    }
    
    function onselectdistrict() {
        $('#customer-district').select2({
            dropdownParent: $("#addNewCustomer"),
            ajax: {
                url: '/v1/administrative/districts?city_id=' + city_id,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.data.map(function(item) {
                        return {
                            text: item.subdistrict_name,
                            id: item.subdistrict_id
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: false
            },
        })
        $('#customer-district').on('change', function(e) {
            var selectedValue = $(this).val();
            dist_id = selectedValue
        });
    }
    
    document.getElementById('formCreate').addEventListener('submit', function(event) {
        event.preventDefault();
    
        const form = event.target;
        const formData = new FormData(form);
        const url = form.action;
    
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                let errorMessages = '';
                for (const [field, messages] of Object.entries(data.errors)) {
                    errorMessages += messages.join('<br>') + '<br>';
                }
    
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessages
                });
            }else if(!data.status){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                })
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Something went wrong!'
            });
        });
    });
</script>