@extends('master')
@section('style')
    <style>
        .theme-color-settings {
            display: flex;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
            justify-content: center;
            text-align: center;
        }

        .theme-color-settings li {
            margin: 10px;
            text-align: center;
            width: 60px;
            justify-content: center;
        }

        .theme-color-settings label {
            text-align: center;
            justify-content: center;
        }

        .theme-color-settings input[type="radio"] {
            display: none;
        }

        .theme-color-settings label {
            display: block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .theme-color-settings label:hover {
            border-color: #ccc;
        }

        .theme-color-settings input[type="radio"]:checked+label {
            border-color: #2f467a;
        }

        .select-template {
            padding: 10px
        }

        .select-template input[name="template"]:checked+label {
            border-color: #2f467a;
        }

        .theme-color-settings span {
            display: block;
            margin-top: 5px;
            font-size: 14px;
            color: #333;
        }

        /* Example color classes */
        #primary_color_10+label {
            background-color: #009688;
        }

        #primary_color_2+label {
            background-color: #4cd964;
        }

        #primary_color_3+label {
            background-color: #2196f3;
        }

        #primary_color_4+label {
            background-color: #ff9eb1;
        }

        #primary_color_5+label {
            background-color: #ffcc00;
        }

        #primary_color_6+label {
            background-color: #ff9500;
        }

        #primary_color_7+label {
            background-color: #9c27b0;
        }

        #primary_color_1+label {
            background-color: #ff3b30;
        }

        #primary_color_9+label {
            background-color: #5ac8fa;
        }

        #primary_color_11+label {
            background-color: #cddc39;
        }

        #primary_color_12+label {
            background-color: #dd4a01;
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
                        <li class="breadcrumb-item"><a href="{{ url('setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pengaturan Storefront</li>
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

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Storefront</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{ route('storefront-save') }}">
                                    @csrf
                                    <div class="accordion" id="accordionPanelsStayOpenExample">
                                        @include('storefront.settings.general')
                                        @include('storefront.settings.appearance')
                                        @include('storefront.settings.shipping')
                                        @include('storefront.settings.lainnya')
                                    </div>

                                    <div class="container">
                                        <div class="row d-flex justify-content-center align-items-center">
                                            <div class="col-4"></div>
                                            <div class="col-4">
                                                <button type="button" class="btn btn-primary w-100"
                                                    id="simpanData">Simpan</button>
                                            </div>
                                            <div class="col-4"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>
@endsection
@section('js')
    <!-- @include('storefront.paymentData') -->
    @include('storefront.shippingData')
    <script>
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        let template = `{{ $setting ? ($setting->template ? $setting->template : 'FNB') : '' }}`;
        let theme_color = `{{ $setting ? ($setting->theme_color ? $setting->theme_color : 'color-teal') : '' }}`;
        // let pay = '{{ $payment }}';
        // let payment = null;
        let address = null;
        let province_id = `{{ $info ? ($info->province_id ? $info->province_id : '') : '' }}`;
        let province_name = `{{ $info ? ($info->province_name ? $info->province_name : '') : '' }}`;
        let city_id = `{{ $info ? ($info->city_id ? $info->city_id : '') : '' }}`;
        let city_name = `{{ $info ? ($info->city_name ? $info->city_name : '') : '' }}`;
        let subdistrict_id = `{{ $info ? ($info->subdistrict_id ? $info->subdistrict_id : '') : '' }}`;
        let subdistrict_name = `{{ $info ? ($info->subdistrict_name ? $info->subdistrict_name : '') : '' }}`;
        let delivery = `{{ $setting ? ($setting->delivery ? $setting->delivery : 0) : 0 }}`;
        let checkout_whatsapp = `{{ $setting ? ($setting->checkout_whatsapp ? $setting->checkout_whatsapp : 0) : 0 }}`;
        let template_order_info =
            `{{ $setting ? ($setting->template_order_info ? $setting->template_order_info : '') : '' }}`;
        let whatsapp_number =
            `{{ $setting ? ($setting->whatsapp_number ? $setting->whatsapp_number : '') : '' }}`;

        // if(pay){
        //     payment = JSON.parse(atob(pay));
        // }

        console.log(checkout_whatsapp);

        if (checkout_whatsapp == 1) {
            $('#checkout-whatsapp').prop('checked', true);
            $("#template-order-info").removeAttr('disabled');
            $("#whatsapp_number").removeAttr('disabled');
        } else {

            $('#checkout-whatsapp').prop('checked', false);
            $("#template-order-info").attr('disabled', true);
            $("#whatsapp_number").attr('disabled', true);
        }



        let ship = '{{ $shipping }}';
        let shipping = null;
        if (ship) {
            shipping = JSON.parse(atob(ship));
        }
        if (shipping != null) {
            shippingData = shipping;
        }

        $(document).ready(function() {
            $('#address').bind('input propertychange', function() {
                var addr = $(this).val();
                address = addr;
            });
            if (province_id != '') {
                $.ajax({
                    url: "/storefront/getCity/" + province_id,
                    type: 'GET',
                    beforeSend: () => {
                        $('#cities').empty().append('<option value="">Memuat data kota...</option>');
                    },
                    success: function(res) {
                        $('#cities').html('<option value="">-- Pilih Kabupaten/Kota --</option>');
                        res.forEach((opt, i) => {
                            $('#cities').append(`
                            <option value="${res[i].city_id}" ${res[i].city_id == city_id ? 'selected' : ''} >${res[i].city_name}</option>
                        `)
                        })
                    }
                });
            }
            $('#provinces').select2();
            $('#provinces').on('change', function() {
                province_id = $(this).val();
                var prov_name = $('#provinces option:selected').text();
                if (province_id != '') {
                    $.ajax({
                        url: "/storefront/getCity/" + province_id,
                        type: 'GET',
                        beforeSend: () => {
                            $('#cities').empty().append(
                                '<option value="">Memuat data kota...</option>');
                        },
                        success: function(res) {
                            $('#cities').html(
                                '<option value="">-- Pilih Kabupaten/Kota --</option>');
                            res.forEach((opt, i) => {

                                $('#cities').append(`
                                <option value="${res[i].city_id}" ${res[i].city_id == city_id ? 'selected' : ''} >${res[i].city_name}</option>
                            `)
                            })
                        }
                    });
                }
            });
            if (city_id != '') {
                $.ajax({
                    url: "/storefront/getSubdistrict/" + city_id,
                    type: 'GET',
                    beforeSend: () => {
                        $('#subdistricts').empty().append(
                            '<option value="">Memuat data kecamatan...</option>');
                    },
                    success: function(res) {
                        $('#subdistricts').html('<option value="">-- Pilih Kecamatan --</option>')
                        console.log("subdist", res)
                        res.forEach((opt, i) => {
                            $('#subdistricts').append(`
                            <option value="${res[i].subdistrict_id}" ${res[i].subdistrict_id == subdistrict_id ? 'selected' : ''} >${res[i].subdistrict_name}</option>
                        `)
                        })
                    }
                });
            }
            $('#cities').select2();
            $('#cities').on('change', function() {
                city_id = $(this).val();
                var cityname = $('#cities option:selected').text();
                if (city_id != '') {
                    $.ajax({
                        url: "/storefront/getSubdistrict/" + city_id,
                        type: 'GET',
                        beforeSend: () => {
                            $('#subdistricts').empty().append(
                                '<option value="">Memuat data kecamatan...</option>');
                        },
                        success: function(res) {
                            $('#subdistricts').html(
                                '<option value="">-- Pilih Kecamatan --</option>')
                            console.log("subdist", res)
                            res.forEach((opt, i) => {
                                $('#subdistricts').append(`
                                <option value="${res[i].subdistrict_id}" ${res[i].subdistrict_id == subdistrict_id ? 'selected' : ''} >${res[i].subdistrict_name}</option>
                            `)
                            })
                        }
                    });
                }
            });
            $('#subdistricts').select2();
            $('#subdistricts').on('change', function() {
                var cityname = $('#cities option:selected').text();
                city_name = cityname;
                var sub = $(this).val();
                var subname = $('#subdistricts option:selected').text();
                subdistrict_id = sub
                subdistrict_name = subname
            })
            $('#username').on('input', function() {
                var username = $(this).val();
                if (username != '') {
                    $.ajax({
                        url: "{{ route('storefront-username-check') }}",
                        type: 'POST',
                        data: {
                            '_token': csrf_token,
                            username: username,
                            id: "{{ session('id') }}"
                        },
                        success: function(response) {
                            if (response === 'available') {
                                $('#username-status').text('Username is available').removeClass(
                                    'text-danger').addClass('text-success');
                            } else {
                                $('#username-status').text('Username is taken').removeClass(
                                    'text-success').addClass('text-danger');
                            }
                        }
                    });
                } else {
                    $('#username-status').text('Username is required').removeClass('text-success').addClass(
                        'text-danger');
                }
            })

            function updateShippingData() {
                $('#shippings .shipping').each(function() {
                    let method = $(this).data('method');
                    let methodCheckbox = $(this).find('.form-check-input');
                    let methodData = shippingData.find(m => m.method === method);

                    methodData.selected = methodCheckbox.is(':checked');
                });

                $('#jsonOutputShipping').text(JSON.stringify(shippingData, null, 2));
            }

            // $('#template').on('change', function(){
            //     template = $('#template').val();
            // })
            $('input[name="template"]').on('change', function() {
                template = $(this).val();
                console.log('template', template)
            });
            $('input[name="theme_color"]').on('change', function() {
                theme_color = $(this).val();
                console.log('theme_color', theme_color)
            });

            $('#shippings .form-check-input').on('change', function() {
                updateShippingData();
            });

            function updateJsonShippingDisplay() {
                $('#jsonOutputShipping').text(JSON.stringify(shippingData, null, 2));
            }
            $('#Transfer').on('change', function() {
                if ($('#Transfer').prop('checked')) {
                    $('#banks').removeClass('d-none');

                } else {
                    $('#banks').addClass('d-none');
                }
            });

            // updateJsonDisplay();
            updateJsonShippingDisplay();
            $('#delivery').change(function() {

                if (this.checked) {
                    delivery = 1;
                    $('#shippingOption').show()
                } else {
                    delivery = 0
                    $('#shippingOption').hide()
                }
            })
            let checkedDelivery = $('#delivery').is(":checked")
            if (checkedDelivery) {
                delivery = 1;
                $('#shippingOption').show()
            } else {
                delivery = 0
                $('#shippingOption').hide()
            }


            $('#checkout-whatsapp').change(function() {

                if (this.checked) {
                    checkout_whatsapp = 1;
                    $("#template-order-info").removeAttr('disabled');
                    $("#whatsapp_number").removeAttr('disabled');
                    
                    
                } else {
                    checkout_whatsapp = 0
                    $("#template-order-info").attr('disabled', true);
                    $("#whatsapp_number").attr('disabled', true);
                }
            })




            $('#simpanData').on('click', function() {
                console.log("SIMPAN DATA")
                let username = $('#username').val();
                let address = $('textarea#address').val();
                var formData = new FormData();
                var img1 = $('#bannerImage1')[0].files[0];
                var link1 = $('#bannerLink1').val();
                var img2 = $('#bannerImage2')[0].files[0];
                var link2 = $('#bannerLink2').val();
                var img3 = $('#bannerImage3')[0].files[0];
                var link3 = $('#bannerLink3').val();
                var template_order_info = $("#template-order-info").val();
                var whatsapp_number = $("#whatsapp_number").val();
                if (img1) formData.append('img1', img1);
                if (img2) formData.append('img2', img2);
                if (img3) formData.append('img3', img3);
                formData.append('img_link1', link1);
                formData.append('img_link2', link2);
                formData.append('img_link3', link3);
                formData.append('username', username);
                formData.append('address', address);
                formData.append('province_id', province_id);
                formData.append('province_name', province_name);
                formData.append('city_id', city_id);
                formData.append('city_name', city_name);
                formData.append('subdistrict_id', subdistrict_id);
                formData.append('subdistrict_name', subdistrict_name);
                formData.append('template', template);
                formData.append('theme_color', theme_color);
                formData.append('delivery', delivery);
                formData.append('shippingData', JSON.stringify(shippingData));
                formData.append('template_order_info', template_order_info);
                formData.append('checkout_whatsapp', checkout_whatsapp);
                formData.append('whatsapp_number', whatsapp_number);
                formData.append('_token', csrf_token);
                if (address == "" || province_id == "" || city_id == "" || subdistrict_id == "") {
                    swal.fire('Oops...', 'Harap isi alamat dengan lengkap', 'error');
                    return
                } else {

                    try {
                        $.ajax({
                                url: "{{ route('storefront-save') }}",
                                type: 'POST',
                                data: formData,
                                contentType: false,
                                processData: false,
                            })
                            .done(function(res) {
                                swal.fire(res.title, res.text, res.icon).then(() => {
                                    location.reload();
                                })
                            })
                            .fail(function() {
                                swal.fire('Oops...', 'Something went wrong with data !', 'error');
                            });
                    } catch (err) {
                        // swal.fire('Oops...', 'error', 'error');
                        console.log(err)
                    }
                }
            })

        });
        $(document).ready(function() {
            $('#template').val(template);

            shippingData.forEach(item => {
                let checkbox1 = $(`#${item.method}`);
                if (item.selected == true) {
                    checkbox1.prop('checked', true);
                }
            });
            console.log("SHIPPING", shippingData)
            if ($('#Transfer').prop('checked')) {
                $('#banks').removeClass('d-none');

            } else {
                $('#banks').addClass('d-none');
            }
            $('#jsonOutputShipping').text(JSON.stringify(shippingData, null, 2));
        })

        // Initialize checkboxes on page load
        //document.addEventListener('DOMContentLoaded', initializeCheckboxes);
        // $(document).ready(initializeCheckboxes);
    </script>
@endsection
