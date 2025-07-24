<form method="POST" id="formPayment" action="{{ route('invoice.terminStore', [$data->id]) }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Termin {{ $data->is_quotation == 1 ? 'Quotation' : 'Invoice' }} {{ $data->name }}</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="date">Tanggal </label>
                    <input type="date" class="form-control" id="date" name="date"
                        value="{{ old('date', now()->format('Y-m-d')) }}">
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="payment_method">Metode Pembayaran</label>
                    <select class="form-control" id="payment_method" name="payment_method">
                        <option value="" selected disabled>Pilih Metode</option>
                        @foreach ($typePayment as $item)
                            <option value="{{ $item['code'] }}">{{ $item['method'] }}</option>
                        @endforeach
                    </select>
                    <small style="color: #385a9c; font-family: italic;">Aktifkan Metode Pembayaran Melalui Pengaturan Pembayaran</small>
                </div>
            </div>

            <div class="mb-4">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="nominal" class="form-label">Tipe Nominal</label>
                            <select name="nominal_type" class="form-control" onchange="countNominal();" id="nominal_type">
                                <option value="nominal" selected>Value</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                        <div class="col-md-10 mb-3">
                            <label for="nominal" class="form-label">Nominal</label>
                            <input type="text" class="form-control" id="nominal" oninput="countNominal()" placeholder="10" name="nominal">
                            <small id="sumNominal" class="form-text text-muted"></small>
                        </div>
                    </div>
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
    $(document).on('input', '#nominal', function() {
        value = formatCurrency($(this).val());
        $(this).val(value);
    });

    function countNominal() {
        var value = $('#nominal').val();
        var nominal_type = $('#nominal_type').val();
        var grand_total = "{{ $data->grand_total }}";
        
        if (nominal_type == 'percent') {
            value = (value / 100) * grand_total;
        } else {
            value = value
        }

        var text = 'Total Invoice : ' + formatCurrency(grand_total, 'Rp.') + ' <br> Nilai Termin : ' + formatCurrency(value, 'Rp.');
        $('#sumNominal').html(text);
    }

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
    document.getElementById('formPayment').addEventListener('submit', function(event) {
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
