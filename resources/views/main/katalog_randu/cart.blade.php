<style>
    .checkout-container {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .order-summary {
        border: 1px dashed #ccc;
        padding: 20px;
        margin-top: 20px;
    }

    .order-summary .total {
        font-size: 24px;
        font-weight: bold;
        margin-top: 20px;
    }

    .order-summary .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .product-image {
        width: 100px;
        height: auto;
    }

    .product-name {
        font-size: 18px;
        font-weight: bold;
    }

    .product-price {
        font-size: 16px;
        color: #666;
    }

    .btn-remove {
        background-color: #ff6b6b;
        border-color: #ff6b6b;
        color: white;
    }

    .btn-remove:hover {
        background-color: #ff4b4b;
        border-color: #ff4b4b;
    }

    .shipping-methods label {
        font-weight: normal;
    }

    .summary-details span {
        display: inline-block;
        width: 120px;
    }

    .input-group-text, .input-group-append button{
        padding: 15px !important;
    }
</style>
<div class="modal-content">
    <div class="modal-header" style="background-color: #2f467a;">
        <h5 class="modal-title" style="color:white;">Checkout</h5>
    </div>
    <div class="modal-body">
        <div class="row checkout-container">
            <div class="col-md-7 m-3">
                <h3>Keranjang Belanja</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Jumlah</th>
                                <th>Harga Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('cartKatalogProduct') as $item)
                                <tr id="cartKatalogProduct-{{ $item['id'] }}">
                                    <td><img src="{{ asset('storage/' . $item['image']) }}" class="product-image"
                                            alt="Paket Tablet Android + Printer Bluetooth"></td>
                                    <td>
                                        <div class="product-name">{{ \Str::limit($item['name'], 40) }}</div>
                                        <div class="product-price">Rp {{ number_format($item['selling_price']) }}</div>
                                    </td>
                                    <td width="100px">
                                        <div class="input-group">
                                            <input type="number" class="form-control" oninput="updateCart('{{ $item['id'] }}', this.value)" value="{{ $item['quantity'] }}" min="1">
                                        </div>
                                    </td>
                                    <td id="subtotalKatalogProduct-{{ $item['id'] }}">Rp {{ number_format($item['selling_price'] * $item['quantity']) }}</td>
                                    <td><button class="btn btn-remove" type="button" onclick="removeToCart('{{ $item['id'] }}')"><span
                                                class="glyphicon glyphicon-trash"><i class="fa fa-trash"></i></span></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr style="border-style: dashed !important; border-width: 1px !important; border-color: black !important;">

                <h5>Masukkan Detail Pengiriman</h5>
                <form action='{{ route('katalog-randu.store') }}' method='POST' id="formPlaceOrder" enctype='multipart/form-data'>
                    @csrf
                        <div class="row" id="addNewBuyer">
                            <div class="col-md-6">
                                <div class="form-group m-1">
                                    <label for="name">Nama Penerima</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan Nama Lengkap" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group m-1">
                                    <label for="phone">Nomor Telepon</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">+62</span>
                                        </div>
                                        <input type="number" class="form-control" id="phone" name="phone" placeholder="Masukkan Nomor Telepon Aktif" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="buyer-province" class="col-form-label">Provinsi:</label>
                                <select id="buyer-province" name="province_id" class="form-control" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="buyer-city" class="col-form-label">Kota:</label>
                                <select id="buyer-city" name="city_id" class="form-control" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="buyer-district" class="col-form-label">Kecamatan:</label>
                                <select id="buyer-district" name="district_id" class="form-control" required>
                                    <option value=""></option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group m-1">
                                    <label for="address">Alamat</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                </div>
                            </div>

                            <div id="inputHiddenDiscount"></div>
                            <div id="inputHiddenOngkir"></div>
                        </div>

                        <hr style="border-style: dashed !important; border-width: 1px !important; border-color: black !important;">
                        <h5>Pilihan Kurir Pengiriman</h5>

                        <div class="shipping-methods row">
                            @foreach ($data['shipping'] as $item)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shipping" onchange="cekOngkir(`{{ $item['method'] }}`)" id="{{ $item['id'] }}" value="{{ $item['method'] }}" required>
                                        <label class="form-check-label" for="jne">
                                            {{ $item['method'] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="col-md-12" style="display: none;" id="ongkirNotSupport">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <span>Mohon Maaf Pengiriman Ini Belum Disupport Dilahkan Order Menggunakan Livechat</span>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3" id="ongkirElement">
                            <div class="form-group">
                                <label for="">Service</label>
                                <select name="courier" id="service_ongkir" class="form-control">
                                    <option value="" selected disabled>Pilih Jasa Kirim</option>
                                </select>
                            </div>
                        </div>

                </form>
            </div>

            <div class="col-md-4 order-summary">
                <h3>Ringkasan Pembelian</h3>
                <div class="form-group m-1 mb-3">
                    <label for="voucher">Kode Kupon (Jika Ada)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="voucher">
                        <div class="input-group-append">
                            <button class="btn bg-randu" type="button" onclick="checkVoucher()">Gunakan</button>
                        </div>
                    </div>
                    <div id="voucherResult">
                    </div>
                </div>

                <div>
                    <p><span>Subtotal:</span> <span class="float-end" id="subtotalKatalogProduct">Rp. {{ number_format($data['sub_total'], 0, ',', '.') }}</span></p>
                    <p><span>Pajak:</span> <span class="float-end" id="pajakKatalogProduct"> Rp. {{ number_format($data['pajak'], 0, ',', '.') }}</span></p>
                    <p><span>Biaya Kirim:</span> <span class="float-end" id="ongkirKatalogProduct"> Rp. {{ number_format($data['ongkir'], 0, ',', '.') }}</span></p>
                    <p><span>Diskon:</span> <span class="text-success float-end" id="diskonKatalogProduct">Rp. {{ number_format($data['diskon'], 0, ',', '.') }}</span></p>
                    <p class="total">Total: <span class="float-end" id="totalKatalogProduct"> Rp. {{ number_format($data['total'], 0, ',', '.') }}</span></p>
                </div>
                <button class="btn bg-randu w-100" type="button" style="font-size: 15px;" id="btnPlaceOrder">Bayar Sekarang</button>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
</div>

<script>
    function removeToCart(id) {
        $.ajax({
            type: 'DELETE',
            url: "{{ route('katalog-randu.remove-to-cart', ':id') }}".replace(':id', id),
            data: {
                _token: "{{ csrf_token() }}"
            }
        })
        .done(function(data) {
            $('#cartKatalogProduct-' + id).remove();
            $('#cart-count').text(' (' + data.item_count + ')');
            $('#subtotalKatalogProduct').text(data.sub_total);
            $('#pajakKatalogProduct').text(data.pajak);
            $('#ongkirKatalogProduct').text(data.ongkir);
            $('#diskonKatalogProduct').text(data.diskon);
            $('#totalKatalogProduct').text(data.total);

            Swal.fire('Success!', data.message, 'success');
        })
        .fail(function() {
            Swal.fire('Error!', 'An error occurred while removing the product from cart.', 'error');
        });
    }

    function updateCart(id, qty) {
        $.ajax({
            type: 'PUT',
            url: "{{ route('katalog-randu.update-cart', ':id') }}".replace(':id', id),
            data: {
                _token: "{{ csrf_token() }}",
                quantity: qty
            }
        })
        .done(function(data) {
            $('#cart-count').text(' (' + data.item_count + ')');
            $('#subtotalKatalogProduct-' + id).text(data.sub_total_product);
            $('#subtotalKatalogProduct').text(data.sub_total);
            $('#pajakKatalogProduct').text(data.pajak);
            $('#ongkirKatalogProduct').text(data.ongkir);
            $('#diskonKatalogProduct').text(data.diskon);
            $('#totalKatalogProduct').text(data.total);
        })
        .fail(function() {
            Swal.fire('Error!', 'An error occurred while updating the cart.', 'error');
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    var prov_id = 0;
    var city_id = 0;
    var dist_id = 0;

    $('#buyer-province').select2({
        dropdownParent: $("#addNewBuyer"),
        ajax: {
            url: '/v1/administrative/provinces',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term // search term
                };
            },
            processResults: function(data) {
                console.log(data);
                apiResults = data.data.data.map(function(item) {
                    return {
                        text: item.province_name,
                        id: item.province_id
                    };
                });

                return {
                    results: apiResults
                };
            },
            cache: false
        },
    })
    $('#buyer-province').on('change', function(e) {
        var selectedValue = $(this).val();
        // var selectedText = $(this).find("option:selected").text();

        prov_id = selectedValue
        onSelectCity()
    });

    function onSelectCity() {
        $('#buyer-city').select2({
            dropdownParent: $("#addNewBuyer"),
            ajax: {
                url: '/v1/administrative/cities?province_id=' + prov_id,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.data.map(function(item) {
                        return {
                            text: item.city_name,
                            id: item.city_id
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: false
            },
        })
        $('#buyer-city').on('change', function(e) {
            var selectedValue = $(this).val();

            city_id = selectedValue
            onselectdistrict()
        });
    }
    
    function onselectdistrict() {
        $('#buyer-district').select2({
            dropdownParent: $("#addNewBuyer"),
            ajax: {
                url: '/v1/administrative/districts?city_id=' + city_id,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function(data) {
                    apiResults = data.data.data.map(function(item) {
                        return {
                            text: item.subdistrict_name,
                            id: item.subdistrict_id
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: false
            },
        })
        $('#buyer-district').on('change', function(e) {
            var selectedValue = $(this).val();
            dist_id = selectedValue
        });
    }

    function checkVoucher() {
        var voucher = $('#voucher').val();
        if (voucher == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Voucher tidak boleh kosong!'
            })
            return
        }
        var url = "{{ route('katalog-randu.check-voucher') }}";
        $.ajax({
            type: 'GET',
            url: url,
            data: {
                _token: "{{ csrf_token() }}",
                voucher: voucher
            }
        })
        .done(function(data) {
            if (data.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message
                })

                $('#diskonKatalogProduct').text(data.diskon);
                $('#totalKatalogProduct').text(data.total);
                $('#voucherResult').html(`<small class="form-text text-muted">Selamat kamu dapat potongan `+ data.diskon +`</small>`)
                $('#inputHiddenDiscount').html(`<input type="hidden" name="voucher" value="`+ voucher +`"> <input type="hidden" name="diskon" value="`+ data.diskon +`">`)
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message
                })
            }
        }).fail(function() {
            Swal.fire('Error!', 'An error occurred while checking the voucher.', 'error');
        })
    }

    function cekOngkir(method) {
        var postForm = {
            '_token': '{{ csrf_token() }}',
            'name'     : $('input[name=name]').val(),
            'city_id' : $('#buyer-city').val(),
            'courier' : method,
        };
        $.ajax({
            url: '{{ route("katalog-randu.cek-ongkir") }}',
            type: 'POST', 
            data : postForm,
            dataType  : 'json',
        })
        .done(function(data) {
            $('#ongkirKatalogProduct').text(data.ongkir)

            var costs = data.rajaongkir.results[0].costs;
            var view = ""
            if (costs.length === 0) {
                $('#ongkirNotSupport').attr('style', 'display: block !important');
            }else{
                $('#ongkirNotSupport').attr('style', 'display: none !important');
            }
            for(i = 0;i < costs.length;i++){
                var limit = costs[i].cost[0]
                var service = costs[i].service
                var harga = limit.value
                view += '<option value="'+harga+'">'+service +' Estimasi pada tanggal '+ limit.etd+'</option>'
            }
            $("#service_ongkir").html('<option value="" selected disabled>Pilih Service</option>'+view);
        })
        .fail(function() {
            alert('Load data failed.');
        });
    }

    $(document).on('change', '#service_ongkir', function() {
        var ongkir = formatCurrency($('#service_ongkir').val(), 'Rp. ');
        $('#ongkirKatalogProduct').text(ongkir);
        $('#inputHiddenOngkir').html(`<input type="hidden" name="ongkir" value="`+ $('#service_ongkir').val() +`">`)
        checkTotal();
    })

    function checkTotal() {
        var subtotal = unFormatCurrency($('#subtotalKatalogProduct').text(), 'Rp. ');
        var discount = unFormatCurrency($('#diskonKatalogProduct').text(), 'Rp. ');
        var ongkir = unFormatCurrency($('#ongkirKatalogProduct').text(), 'Rp. ');
        var pajak = unFormatCurrency($('#pajakKatalogProduct').text(), 'Rp. ');

        // console.log(subtotal, discount, ongkir, pajak);
        

        var total = parseInt(subtotal) + parseInt(ongkir) - parseInt(discount) + parseInt(pajak);
        var total = formatCurrency(total, 'Rp. ');
        $('#totalKatalogProduct').text(total);
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

    function unFormatCurrency(angka, prefix = 'Rp. ') {
        if (!angka) {
            return (prefix || '') + '-';
        }

        angka = angka.toString();
        return angka.replace(prefix, '').replace(/\./g, '').replace(/,/g, '.');
    }

    $(document).on('click', '#btnPlaceOrder', function() {
        if (this.prov_id == 0 || this.city_id == 0 || this.dist_id == 0) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Pilih alamat terlebih dahulu!'
            })
        }else{
            Swal.fire({
                title: 'Apakah Anda yakin ingin membeli produk ini?',
                text: "Kamu akan membeli produk ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, beli!'
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#formPlaceOrder')[0];
                    if (form.checkValidity()) {
                        form.submit();
                    } else {
                        form.reportValidity();
                    }
                }
            }).catch((error) => {
                console.log(error)
            })
        }
    });
    
</script>