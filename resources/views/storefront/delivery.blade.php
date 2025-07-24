@extends('storefront.template.layouts.app')
@section('style')
<style>
/* Change font size for the selected option */
.select2-container .select2-selection--single .select2-selection__rendered {
    font-size: 16px;
}

/* Change font size for the dropdown options */
.select2-container--default .select2-results__option {
    font-size: 16px;
}

/* Set the height for the select2 input */
.select2-container .select2-selection--single {
    height: 40px; /* Adjust height as needed */
}

/* Ensure the selected option text is vertically centered */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 40px; /* Match this to the height */
    padding-left: 10px; /* Optional: adjust padding if needed */
}

/* Set the height for the dropdown options (optional) */
.select2-container--default .select2-results__option {
    height: 40px; /* Adjust height for dropdown items */
    line-height: 40px; /* Ensure the text is vertically aligned */
}

</style>
@endsection
@section('content')
<!-- Header -->
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="{{route('order.create', $username)}}" class="back-btn">
						<i class="fa-solid fa-arrow-left"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">Pengiriman</h4>
				</div>
				<div class="mid-content"></div>
				<div class="right-content"></div>
			</div>
		</div>
	</div>
</header>
<!-- Header -->

<!-- Page Content -->
<div class="page-content">
	<div class="container fb">
        @php
            $kurir = "";
            $shippings = json_decode($store->shipping, true);
            foreach($shippings as $shipping){
                if($shipping['selected'] == "true"){
                    if($kurir == ""){
                        $kurir .= strtolower($shipping['method']);
                    }else{
                        $kurir .= ":".strtolower($shipping['method']);
                    }
                }
            }
        @endphp
        @if($cart)
		<!-- <div class="title-bar">
			<h5 class="mb-0">Delivery Data</h5>
		</div> -->
        <div class="bill-detail">
			<ul class="p-1">
				<li>
                    <div class="row">

                    <li class="mb-2 pb-2">
                        @if($order['order_type'] == 'delivery')
                        <div class="row" id="customerAddress">
                            <div class="col-12 mb-3">
                                <h5>Tujuan Pengiriman:</h5>
<label for="address">Alamat</label>
<textarea class="form-control" id="address" rows="4">{{ @$order['cust_alamat'] }}</textarea>
<span id="addressAlert" class="text-danger" style="display:none"><small>Alamat harap diisi dengan lengkap Cth: Perumahan Singomenggolo. Jalan Sapudi No 4, Kelurahan Gubeng</small></span>

                            </div>
                            <div class="col-12 mb-3">
                                <label for="">Provinsi</label>
                                <select name="province" id="province" class="js-example-basic-single form-control-lg">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach($provinces as $province)
                                    <option value="{{$province['province_id']}}" {{@$order['cust_provinsi_id'] == $province['province_id'] ? 'selected' : ''}}>{{$province['province']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="">Kabupaten / Kota</label>
                                <select name="city" id="city" class="js-example-basic-single form-control-lg">
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="">Kecamatan</label>
                                <select name="subdistrict" id="subdistrict" class="js-example-basic-single form-control-lg">
                                </select>
                            </div>
                                <div class="divider divider-dotted border-light"></div>
                            </div>
                            <div class="col-12">
                                <div id="shippingLoading"></div>
                                <div class="accordion accordion-primary" id="shippingCalculate">
							    </div>
                            </div>
                            <!-- <button class="btn btn-primary mt-3" id="save-customer-details">Simpan Data</button> -->
                        </div>
                        @endif

		    		</li>
                    </div>
				</li>
			</ul>
		</div>
        @else
            <div class="bill-detail text-center">
                <h4>Keranjang kosong...</h4>
                <p>Silahkan melanjutkan belanja</p>
            </div>
        @endif
	</div>
	<!-- FOOTER -->
	<div class="footer fixed text-left">
		<div class="container">
			<button class="btn btn-primary btn-block text-left" id="save-customer-details">
                <span class="mb-0 font-14 text-white">Total <i class="font-14 fa-solid fa-rupiah-sign"></i><span id="footer-total">{{number_format($totals['total']-$totals['shipping'],0,',','.')}}</span></span>
                <span class="mb-0 font-14 text-white" style="display:none" id="footer-shipping"> + <i class="font-14 fa-solid fa-rupiah-sign"></i><span id="footer-shipping-total">0</span></span>
                <span class="text-right"> | Bayar</span>
            </button>
		</div>
	</div>
</div>
@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
<script>
$(document).ready(function() {
    $('#province').select2();
    $('#city').select2();
    $('#subdistrict').select2();
    let province_id = `{{$order ? @$order["cust_provinsi_id"] ? $order["cust_provinsi_id"] : '' : ''}}`;
    let city_id = `{{$order ? @$order["cust_kota_id"] ? $order["cust_kota_id"] : '' : ''}}`;
    let subdistrict_id = `{{$order ? @$order["cust_kecamatan_id"] ? $order["cust_kecamatan_id"] : '' : ''}}`;
    let shipping_service = `{{$order ? @$order["shipping_service"] ? $order["shipping_service"] : '' : ''}}`;
    let shipping_code = `{{$order ? @$order["shipping_code"] ? $order["shipping_code"] : '' : ''}}`;
    if(shipping_service == ''){
        $('#save-customer-details').prop('disabled', true);
    }else{
        $('#save-customer-details').prop('disabled', false);
    }
    $('#save-customer-details').on('click', function() {
        return window.location.href = "{{ route('checkout.index', $username)}}";
    });

    function updateCustomerDetails(details) {
        $.ajax({
            url: '{{ route('order.updateCustomerDetails', $username) }}',
            type: 'POST',
            data: details,
            success: function(response) {
                if (response.success) {

                } else {
                    console.log('Failed to update customer details.');
                }
            }
        });
    }
    $('#address').on('change', function(){
        $('#addressAlert').hide();
    })
    if(province_id != ''){
            // Kosongkan select city dan subdistrict saat provinsi berubah
            // $('#subdistrict').empty().append('<option value="">Pilih Kecamatan</option>');
            $.ajax({
                url: "/storefront/getCity/"+province_id,
                type: 'GET',
                beforeSend: () => {
                    $('#city').empty().append('<option value="">Memuat data kota...</option>');
                },
                success: function(res) {
                    console.log("RESPONSE CITY", res)
                    $('#city').empty().append('<option value="">Pilih Kota</option>');
                    res.forEach((opt, i) => {
                        $('#city').append(`
                            <option value="${opt.city_id}" ${city_id == opt.city_id ? 'selected' : ''} >${opt.city_name}</option>
                        `)
                    })
                }
            });
        }
    $('#province').on('change', function(){
        var address = $('#address').val()
        if(address == ""){
            $('#addressAlert').show();
            $('#province').val('');
            return
        }
        var province = $(this).val();
        console.log("PROVINCE", province)
        if(province != ''){
            // Kosongkan select city dan subdistrict saat provinsi berubah
            // $('#subdistrict').empty().append('<option value="">Pilih Kecamatan</option>');
            $.ajax({
                url: "/storefront/getCity/"+province,
                type: 'GET',
                beforeSend: () => {
                    $('#city').empty().append('<option value="">Memuat data kota...</option>');
                },
                success: function(res) {
                    console.log("RESPONSE CITY", res)
                    $('#city').empty().append('<option value="">Pilih Kota</option>');
                    res.forEach((opt, i) => {
                        $('#city').append(`
                            <option value="${opt.city_id}" ${city_id == opt.city_id ? 'selected' : ''} >${opt.city_name}</option>
                        `)
                    })
                }
            });
        }
    });
    if(city_id != ''){
        $.ajax({
            url: "/storefront/getSubdistrict/"+city_id,
            type: 'GET',
            beforeSend: () => {
                $('#subdistrict').empty().append('<option value="">Memuat data kecamatan...</option>');
            },
            success: function(res) {
                $('#subdistrict').empty().append('<option value="">Pilih Kecamatan</option>');
                res.forEach((opt, i) => {
                    $('#subdistrict').append(`
                        <option value="${opt.subdistrict_id}" ${opt.subdistrict_id == subdistrict_id ? 'selected' : ''}>${opt.subdistrict_name}</option>
                    `)
                })
                if(subdistrict_id != ''){
                    $('#subdistrict').change();
                }
            }
        });
    }
    $('#city').on('change', function(){
        var city = $(this).val();
        console.log("CITY", city)
        if(city != null){
            $.ajax({
                url: "/storefront/getSubdistrict/"+city,
                type: 'GET',
                beforeSend: () => {
                    $('#subdistrict').empty().append('<option value="">Memuat data kecamatan...</option>');
                },
                success: function(res) {
                    $('#subdistrict').empty().append('<option value="">Pilih Kecamatan</option>');
                    res.forEach((opt, i) => {
                        $('#subdistrict').append(`
                            <option value="${opt.subdistrict_id}" ${opt.subdistrict_id == subdistrict_id ? 'selected' : ''}>${opt.subdistrict_name}</option>
                        `)
                    })
                }
            });
        }
    })
    $('#subdistrict').on('change', function(){
        var sub = $(this).val();
        $.ajax({
            url: "/storefront/getShippingCost",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                origin: "{{$store->subdistrict_id}}",
                originType: "subdistrict",
                destination: sub,
                destinationType: "subdistrict",
                weight: "{{$totals['weight']}}",
                courier: "{{$kurir}}"
            },
            beforeSend: () => {
                $('#shippingLoading').append('<div>Memuat data kurir...</div>');
            },
            success: function(res) {
                $('#shippingLoading').html('');
                $('#shippingCalculate').html('');
                res.forEach((opt, i)=>{
                    let cost = opt.costs
                    let service = "";
                    cost.forEach((data, index)=>{
                        service = service + `
                            <label class="radio-label">
                                <input type="radio" name="kurir" data-kurir-code="${opt.code}" data-kurir-name="${opt.name}" data-kurir-service="${data.service}" value="${data.cost[0].value}" ${shipping_service == data.service ? 'checked' : ''}>
                                <span class="checkmark"></span>
                                <p style="font-weight:700">${data.service} (Rp${data.cost[0].value.toLocaleString('id-ID')})</p>
                                <small style="margin-top:-20px">${data.description} Estimasi: ${data.cost[0].etd.replace('HARI', '')} Hari</small>
                            </label>`
                    })
                    $('#shippingCalculate').append(`
                        <div class="accordion-item">
							<div class="accordion-header ${shipping_code == opt.code ? '' : 'collapsed'}" id="heading${i}" data-bs-toggle="collapse" data-bs-target="#collapse${i}" aria-controls="collapse${i}" aria-expanded="true" role="button">
							    <span class="accordion-header-icon"></span>
							    <span class="accordion-header-text">${opt.name}</span>
							    <span class="accordion-header-indicator"></span>
							</div>
							<div id="collapse${i}" class="collapse ${shipping_code == opt.code ? 'show' : ''}" aria-labelledby="heading${i}" data-bs-parent="#shippingCalculate" style="">
							    <div class="accordion-body-text">
								    <div class="radio circle-radio">${service}</div>
							    </div>
							</div>
						</div>
                    `)
                })
                // $('#shippingCalculate').show()
            },
            error: function(xhr, status, error) {
                $('#shippingLoading').html('<div class="text-danger">'+xhr.responseJSON.message+'</div>');
            }
        });
    })
    $('#shippingCalculate').on('change', '.circle-radio input[type="radio"]', function() {
        const code = $(this).data('kurir-code');
        const service = $(this).data('kurir-service');
        const cost = $(this).val();
        console.log(code, service, cost)

        if(cost != null){
            $.ajax({
                url: "/{{$username}}/order/calculate-final",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    address: $('#address').val(),
                    province: $('#province option:selected').text(),
                    province_id: $('#province').val(),
                    city: $('#city option:selected').text(),
                    city_id: $('#city').val(),
                    subdistrict: $('#subdistrict option:selected').text(),
                    subdistrict_code: $('#subdistrict').val(),
                    code: code,
                    service: service,
                    cost: cost,
                    username: '{{$username}}'
                },
                success: function(res) {
                    if(res.success){
                        $('#footer-shipping').show();
                        $('#footer-shipping-total').text(res.totals.shipping.toLocaleString('id-ID'));
                        $('#save-customer-details').prop('disabled', false);
                    }
                }
            });
        }
    })
});
</script>
@endsection
