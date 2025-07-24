<form method="POST" id="formCreate" action="{{ route('utang.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Utang Baru</h5>
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
                <div class="col-12 col-xxl-12" id="formSelectType">
                    <label class="form-label" for="type">Kategori</label>
                    <select class="form-control cust-control" id="type_category_create" name="type">
                    </select>
                    @if($errors->has('type'))
                        <span class="help-block">{{ $errors->first('type') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectSubType">
                    <label class="form-label" for="sub_type">Sub Kategori</label>
                    <select class="form-control cust-control" id="sub_type" name="sub_type" disabled>
                    </select>
                    @if($errors->has('sub_type'))
                    <span class="help-block">{{ $errors->first('sub_type') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="name">Nama <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name') }}" placeholder="Input Nama">
                    </input>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectDebtFrom">
                    <label class="form-label" for="debt_from">Utang Dari</label>
                    <select class="form-control cust-control" id="debt_from" name="debt_from" disabled>
                    </select>
                    @if($errors->has('debt_from'))
                    <span class="help-block">{{ $errors->first('debt_from') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectSaveTo">
                    <label class="form-label" for="save_to">Simpan Ke</label>
                    <select class="form-control cust-control" id="save_to" name="save_to">
                    </select>
                    @if($errors->has('save_to'))
                    <span class="help-block">{{ $errors->first('save_to') }}</span>
                    @endif
                </div>
            </div>
            
            

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="amount">Nominal <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('amount') is-invalid @enderror"
                        id="amount" name="amount" value="{{ old('amount') }}" placeholder="15.000.000">
                    </input>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="note">Keterangan </label>
                    <textarea name="note" id="note" cols="30" rows="5" class="form-control">{{ old('note') }}</textarea>
                    @error('note')
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
    $('#type_category_create').select2({
        dropdownParent: $('#formSelectType'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Kategori',
        ajax: {
            url: "{{ route('utang.type') }}",
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
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.name
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

    $('#sub_type').select2({
        dropdownParent: $('#formSelectSubType'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Sub Kategori',
        ajax: {
            url: "{{ route('utang.subType') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    type: $('#type_category_create').val(),
                    keyword: params.term,
                    limit: 25
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $('#debt_from').select2({
        dropdownParent: $('#formSelectDebtFrom'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Utang Dari',
        ajax: {
            url: "{{ route('utang.debtFrom') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    type: $('#type_category_create').val(),
                    keyword: params.term,
                    limit: 25
                };
            },
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $('#save_to').select2({
        dropdownParent: $('#formSelectSaveTo'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Simpan Ke',
        ajax: {
            url: "{{ route('utang.saveTo') }}",
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
                    results: $.map(data, function(item) {
                        return {
                            id: item.id+'_'+item.account_code_id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $(document).on('input', '#amount', function() {
        value = formatCurrency($(this).val());
        $(this).val(value);
    });

    function formatCurrency(angka, prefix) {
        if (!angka) {
            return (prefix || '') + '-';
        }

        angka = angka.toString();
        const splitDecimal = angka.split('.');
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            const separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix === undefined ? rupiah : rupiah ? (prefix || '') + rupiah : '';
    }
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