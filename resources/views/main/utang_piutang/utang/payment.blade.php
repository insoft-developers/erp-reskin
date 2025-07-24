<form method="POST" id="formPayment" action="{{ route('utang.payment', [$data->id]) }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content" id="modal-bayar-utang">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Bayar Utang {{ $data->name }}</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="created_at">Tanggal </label>
                    <input type="datetime-local" class="form-control" id="created_at" name="created_at"
                        value="{{ old('created_at', now()) }}">
                    @error('created_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="payment_from_id">Bayar Dengan: <span
                            class="text-danger">*</span></label>
                    <select class="form-control @error('payment_from_id') is-invalid @enderror" id="payment_from_id"
                        name="payment_from_id" value="{{ old('payment_from_id') }}">
                        @foreach ($data_akun as $akun)
                            <option value="{{ $akun['id'] }}_{{ $akun['account_code_id'] }}">{{ $akun['name'] }}</option>
                        @endforeach
                    </select>
                    @error('payment_from_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="amount">Nominal <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('amount') is-invalid @enderror" id="amount"
                        name="amount" value="{{ old('amount') }}" placeholder="15.000.000">
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
    $("document").ready(function() {
        $("#payment_from_id").select2({
            dropdownParent: $("#modal-bayar-utang")
        });
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
