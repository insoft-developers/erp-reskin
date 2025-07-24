<form method="POST" id="formCreate" action="{{ route('transfer-stock.material.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Transfer Stok Bahan Baku</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="date">Tanggal <span
                            class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                        id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}">
                    </input>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formMaterialFrom">
                    <label class="form-label" for="material_from_id">Material Yang Ditransfer</label>
                    <select class="form-control cust-control" id="material_from_id" name="material_from_id">
                    </select>
                    @if($errors->has('material_from_id'))
                        <span class="help-block">{{ $errors->first('material_from_id') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="stock_from">Jumlah Stok Yang Di Kurangi <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('stock_from') is-invalid @enderror"
                        id="stock_from" name="stock_from" value="{{ old('stock_from') }}" placeholder="10">
                    </input>
                    @error('stock_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formMaterialTo">
                    <label class="form-label" for="material_to_id">Material Yang Dituju</label>
                    <select class="form-control cust-control" id="material_to_id" name="material_to_id">
                    </select>
                    @if($errors->has('material_to_id'))
                        <span class="help-block">{{ $errors->first('material_to_id') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="stock_to">Jumlah Stock Ditambah <span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('stock_to') is-invalid @enderror"
                        id="stock_to" name="stock_to" value="{{ old('stock_to') }}" placeholder="10">
                    </input>
                    @error('stock_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>

<script>
    $('#material_from_id').select2({
        dropdownParent: $('#formMaterialFrom'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Produk',
        ajax: {
            url: '/material_table',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    keyword: params.term,
                    limit: 25
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(item) {
                        return {
                            id: item.id,
                            text: item.material_name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $('#material_to_id').select2({
        dropdownParent: $('#formMaterialTo'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Produk',
        ajax: {
            url: '/material_table',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    keyword: params.term,
                    limit: 25
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(item) {
                        return {
                            id: item.id,
                            text: item.material_name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $(document).on('change', '#type_category_create', function() {
        $('#debt_from').attr('disabled', false);
        $('#debt_from').val(null).trigger('change');

        $('#sub_type').attr('disabled', false);
        $('#sub_type').val(null).trigger('change');
    })
</script>
<script>
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
            console.log(data);
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