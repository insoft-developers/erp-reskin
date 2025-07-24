@extends('master')

@section('style')
    <style>
        .table-responsive .table tbody tr:last-child *{
            border-bottom: 1px solid #e5e7eb !important;
        }
    </style>
@endsection

@section('content')
<main class="nxl-container">
    <div class="nxl-content">
        <!-- [ page-header ] start -->
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10"></h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('invoice.invoice.index') }}">Invoice</a></li>
                    <li class="breadcrumb-item">Invoice Builder</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">


                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ page-header ] end -->
        <!-- [ Main Content ] start -->
        <div class="main-content">
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title">Buat Invoice</h5>
                    </div>
                    <div class="card-body custom-card-action p-3">
                        <form action="{{ route('invoice.invoice.store') }}" method="POST" enctype="multipart/form-data" id="formCreate">
                            @csrf

                            @if (count($typePayment) < 1)
                                <div class="col-md-12 mtop20">
                                    <div class="alert alert-success">
                                        Anda belum melengkapi data rekening bank, Silahkan lengkapi terlebih dahulu
                                    </div>
                                </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="invoiceName" class="form-label">Nama Invoice</label>
                                    <input type="text" class="form-control" id="invoiceName" name="name">
                                </div>

                                <div class="col">
                                    <h5>Is Quotation</h5>
                                    <div class="mb-3">
                                        <select class="form-control select2" id="is_quotation" name="is_quotation" required>
                                            <option value="0" selected>Invoice</option>
                                            <option value="1">Quotation</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <h5>Dari</h5>
                                    <div class="mb-3">
                                        <select class="form-control select2" id="invoice_from" name="invoice_from" required>
                                            <option value="">Pilih Opsi</option>
                                            <option value="perusahaan">Perusahaan</option>
                                            <option value="personal">Personal</option>
                                        </select>
                                    </div>
                                    <div id="formInvoiceFrom"></div>
                                </div>
                                <div class="col">
                                    <h5>Kepada</h5>
                                    <div class="mb-3" id="formClientId">
                                        <select class="form-control" id="client_id" name="client_id" required>
                                            <option>Klien</option>
                                        </select>
                                    </div>
                                    <div id="clientDetail"></div>
                                </div>
                            </div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="invoiceNumber" class="form-label">No. Invoice</label>
        <input type="text" class="form-control" id="invoiceNumber" placeholder="Nomor Invoice" name="invoice_number">
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let invoiceName = document.getElementById('invoiceName');
        let invoiceNumber = document.getElementById('invoiceNumber');
        let isQuotation = document.getElementById('is_quotation');

        // Fungsi untuk mendapatkan bulan dan tahun saat ini dalam format MMYY
        function getCurrentMonthYear() {
            let today = new Date();
            let month = String(today.getMonth() + 1).padStart(2, '0'); // Tambah 1 karena bulan dimulai dari 0
            let year = String(today.getFullYear()).substr(-2); // Ambil 2 digit terakhir dari tahun
            return month + year;
        }

        // Fungsi untuk menghasilkan angka urut berdasarkan tanggal saat ini dan angka acak 00-99
        function generateSequentialNumber() {
            let today = new Date();
            let day = String(today.getDate()).padStart(2, '0'); // Ambil tanggal hari ini dengan 2 digit
            let randomNumber = Math.floor(Math.random() * 100).toString().padStart(2, '0'); // Angka acak antara 00 dan 99
            return day + randomNumber; // Gabungkan tanggal hari ini dan angka acak
        }

        // Fungsi untuk menghasilkan nomor invoice
        function generateInvoiceNumber() {
            let prefix = isQuotation.value === "0" ? "INV" : "QUO";
            let datePart = getCurrentMonthYear();
            let sequentialNumber = generateSequentialNumber(); // Gantikan dengan nomor urut dinamis
            invoiceNumber.value = prefix + datePart + "-" + sequentialNumber;
        }

        // Update nomor invoice saat nama invoice atau pilihan is_quotation berubah
        invoiceName.addEventListener('input', generateInvoiceNumber);
        isQuotation.addEventListener('change', generateInvoiceNumber);

        // Panggil saat halaman pertama kali dimuat
        generateInvoiceNumber();
    });
</script>

                                <div class="col-md-6 mb-3" id="formPayTo">
                                    <label for="payment_method" class="form-label">Pembayaran Ke</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="" selected disabled>Pilih Metode</option>
                                        @foreach ($typePayment as $item)
                                            <option value="{{ $item['code'] }}">{{ $item['method'] }}</option>
                                        @endforeach
                                    </select>
                                    <small style="color: #385a9c; font-family: italic;">Aktifkan Metode Pembayaran Melalui Pengaturan Pembayaran</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="invoiceDate" class="form-label">Tanggal Dibuat</label>
                                    <input type="date" class="form-control" id="invoiceDate" value="{{ now()->format('Y-m-d') }}" name="created">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="additional_intruction" class="form-label">Intruksi Tambahan</label>
                                    <textarea class="form-control" rows="5" placeholder="SWIFT/BIC Code: CENAIDJA &#10;Bank Address: BCA KCU Diponegoro, Dr. Soetomo Street No.118, Darmo Sub-District, Wonokromo District, Surabaya City, East Java, Indonesia 60241" name="additional_intruction"></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="dueDate" class="form-label">Jatuh Tempo</label>
                                    <input type="date" class="form-control" id="dueDate" value="{{ now()->format('Y-m-d') }}" name="due_date">
                                </div>

                                <div class="col-md-6 mb-3" id="formCurrency">
                                    <label for="currency" class="form-label">Mata Uang</label>
                                    <select class="form-control" id="currency_id" name="currency">
                                        <option value="1" selected>IDR - Indonesian Rupiah</option>
                                    </select>
                                    <small style="color: #385a9c; font-family: italic;">Pilih Mata Uang Yang ditampilkan di Invoice</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label for="discount" class="form-label">Tipe Diskon</label>
                                            <select name="discount_type" class="form-control" onchange="updateTotals();" id="discount_type">
                                                <option value="nominal" selected>Value</option>
                                                <option value="percent">%</option>
                                            </select>
                                        </div>
                                        <div class="col-md-10 mb-3">
                                            <label for="discount" class="form-label">Nilai Diskon</label>
                                            <input type="text" class="form-control" id="discount" placeholder="10" name="discount_value">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="exchangeRate" class="form-label">Kurs (Rupiah)</label>
                                    <input type="text" class="form-control" id="exchangeRate" placeholder="15000" name="kurs" value="1">
                                    <small style="color: #385a9c; font-family: italic;">Kurs Mata Uang dalam Indonesian Rupiah (Rp)</small>
                                </div>
                            </div>


                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label for="tax" class="form-label">Pajak</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" oninput="updateTotals();" id="taxRate" placeholder="10" value="{{ $data['tax'] }}" name="tax" step="0.01">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">DESKRIPSI</th>
                                                    <th scope="col">HARGA / NOMINAL</th>
                                                    <th scope="col">QTY</th>
                                                    <th scope="col">JUMLAH TOTAL</th>
                                                    <th scope="col">Aksi</th>
                                                </tr>
                                            </thead>

                                                 <tbody id="descriptionTable">
                                                <tr>
                                                    <td>
                                                        <input type="text" class="form-control" placeholder="Deskripsi Singkat" name="short_description[]">
                                                        <br>
                                                        <textarea class="form-control ckeditor" id="" cols="30" rows="5" placeholder="Deskripsi Tambahan" name="description[]"></textarea>
                                                    </td>
                                                    <td style="vertical-align: top;"><input type="text" oninput="numberFormat(this);" class="form-control harga" name="price[]"></td>
                                                    <td style="vertical-align: top;"><input type="number" oninput="updateTotals();" class="form-control qty" min="1" value="1" name="qty[]"></td>
                                                    <td style="vertical-align: top;" class="total">Rp0.00</td>
                                                    <td style="vertical-align: top;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Hapus</button></td>
                                                </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-primary mt-3" id="addRowBtn">Tambah Item</button>

                            <div class="row mt-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label">Notes:</label>
                                    <textarea class="form-control ckeditor" rows="8" placeholder="Mohon pastikan pembayaran dilakukan dalam waktu 30 hari menggunakan detail akun yang tersedia pada invoice. Jika Anda memiliki pertanyaan, silakan hubungi tim keuangan kami di (021) 555-1234 atau melalui email di mail@randu.co.id. Kami menghargai kemitraan Anda dan menantikan kerja sama yang berkelanjutan." name="notes"></textarea>
                                </div>
                                <div class="col-md-5">
                                    <table border="0" class="table">
                                        <tr>
                                            <th>Sub Total</th>
                                            <td>:</td>
                                            <td class="text-end" id="subTotal">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <th>Pajak</th>
                                            <td>:</td>
                                            <td class="text-end" id="taxAmount">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <th>Diskon</th>
                                            <td>:</td>
                                            <td class="text-end" id="discountAmount">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <th><strong>GRAND TOTAL</strong></th>
                                            <td>:</td>
                                            <td class="text-end"><strong id="grandTotal">0</strong></td>
                                        </tr>
                                    </table>
                                    <div id="inputHiddenTotals"></div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col">
                                    <label for="signatureField" class="form-label">Signature Field On/Off</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="signatureField">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-3" id="formSignature" hidden>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="signature_name" placeholder="Nama">
                                    <input type="text" class="form-control mt-2" name="signature_position" placeholder="Jabatan">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <button type="reset" class="btn btn-warning m-2">BERSIHKAN</button>
                                        <button type="submit" class="btn btn-success m-2">SIMPAN</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".ckeditor").forEach(textarea => {
            CKEDITOR.replace(textarea.id);
        });
    });

    var currency_symbol = 'Rp. ';
    $('#client_id').select2({
        dropdownParent: $("#formClientId"),
        ajax: {
            url: "{{ route('invoice.client_data') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term // search term
                };
            },
            processResults: function(data) {
                apiResults = data.data.map(function(item) {
                    return {
                        text: item.name,
                        id: item.id
                    };
                });

                return {
                    results: apiResults
                };
            },
            cache: false
        },
    })

    $('#currency_id').select2({
        dropdownParent: $("#formCurrency"),
        ajax: {
            url: "{{ route('currency') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term // search term
                };
            },
            processResults: function(data) {
                apiResults = data.data.map(function(item) {
                    return {
                        text: item.code+' - '+item.name,
                        id: item.id
                    };
                });

                return {
                    results: apiResults
                };
            },
            cache: false
        },
    })
    .on('select2:select', function (e) {
        var selectedData = e.params.data;        
        checkExchange(selectedData.id);
    });

    function checkExchange(id) {
        var currency_id = id;
        $.ajax({
            url: '{{ route("checkExchange", ":id") }}'.replace(':id', currency_id),
            type: 'GET', 
            dataType  : 'json',
        })
        .done(function(data) {
            console.log(data.data);
            
            var exchangeRate = formatCurrency(data.data.exchange);
            $('#exchangeRate').val(exchangeRate);
            currency_symbol = data.data.symbol == '' ? data.data.code : data.data.symbol;
            updateTotals();
        })
        .fail(function() {
            alert('Load data failed.');
        });
    }

    $('#client_id').on('change', function() {
        var id = $(this).val();
        $.ajax({
            url: "{{ route('invoice.client.show', ':id') }}".replace(':id', id),
            type: 'GET', 
            dataType  : 'json',
        })
        .done(function(data) {
            var data = data.data;
            var elem = `<div class="mb-3">
                            <input type="text" class="form-control" placeholder="Client Name" value="`+ data.name +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="name@client.com" value="`+ data.email +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Street" value="`+ data.address +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="(123) 456 789" value="`+ data.phone +`" disabled>
                        </div>`

            $('#clientDetail').html(elem)
        })
        .fail(function() {
            alert('Load data failed.');
        });
    });

    $('#invoice_from').on('change', function() {
        var value = $(this).val();
        var postForm = {
            'invoice_from': value
        }

        $.ajax({
            url: "{{ route('invoice.invoiceFrom') }}",
            type: 'GET', 
            data : postForm,
            dataType  : 'json',
        })
        .done(function(data) {
            var data = data.data;
            var elem = `<div class="mb-3">
                            <input type="text" class="form-control" placeholder="Business Name" value="`+ data.name +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="name@business.com" value="`+ data.email +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Street" value="`+ data.address +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="(123) 456 789" value="`+ data.phone +`" disabled>
                        </div>`

            $('#formInvoiceFrom').html(elem)
        })
        .fail(function() {
            alert('Load data failed.');
        });
    });

    $('#client_id').on('change', function() {
        var id = $(this).val();
        $.ajax({
            url: "{{ route('invoice.client.show', ':id') }}".replace(':id', id),
            type: 'GET', 
            dataType  : 'json',
        })
        .done(function(data) {
            var data = data.data;
            var elem = `<div class="mb-3">
                            <input type="text" class="form-control" placeholder="Client Name" value="`+ data.name +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="name@client.com" value="`+ data.email +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Street" value="`+ data.address +`" disabled>
                        </div>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="(123) 456 789" value="`+ data.phone +`" disabled>
                        </div>`

            $('#clientDetail').html(elem)
        })
        .fail(function() {
            alert('Load data failed.');
        });
    });

    $('#signatureField').on('change', function() {
        if (this.checked) {
            $("#formSignature").removeAttr("hidden");
        } else {
            $("#formSignature").attr("hidden", true);
        }
    });

    $('#exchangeRate').on('input', function() {
        var val = $(this).val();
        var value = formatCurrency(val);
        $(this).val(value == 0 ? '' : value);
    });

    $('#discount').on('input', function() {
        var val = $(this).val();
        $(this).val(formatCurrency(val));
        updateTotals();
    });

    function numberFormat(input) {
        var val = input.value;
        input.value = formatCurrency(val);
        updateTotals();
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

    function updateTotals() {
        let subTotal = 0;
        document.querySelectorAll('#descriptionTable tr').forEach(function(row) {
            var harga = row.querySelector('.harga').value;
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            harga = parseFloat(harga.replace(/\./g, '')) || 0;
            const total = harga * qty;
            subTotal += total;
            row.querySelector('.total').textContent = formatCurrency(total, currency_symbol);
        });

        const discount_type = document.getElementById('discount_type').value;
        var discount = document.getElementById('discount').value;
        discount = parseInt(discount.replace(/\./g, '')) || 0;

        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;

        if (discount_type === 'nominal') {
            discountAmount = discount;
        } else {
            discountAmount = (subTotal * discount) / 100;
        }

        const taxableAmount = subTotal;
        const taxAmount = parseInt((taxableAmount * taxRate) / 100);
        var grandTotal = (taxableAmount + taxAmount) - discountAmount;
        grandTotal = new Intl.NumberFormat().format(grandTotal);

        document.getElementById('subTotal').textContent = formatCurrency(subTotal, currency_symbol);
        document.getElementById('discountAmount').textContent = formatCurrency(discountAmount, currency_symbol);
        document.getElementById('taxAmount').textContent = formatCurrency(taxAmount, currency_symbol)
        document.getElementById('grandTotal').textContent = currency_symbol+' '+grandTotal;

        var inputHidden = '';
        inputHidden += `<input type="hidden" id="grand_total" name="grand_total" value="${grandTotal}">`;
        inputHidden += `<input type="hidden" id="sub_total" name="sub_total" value="${subTotal}">`;
        inputHidden += `<input type="hidden" id="discount_amount" name="discount_amount" value="${discountAmount}">`;
        inputHidden += `<input type="hidden" id="tax_amount" name="tax_amount" value="${taxAmount}">`;

        $('#inputHiddenTotals').html(inputHidden)
    }

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const table = document.getElementById('descriptionTable');
        const row = table.insertRow();
        row.innerHTML = `
            <td><input type="text" class="form-control" placeholder="Deskripsi Singkat" name="short_description[]"><br><textarea name="description[]" class="form-control ckeditor" id="" cols="30" rows="5" placeholder="Deskripsi Tambahan"></textarea></td>
            <td style="vertical-align: top;"><input type="text" oninput="numberFormat(this);" class="form-control harga" value="" name="price[]"></td>
            <td style="vertical-align: top;"><input type="number" oninput="updateTotals();" class="form-control qty" value="1" name="qty[]"></td>
            <td style="vertical-align: top;" class="total">${currency_symbol} 0.00</td>
            <td style="vertical-align: top;"><button type="button" class="btn btn-danger" onclick="removeRow(this)">Hapus</button></td>
        `;

        // Hapus semua inisialisasi CKEditor sebelumnya
        for (let instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].destroy();
        }

        // Inisialisasi ulang semua textarea dengan class "ckeditor"
        document.querySelectorAll(".ckeditor").forEach(textarea => {
            CKEDITOR.replace(textarea);
        });
    });

    function removeRow(button) {
        const row = button.parentNode.parentNode;
        row.parentNode.removeChild(row);
        updateTotals();
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
                    window.location.href = "{{ route('invoice.invoice.index') }}";
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
@endsection
