<form method="POST" id="formCreate" action="{{ route('expense.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Pengeluaran Baru</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                        name="date" value="{{ old('date', now()->format('Y-m-d')) }}" placeholder="Input Tanggal">
                    </input>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectKategori">
                    <label class="form-label" for="expense_category_id">Kategori</label>
                    <select class="form-control cust-control" id="category" name="expense_category_id">
                    </select>
                    @if ($errors->has('expense_category_id'))
                        <span class="help-block">{{ $errors->first('expense_category_id') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectDari">
                    <label class="form-label" for="dari">Diambil Dari</label>
                    <select class="form-control cust-control" id="dari" name="dari">
                    </select>
                    @if ($errors->has('dari'))
                        <span class="help-block">{{ $errors->first('dari') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectUntuk">
                    <label class="form-label" for="untuk">Pengeluaran Untuk</label>
                    <select class="form-control cust-control" id="untuk" name="untuk">
                    </select>
                    @if ($errors->has('untuk'))
                        <span class="help-block">{{ $errors->first('untuk') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="amount">Amount <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount"
                        name="amount" value="{{ old('amount') }}" placeholder="200.000">
                    </input>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" class="form-control" cols="30" rows="10">{{ old('keterangan') }}</textarea>
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
    $('#category').select2({
        dropdownParent: $('#formSelectKategori'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Kategori',
        ajax: {
            url: "{{ route('expense.category_getData') }}",
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

    $('#dari').select2({
        dropdownParent: $('#formSelectDari'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Akun Rekening',
        ajax: {
            url: "{{ route('expense.from') }}",
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

    $('#untuk').select2({
        dropdownParent: $('#formSelectUntuk'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Akun Rekening',
        ajax: {
            url: "{{ route('expense.to') }}",
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
                            id: item.id +
                                '_' + item.account_code_id,
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
    })

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
                } else if (!data.status) {
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
