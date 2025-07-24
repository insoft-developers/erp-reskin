<form method="POST" id="formCreate" action="{{ route('adjustment.category.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="modal-content">
        <div class="modal-header" style="background-color: #2f467a;">
            <h5 class="modal-title" style="color:white;">Kategori Penyesuaian Baru</h5>
        </div>
        <div class="modal-body">

            <div class="mb-4">
    <div class="col-12 col-xxl-12">
        <label class="form-label" for="name">Nama Kategori <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Cth. Barang Hilang">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

            <div class="mb-4">
    <div class="col-12 col-xxl-12">
        <label class="form-label" for="code">Kode Kategori <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" placeholder="" readonly>
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>



<script>
    // Fungsi untuk menghasilkan 2 huruf konsonan acak, 2 angka, dan 1 huruf vokal (semuanya uppercase)
    function generateCode() {
        const consonants = 'BCDFGHJKLMNPQRSTVWXYZ'; // Daftar konsonan
        const vowels = 'AEIOU'; // Daftar vokal
        let randomConsonants = '';
        let randomVowel = '';
        
        // Menghasilkan 2 huruf konsonan acak
        for (let i = 0; i < 2; i++) {
            randomConsonants += consonants.charAt(Math.floor(Math.random() * consonants.length));
        }

        // Menghasilkan 2 angka acak
        const randomNumbers = Math.floor(10 + Math.random() * 90); // Menghasilkan angka acak antara 10 dan 99

        // Menghasilkan 1 huruf vokal acak
        randomVowel = vowels.charAt(Math.floor(Math.random() * vowels.length));

        return randomConsonants + randomNumbers + randomVowel; // Format: 2 konsonan + 2 angka + 1 vokal
    }

    document.getElementById('name').addEventListener('input', function() {
        // Set value kolom 'code' ketika ada input di kolom 'name'
        document.getElementById('code').value = generateCode();
    });
</script>



            
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
            const maxLength = 5;

            for (let i = 0; i < value.length; i++) {
                $(event.target).val(value);

                if (!allowedCharacters.includes(value[i]) || value.length > maxLength) {
                    $(event.target).val(value.substring(0, i) + value.substring(i + 1));
                }
            }
        });
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
