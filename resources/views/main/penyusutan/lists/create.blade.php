<form method="POST" id="formCreate" action="{{ route('penyusutan.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Penyusutan Baru</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="mb-4">
                    <div class="col-12 col-xxl-12">
                        <label class="form-label" for="date">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                            name="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                            placeholder="Input Nama Asset">
                        </input>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-xxl-12" id="formSelectKategoriPenyusutan">
                    <label class="form-label" for="ml_fixed_asset_id">Kategori Penyusutan</label>
                    <select class="form-control cust-control" id="ml_fixed_asset_id" name="ml_fixed_asset_id">
                    </select>
                    @if ($errors->has('ml_fixed_asset_id'))
                        <span class="help-block">{{ $errors->first('ml_fixed_asset_id') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectAkumulasiPenyusutan">
                    <label class="form-label" for="ml_accumulated_depreciation_id">Akumulasi Penyusutan</label>
                    <select class="form-control cust-control" id="ml_accumulated_depreciation_id"
                        name="ml_accumulated_depreciation_id">
                    </select>
                    @if ($errors->has('ml_accumulated_depreciation_id'))
                        <span class="help-block">{{ $errors->first('ml_accumulated_depreciation_id') }}</span>
                    @endif
                </div>
            </div>


            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectBebanPenyusutan">
                    <label class="form-label" for="ml_admin_general_fee_id">Beban Penyusutan</label>
                    <select class="form-control cust-control" id="ml_admin_general_fee_id"
                        name="ml_admin_general_fee_id">
                    </select>
                    @if ($errors->has('ml_admin_general_fee_id'))
                        <span class="help-block">{{ $errors->first('ml_admin_general_fee_id') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12" id="formSelectBuyingWithAccount">
                    <label class="form-label" for="buying_with_account">Aset dibeli dengan</label>
                    <select class="form-control cust-control" id="buying_with_account" name="buying_with_account"
                        required>
                        <option value="">Pilih</option>
                        <optgroup label="BELI DENGAN CASH/TUNAI">
                            @foreach ($lancar as $l)
                                <option value="{{ $l->id }}_{{ $l->account_code_id }}_1">{{ $l->name }}
                                </option>
                            @endforeach
                        </optgroup>
                        <optgroup label="BELI DENGAN UTANG JANGKA PENDEK">
                            @foreach ($pendek as $p)
                                <option value="{{ $p->id }}_{{ $p->account_code_id }}_2">{{ $p->name }}
                                </option>
                            @endforeach
                        </optgroup>

                        <optgroup label="BELI DENGAN UTANG JANGKA PANJANG">
                            @foreach ($panjang as $j)
                                <option value="{{ $j->id }}_{{ $j->account_code_id }}_2">{{ $j->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    </select>
                    @if ($errors->has('buying_with_account'))
                        <span class="help-block">{{ $errors->first('buying_with_account') }}</span>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="name">Nama Asset <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name') }}" placeholder="Input Nama Asset">
                    </input>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="initial_value">Nilai Awal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('initial_value') is-invalid @enderror"
                        id="initial_value" name="initial_value" value="{{ old('initial_value') }}"
                        placeholder="15.000.000">
                    </input>
                    @error('initial_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="initial_value">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                        name="quantity" value="{{ old('quantity') }}" placeholder="masukkan jumlah pembelian asset">
                    </input>
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="useful_life">Umur Manfaat <span
                            class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" class="form-control @error('useful_life') is-invalid @enderror"
                            id="useful_life" name="useful_life" value="{{ old('useful_life', 1) }}"
                            placeholder="10">
                        </input>
                        <div class="input-group-append">
                            <span class="input-group-text" style="padding: 12px 15px !important;">Bulan</span>
                        </div>
                    </div>
                    @error('useful_life')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="residual_value">Nilai Residu <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('residual_value') is-invalid @enderror"
                        id="residual_value" name="residual_value" value="{{ old('residual_value') }}"
                        placeholder="0">
                    </input>
                    <small>Nilai residu adalah nilai sisa atau nilai perkiraan yang diharapkan dari suatu aset setelah
                        habis masa manfaatnya.</small>
                    @error('residual_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="residual_value">Keterangan </label>
                    <div class="alert alert-warning">
                        Penyusutan Ini Menggunakan Metode Garis Lurus. Penyusutan Aset (Aktiva Tetap) dengan beban
                        penyusutan tetap setiap bulan.
                    </div>
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
    $(document).ready(function() {
        $("#buying_with_account").select2();
    });


    $('#ml_fixed_asset_id').select2({
        dropdownParent: $('#formSelectKategoriPenyusutan'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Kategori Penyusutan',
        ajax: {
            url: "{{ route('penyusutan.mlFixedAsset') }}",
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

    $('#ml_accumulated_depreciation_id').select2({
        dropdownParent: $('#formSelectAkumulasiPenyusutan'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Akumulasi Penyusutan',
        ajax: {
            url: "{{ route('penyusutan.mlAccumulateDepreciation') }}",
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

    $('#ml_admin_general_fee_id').select2({
        dropdownParent: $('#formSelectBebanPenyusutan'),
        theme: 'bootstrap-5',
        placeholder: 'Pilih Beban Penyusutan',
        ajax: {
            url: "{{ route('penyusutan.mlAdminGeneralFee') }}",
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

    $(document).on('input', '#residual_value', function() {

        value = formatCurrency($(this).val());
        $(this).val(value);

    })

    $(document).on('input', '#initial_value', function() {
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
