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
                        <li class="breadcrumb-item"><a href="{{ route('adjustment.index') }}">Penyesuaian</a></li>
                        <li class="breadcrumb-item">Penyesuaian Barang Setengah Jadi Builder</li>
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
                        <form method="POST" action="{{ route('adjustment.storeInterProduct') }}"
                            class="card stretch stretch-full" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header">
                                <h5 class="card-title">Penyesuaian Barang Setengah Jadi</h5>
                            </div>
                            <div class="card-body custom-card-action p-4">
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label" for="date">Tanggal <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror"
                                            id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                                            placeholder="">
                                        </input>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col">
                                        <div class="mb-4">
                                            <label class="form-label">Kategori Penyesuaian <span
                                                    class="text-danger">*</span></label>
                                            <select
                                                class="form-control @error('category_adjustment_id') is-invalid @enderror"
                                                id="categoryAdjustment" name="category_adjustment_id"
                                                value="{{ old('category_adjustment_id') }}">
                                            </select>
                                            @error('category_adjustment_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-4" id="costGoodSoldForm">
                                            <label class="form-label">Penyesuaian Untuk <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control @error('cost_good_sold_id') is-invalid @enderror"
                                                id="costGoodSold" name="cost_good_sold_id"
                                                value="{{ old('cost_good_sold_id') }}">
                                                @foreach ($costGoodSold as $item)
                                                    <option value="{{ $item['id'] }}" {{ ($item['name'] == 'Harga Pokok Penjualan') ? 'selected' : '' }}>{{ $item['code'] == 'prive' ? 'Prive / Transfer Stok' :  $item['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('cost_good_sold_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Produk <span class="text-danger">*</span></label>
                                    <select class="form-control @error('md_inter_product_id') is-invalid @enderror"
                                        id="searchProduct" name="md_inter_product_id"
                                        value="{{ old('md_inter_product_id') }}">
                                    </select>
                                    @error('md_inter_product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>


                            <hr>

                            <div class="table-responsive">
                                <table class="table table-striped " id="data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Tipe</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="contentTable">

                                    </tbody>
                                </table>
                            </div>


<div class="card-footer d-flex justify-content-start">
    <a href="{{ route('adjustment.index') }}" class="btn btn-danger me-3">Kembali</a>
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
                theme: 'bootstrap-5',
                placeholder: 'Pilih Produk',
                ajax: {
                    url: "{{ route('adjustment.interProduct') }}",
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
                                    text: item.product_name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#categoryAdjustment').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Kategori Penyesuaian',
                ajax: {
                    url: "{{ url('adjustment/category-data') }}",
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
                                    id: item.ids,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // $('#costGoodSold').select2({
            //     dropdownParent: $('#costGoodSoldForm'),
            //     theme: 'bootstrap-5',
            //     placeholder: 'Pilih Opsi',
            //     ajax: {
            //         url: "{{ route('adjustment.costGoodSold') }}",
            //         dataType: 'json',
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 keyword: params.term,
            //                 limit: 25
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         id: item.id,
            //                         text: item.name
            //                     };
            //                 })
            //             };
            //         },
            //         cache: true
            //     }
            // });

            $(document).on('change', '#searchProduct', function() {
                var product = $('#searchProduct').select2('data')[0];
                var category = $('#categoryAdjustment').select2('data')[0];

                // APPENT IN TABLE BODY contentTable
                var contentTable = `
                <tr id="row${no}">
                    <td>${no}</td>
                    <td>
                        ${product.text}
                        <input type="hidden" name="md_inter_product_id[]" value="${product.id}">
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control" min="1" value="1">
                    </td>
                    <td>
                        <select name="type[]" id="type" class="form-control">
                            <option value="addition" selected>Addition - Penambahan</option>
                            <option value="substraction">Subtraction - Pengurangan</option>
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteRow(this)"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                `;
                $('#contentTable').append(contentTable);
                no += 1;
            });

            // Event listener for title input
            $('#title').on('input', function() {
                let title = $(this).val();
                let slug = convertToSlug(title);
                $('#slug').val(slug);
            });

            $('#bump_md_inter_product_id').select2({
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
