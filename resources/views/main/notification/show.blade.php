@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
       
            <div style="background: whitesmoke;" class="content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <!-- Notification Detail View -->
                        <div class="container">
                            <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-3">
                                <div class="row no-gutters">
                                    <div class="col-md-12 text-center">
                                        @if($data->image != null)
                                        <img src="{{ asset('storage/'.$data->image) }}" alt="data Image" width="300px">
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <div class="card-body">
                                            <h5 class="">{{ $data->title }}</h5>
                                            <p class="card-text">
                                                <small class="text-muted">By {{ $data->penulis->fullname ?? '' }} on {{ $data->created_at->diffForHumans() }}</small>
                                            </p>
                                            <p class="card-text">{!! $data->description !!}</p>
                                        </div>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>


                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        
    </div>
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
