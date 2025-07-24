@extends('master')

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
                        <li class="breadcrumb-item"><a href="{{ route('expense.category.index') }}">Kategori Biaya</a></li>
                        <li class="breadcrumb-item">Kategori Biaya Builder</li>
                        <li class="breadcrumb-item">Buat Baru</li>
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
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('expense.category.store') }}"
                            class="card stretch stretch-full" enctype="multipart/form-data">
                            @csrf

                            <div class="card-header">
                                <h5 class="card-title">Kategori Biaya</h5>
                            </div>
                            <div class="card-body custom-card-action p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label" for="name">Nama Kategori <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}"
                                            placeholder="Contoh: Biaya Pengiriman">
                                        </input>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6" id="searchProductForm">
                                        <label class="form-label">Produk <span class="text-danger">*</span></label>
                                        <select class="form-control @error('product_id') is-invalid @enderror"
                                            id="searchProduct" name="product_id" value="{{ old('product_id') }}">
                                        </select>
                                        @error('product_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                            </div>


                            <hr>

                            <div class="table-responsive">
                                <table class="table table-striped " id="data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contentTable">

                                    </tbody>
                                </table>
                            </div>


                            <div class="card-footer d-flex justify-content-end">
                                <a href="{{ route('expense.category.index') }}" class="btn btn-danger me-2">Kembali</a>
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>
@endsection

@section('js')
    <script>
        function toggleCheckbox(selectElement) {
            var checkbox_customer_name = document.getElementById('with_customer_name')
            var checkbox_customer_wa_number = document.getElementById('with_customer_wa_number')
            var checkbox_customer_email = document.getElementById('with_customer_email')
            var checkbox_customer_full_address = document.getElementById('with_customer_full_address')
            var checkbox_customer_proty = document.getElementById('with_customer_proty')
            var input_contact_seller = document.getElementById('contact_seller')
            if (selectElement.value === '1') {
                checkbox_customer_name.disabled = true;
                checkbox_customer_wa_number.disabled = true;
                checkbox_customer_email.disabled = true;
                checkbox_customer_full_address.disabled = true;
                checkbox_customer_proty.disabled = true;
                input_contact_seller.disabled = false;
            } else {
                checkbox_customer_name.disabled = false;
                checkbox_customer_wa_number.disabled = false;
                checkbox_customer_email.disabled = false;
                checkbox_customer_full_address.disabled = false;
                checkbox_customer_proty.disabled = false;
                input_contact_seller.disabled = true;
            }
        }

        var no = 1;

        function formatCurrency(angka, prefix) {
            if (!angka) {
                return (prefix || '') + '-';
            }

            angka = angka.toString();
            const splitDecimal = angka.split('.');
            angka = splitDecimal[0];
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

        function deleteRow(r) {
            var i = r.parentNode.parentNode.rowIndex;

            document.getElementById("data-table").deleteRow(i);
        }

        $(document).ready(function() {
            $('#searchProduct').select2({
                dropdownParent: $("#searchProductForm"),
                placeholder: 'Semua Produk',
                ajax: {
                    url: "{{ url('api/products') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        apiResults = data.map(function(item) {
                            return {
                                id: item.id,
                                text: item.name
                            };
                        });

                        apiResults.unshift({
                            id: 'all',
                            text: 'Semua Produk'
                        });

                        return {
                            results: apiResults
                        };
                    },
                    cache: false
                },
            });

            $(document).on('change', '#searchProduct', function() {
                var product = $('#searchProduct').select2('data')[0];
                if (product.id === 'all') {
                    $.ajax({
                            url: "{{ url('api/products?pages=all') }}",
                            type: 'GET',
                            dataType: 'json',
                        })
                        .done(function(data) {
                            var productList = data;

                            productList.forEach(function(item) {
                                var contentTable = `
                                <tr id="row${no}">
                                    <td>${no}</td>
                                    <td>
                                        ${item.name}
                                        <input type="hidden" name="product_id[]" value="${item.id}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>`;

                                $('#contentTable').append(contentTable);
                                no += 1;
                            });
                            $('#contentTable').append(contentTable);

                        })
                        .fail(function() {
                            alert('Load data failed.');
                        });
                } else {
                    // APPENT IN TABLE BODY contentTable
                    var contentTable = `
                    <tr id="row${no}">
                        <td>${no}</td>
                        <td>
                            ${product.text}
                            <input type="hidden" name="product_id[]" value="${product.id}">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                    `;
                    $('#contentTable').append(contentTable);
                    no += 1;
                }
            });

            // Event listener for title input
            $('#title').on('input', function() {
                let title = $(this).val();
                let slug = convertToSlug(title);
                $('#slug').val(slug);
            });

            $('#bump_product_id').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Bump Produk',
                ajax: {
                    url: '/api/products',
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
                                    text: item.name,
                                    price: item.price
                                };
                            })
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                var data = e.params.data;
                $('#bump_product_custom_name').val(data.text);
                //$('#bump_product_price').val(data.price);

                calculateDiscountedPrice(); // Memanggil fungsi untuk menghitung harga setelah diskon
            });

            $('#bump_product_discount').on('keyup', function() {
                calculateDiscountedPrice();
                // Memanggil fungsi untuk menghitung harga setelah diskon saat nilai diskon berubah
            });

            // Ambil elemen input file
            const input = document.getElementById('bump_product_custom_photo');
            // Ambil elemen div untuk menampilkan gambar
            const imagePreview = document.getElementById('imagePreview');

            // Tambahkan event listener untuk peristiwa unggah file
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML =
                            `<img src="${e.target.result}" style="max-width: 100%; height: 300px;" />`;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
