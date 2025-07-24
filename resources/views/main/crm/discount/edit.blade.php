<form method="POST" id="formEdit" action="{{ route('crm.discount.update', $data->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Diskon Baru</h5>
        </div>
        <div class="modal-body">
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="code">Code <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror"
                        id="code" name="code" value="{{ old('code', $data->code) }}" placeholder="">
                    </input>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="name">Name <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name', $data->name) }}" placeholder="">
                    </input>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="type">Type</label>
                    <select class="form-select @error('type') is-invalid @enderror"
                        id="type" name="type" value="{{ old('type') }}" onchange="toggleCheckbox(this)">
                        <option value="persen" {{ $data->type == 'persen' ? 'selected' : '' }}>Percentage</option>
                        <option value="nominal" {{ $data->type == 'nominal' ? 'selected' : '' }}>Nominal</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="value">Nilai <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('value') is-invalid @enderror"
                        id="value" name="value" value="{{ old('value', $data->value) }}" placeholder="Nilai">
                    </input>
                    @error('value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="min_order">Minimal Total Pesanan <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('min_order') is-invalid @enderror"
                        id="min_order" name="min_order" value="{{ old('min_order', $data->min_order) }}" placeholder="200.000">
                    </input>
                    @error('min_order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="expired_at">Expired At <span
                            class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('expired_at') is-invalid @enderror"
                        id="expired_at" name="expired_at" value="{{ old('expired_at', $data->expired_at) }}" placeholder="">
                    </input>
                    @error('expired_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            {{-- <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="max_use">Maksimal Dipakai<span
                            class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_use') is-invalid @enderror"
                        id="max_use" name="max_use" value="{{ old('max_use', $data->max_use) }}" placeholder="">
                    </input>
                    @error('max_use')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div> --}}
            {{-- <div class="mb-4">
                <div class="col-12 col-xxl-12">
                    <label class="form-label" for="allowed_multiple_use">Boleh Digunakan Beberapa Kali <span class="text-danger">pada customer yang sama</span></label>
                    <select class="form-select @error('allowed_multiple_use') is-invalid @enderror"
                        id="allowed_multiple_use" name="allowed_multiple_use" value="{{ old('allowed_multiple_use') }}" onchange="toggleCheckbox(this)">
                        <option value="1" {{ ($data->allowed_multiple_use == 1) ? 'selected' : '' }}>Ya</option>
                        <option value="0" {{ ($data->allowed_multiple_use == 0) ? 'selected' : '' }}>Tidak</option>
                    </select>
                    @error('allowed_multiple_use')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div> --}}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</form>

<script>
    $(document).on('input', '#min_order', function() {
            value = formatCurrency($(this).val());
            $(this).val(value);
        })

        $(document).on('change', '#customer_id', function() {
            if ($(this).is(':checked')) {
                $('#searchCustomer').attr('disabled', true);
            } else {
                $('#searchCustomer').attr('disabled', false);
            }
        })

        $(document).on('input', '#value', function() {
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

        $(document).on('input', '#code', function() {
            const value = $(event.target).val().toUpperCase();
            const allowedCharacters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            const maxLength = 10;

            for (let i = 0; i < value.length; i++) {
                $(event.target).val(value);

                if (!allowedCharacters.includes(value[i]) || value.length > maxLength) {
                    $(event.target).val(value.substring(0, i) + value.substring(i + 1));
                }
            }
        });
</script>
<script>
    document.getElementById('formEdit').addEventListener('submit', function(event) {
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