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
                        <li class="breadcrumb-item"><a href="{{ route('landing-page.index') }}">Landing Page</a></li>
                        <li class="breadcrumb-item">Landing Page Builder</li>
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
                        <form method="POST" action="{{ route('landing-page.update', ['landing_page' => $id]) }}"
                            class="card stretch stretch-full" enctype="multipart/form-data">
                            @method('PUT')
                            @csrf
                            <div class="card-header">
                                <h5 class="card-title">Landing Page</h5>

                                <a href="/landing-page" class="btn btn-danger">Kembali</a>
                            </div>
                            <div class="card-body custom-card-action p-4">
                                <div class="mb-4">
                                    <label class="form-label">Produk <span class="text-danger">*</span></label>
                                    <select class="form-control @error('product_id') is-invalid @enderror"
                                        id="searchProduct" name="product_id" value="{{ $data->product_id }}">
                                    </select>
                                    @error('product_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-4">
                                    <div class="row">
                                        <div class="col-12 col-xxl-6">
                                            <label class="form-label" for="title">Judul <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" value="{{ $data->title }}">
                                            </input>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-xxl-6">
                                            <label class="form-label">Slug <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                                id="slug" name="slug" value="{{ old('slug', $data->slug) }}"
                                                readonly>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="script_header" class="form-label">Script Header</label>
                                    <textarea name="script_header" id="script_header" class="form-control">{{ old('script_header') ?? $data->script_header }}</textarea>
                                    <div class="form-text">
                                        Ini sangat berguna jika anda ingin menambahkan custom script, misal anda ingin
                                        menambahkan script facebook atau google analytics untuk melihat jumlah trafic
                                        pelanggan dan ini akan diletakan di halaman landing page
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="script_header_payment_page" class="form-label">Script Header Payment
                                        Page</label>
                                    <textarea name="script_header_payment_page" id="script_header_payment_page" class="form-control">{{ old('script_header_payment_page') ?? $data->script_header_payment_page }}</textarea>
                                    <div class="form-text">
                                        Tujuannya sama dengan script header, ini akan di tambahkan ketika user di arahkan
                                        ke halaman payment setelah klik tombol aksi pada landing page
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="script_header_wa_page" class="form-label">Script Header Click To
                                        Whatsapp</label>
                                    <textarea name="script_header_wa_page" id="script_header_wa_page" class="form-control">{{ old('script_header_wa_page') ?? $data->script_header_wa_page }}</textarea>
                                    <div class="form-text">
                                        Tujuannya sama dengan script header, ini akan di tambahkan ketika fitur click to
                                        whatsapp di aktifkan
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label">Click To Whatsapp </label>
                                    <select class="form-select" data-select2-selector="default" name="click_to_wa"
                                        onchange="toggleCheckbox(this)">
                                        <option value="{{ 0 }}"
                                            @if (old('click_to_wa') == 0 || $data->click_to_wa == 0) selected @endif>Non Aktif</option>
                                        <option value="{{ 1 }}"
                                            @if (old('click_to_wa') == 1 || $data->click_to_wa == 1) selected @endif>Aktif</option>
                                    </select>
                                    <div class="form-text">
                                        Jika anda ingin langsung mengarahkan pelanggan ke whatsapp anda maka aktifkan, namun
                                        jika anda membutuhkan data pelanggan maka nonaktifkan agar pelanggan wajib mengisi
                                        data
                                        mereka
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="contact_seller">
                                        Phone <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" placeholder="Phone" name="contact_seller"
                                        id="contact_seller" @if ($data->click_to_wa == 0) disabled @endif
                                        value="{{ $data->contact_seller ?? ($user->information[0]->phone_number ?? '') }}" />
                                </div>
                                <h4>Data Pelanggan</h4>
                                <div class="form-check form-switch form-switch-sm ps-5">
                                    <input @if (old('with_customer_name') || $data->with_customer_name) checked @endif
                                        class="form-check-input c-pointer" type="checkbox" id="with_customer_name"
                                        name="with_customer_name" value="{{ 1 }}">
                                    <label class="form-check-label fw-500 text-dark c-pointer" for="with_customer_name">
                                        Aktifkan Nama
                                    </label>
                                </div>
                                <div class="form-check form-switch form-switch-sm ps-5">
                                    <input @if (old('with_customer_wa_number') || $data->with_customer_wa_number) checked @endif
                                        class="form-check-input c-pointer" type="checkbox" id="with_customer_wa_number"
                                        name="with_customer_wa_number" value="{{ 1 }}">
                                    <label class="form-check-label fw-500 text-dark c-pointer"
                                        for="with_customer_wa_number">
                                        Aktifkan Nomor Whatsapp
                                    </label>
                                </div>
                                <div class="form-check form-switch form-switch-sm ps-5">
                                    <input @if (old('with_customer_email') || $data->with_customer_email) checked @endif
                                        class="form-check-input c-pointer" type="checkbox" id="with_customer_email"
                                        name="with_customer_email" value="{{ 1 }}">
                                    <label class="form-check-label fw-500 text-dark c-pointer" for="with_customer_email">
                                        Aktifkan Email
                                    </label>
                                </div>
                                <div class="form-check form-switch form-switch-sm ps-5">
                                    <input @if (old('with_customer_full_address') || $data->with_customer_full_address) checked @endif
                                        class="form-check-input c-pointer" type="checkbox"
                                        id="with_customer_full_address" name="with_customer_full_address"
                                        value="{{ 1 }}">
                                    <label class="form-check-label fw-500 text-dark c-pointer"
                                        for="with_customer_full_address">
                                        Aktifkan Alamat Lengkap
                                    </label>
                                </div>
                                <div class="form-check form-switch form-switch-sm ps-5">
                                    <input @if (old('with_customer_proty') || $data->with_customer_proty) checked @endif
                                        class="form-check-input c-pointer" type="checkbox" id="with_customer_proty"
                                        name="with_customer_proty" value="{{ 1 }}">
                                    <label class="form-check-label fw-500 text-dark c-pointer" for="with_customer_proty">
                                        Aktifkan Provinsi & Kota
                                    </label>
                                </div>
                                <hr class="my-4" />
                                <h3>Bump Produk</h3>
                                <div class="row mt-4">
                                    <input type="hidden" name="bump_id" value="{{ $bump->id ?? null }}" />
                                    <div class="col-12 col-xl-6 mb-4">
                                        <label class="form-label" for="bump_product_id">Bump Produk</label>
                                        <select class="form-control" id="bump_product_id" name="bump_product_id"
                                            value="{{ old('bump_product_id') }}">
                                        </select>
                                        <div class="form-text">
                                            Silahkan pilih produk anda yang ingin dijadikan sebagai bump.
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-3 mb-4">
                                        <label class="form-label" for="bump_product_price">Bump Produk
                                            (Harga)</label>
                                        <input type="text" class="form-control" id="bump_product_price"
                                            name="bump_product_price" disabled />
                                        <div class="form-text">
                                            Akan otomatis update jika anda memberikan diskon
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-3 mb-4">
                                        <label class="form-label" for="bump_product_discount">Bump Produk
                                            (Diskon)</label>
                                        <div class="input-group flex-nowrap">
                                            <span class="input-group-text" id="addon-wrapping">%</span>
                                            <input type="number" class="form-control" id="bump_product_discount"
                                                name="bump_product_discount"
                                                value="{{ old('bump_product_discount') ?? ($bump->discount ?? '') }}" />
                                        </div>
                                        <div class="form-text">
                                            Jika anda ingin memberi potongan harga dari harga aslinya.
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-6 mb-4">
                                        <label class="form-label" for="bump_product_custom_name">Bump Produk (Nama
                                            Kustom)</label>
                                        <input type="text" class="form-control" id="bump_product_custom_name"
                                            name="bump_product_custom_name"
                                            value="{{ old('bump_product_custom_name') ?? ($bump->custom_name ?? '') }}" />
                                        <div class="form-text">
                                            Jika anda ingin menggunakan nama kustom untuk bump produk.
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-6 mb-4">
                                        <label class="form-label" for="bump_product_title">Bump Produk (Judul)</label>
                                        <input type="text" class="form-control" id="bump_product_title"
                                            name="bump_product_title"
                                            value="{{ old('bump_product_title') ?? ($bump->title ?? '') }}" />
                                        <div class="form-text">
                                            Judul yang menarik dapat meningkatkan penjualan.
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-6 mb-4">
                                        <label class="form-label" for="bump_product_description">
                                            Bump Produk (Deskripsi)
                                        </label>
                                        <textarea class="form-control" id="bump_product_description" name="bump_product_description">{{ old('bump_product_description') ?? ($bump->description ?? '') }}</textarea>
                                        <div class="form-text">
                                            Deskripsi produk yang informatif membantu calon pembeli memahami detail
                                            produk dengan lebih baik.
                                        </div>
                                    </div>
                                    <div class="col-12 col-xl-6 mb-4">
                                        <label class="form-label" for="bump_product_custom_photo">Bump Produk
                                            (Foto Kustom)</label>
                                        <input type="file" class="form-control" id="bump_product_custom_photo"
                                            name="bump_product_custom_photo" />
                                        <div class="form-text">
                                            Jika anda ingin menggunakan foto kustom untuk bump produk.
                                        </div>
                                        <div id="imagePreview" style="max-width: 100%; max-height: 300px;">
                                            @if (isset($bump) && $bump->custom_photo)
                                                <img src="{{ asset($bump->custom_photo) }}"
                                                    style="max-width: 100%; height: 300px;" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4">
                                <div class="mb-4">
                                    <label class="form-label" for="text_submit_button">Teks Tombol Submit <span
                                            class="text-danger">*</span></label>
                                    <input checked type="text"
                                        class="form-control @error('text_submit_button') is-invalid @enderror"
                                        placeholder="Masukan Teks Tombol Submit" name="text_submit_button"
                                        id="text_submit_button"
                                        value="{{ old('text_submit_button') ?? ($data->text_submit_button ?? 'Mulai Order Sekarang') }}">
                                    <div class="form-text">
                                        Anda dapat mengatur tombol aksi pembelian sesuai kebutuhan Anda. Contoh: Order
                                        Sekarang, Langganan Sekarang, Beli Sekarang dan lainnya
                                    </div>
                                    @error('text_submit_button')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <a href="/landing-page" class="btn btn-danger me-2">Kembali</a>
                                <a href="{{ route('landing-page.content-builder', ['id' => $id]) }}"
                                    class="btn btn-primary me-2">
                                    Content Builder
                                </a>
                                <button type="submit" class="btn btn-success">
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

        function calculateDiscountedPrice() {
            var data = $('#bump_product_id').select2('data')[0];
            var discount = parseFloat($('#bump_product_discount').val());
            if (discount && discount !== 0) {
                var discountedPrice = data.price - (data.price * discount / 100);
                $('#bump_product_price').val(formatCurrency(discountedPrice, 'Rp. '));
            } else {
                $('#bump_product_price').val(formatCurrency(data.price, 'Rp. '));
            }
        }

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

        function convertToSlug(text) {
            return text.toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
        }

        $(document).ready(function() {
            $('#searchProduct').select2({
                theme: 'bootstrap-5',
                placeholder: 'Pilih Produk',
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
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Event listener for title input
            $('#title').on('input', function() {
                let title = $(this).val();
                let slug = convertToSlug(title);
                $('#slug').val(slug);
            });

            // Set nilai inisialisasi pada Select2
            var productId = '{{ $data->product_id }}'; // Ambil nilai product_id dari variabel Blade
            if (productId) {
                $.ajax({
                    url: '/api/products/' + productId, // Ganti dengan URL yang sesuai
                    type: 'GET',
                    success: function(response) {
                        var option = new Option(response.name, response.id, true, true);
                        $('#searchProduct').append(option).trigger('change');
                    }
                });
            }

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

            // Set nilai inisialisasi pada Select2
            var bumpProductId = '{{ $bump->product_id ?? null }}'; // Ambil nilai product_id dari variabel Blade
            if (bumpProductId) {
                $.ajax({
                    url: '/api/products/' + bumpProductId, // Ganti dengan URL yang sesuai
                    type: 'GET',
                    success: function(response) {
                        var option = new Option(response.name, response.id, true, true);
                        $('#bump_product_id').append(option).trigger('change');

                        var discount = parseFloat($('#bump_product_discount').val());
                        if (discount && discount !== 0) {
                            var discountedPrice = response.price - (response.price * discount / 100);
                            $('#bump_product_price').val(formatCurrency(discountedPrice, 'Rp. '));
                        }
                    }
                });
            }

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
