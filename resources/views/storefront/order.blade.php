@extends('storefront.template.layouts.app')
@section('content')
<!-- Header -->
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="{{route('cart.index', $username)}}" class="back-btn">
						<i class="fa-solid fa-arrow-left"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">Checkout</h4>
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
        @if($cart)
		<div class="title-bar">
			<h5 class="mb-0">Data Pesanan</h5>
		</div>
        <div class="bill-detail">
			<ul class="p-1">
				<li>
                    <div class="row">
                        <li class="mb-2 pb-2">
                        <div class="row d-flex mb-3">

                            <div class="w-50">
                                <label class="radio-label btn btn-primary btn-square justify-content-start" style="width:100%">
                                    <input type="radio" name="order_type" id="dine_in" value="dine_in" {{ $order && $order['order_type'] == 'dine_in' ? 'checked' : '' }}>
                                    <span class="checkmark"></span> &nbsp; {{$store->template == "FNB" ? "Dine in" : "In Store"}}
                                </label>
                            </div>
                            @if($store->delivery == 1)
                            <div class="w-50">
                                <label class="radio-label btn btn-info btn-square justify-content-start" style="width:100%">
                                    <input type="radio" name="order_type" id="delivery" value="delivery" {{ $order && $order['order_type'] == 'delivery' ? 'checked' : '' }}>
                                    <span class="checkmark"></span> &nbsp; Delivery
                                </label>
                            </div>
                            @endif
                        </div>
                        @if($qr == '')
                        <div class="row d-none">
                            <label for="">Pilih Lokasi</label>
                            <select name="" id="branches" class="form-control">
                                <!-- <option value="">-- Pilih Lokasi --</option> -->
                                @foreach($branches as $branch)
                                    <option value="{{$branch->id}}">{{$branch->name}}</option>
                                @endforeach
                            </select>
                            @if($store->template == "FNB")
                            <label for="">Pilih Meja / Ruangan</label>
                            <select name="" id="qrTable" class="form-control">
                                <!-- <option value="">-- Pilih Lokasi Dahulu --</option> -->
                            </select>
                            @endif
                        </div>
                        @else
                        <div class="row">
                            <label for="">Pilih Meja / Ruangan</label>
                            <input type="text" class="form-control btn-square ml-2 bg-primary text-white" id="meja" name="meja" value="{{ $meja }}" readonly>
                        </div>
                        @endif
<div class="row">
    <label for="">Nama</label>
    <input type="text" class="form-control ml-2" id="customer_name" name="customer_name"
           value="{{ $order ? $order['customer_name'] : '' }}">

    <label for="">Nomor Telepon</label>
    <input
        type="text"
        class="form-control ml-2"
        id="phone_number"
        name="phone_number"
        value="{{ $order ? $order['phone_number'] : '' }}"
        onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
        maxlength="13"
    >

    <button class="btn btn-primary mt-3" id="save-customer-details">Lanjutkan Pemesanan</button>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const phoneInput = document.getElementById('phone_number');
        const nameInput = document.getElementById('customer_name');

        // Format nomor telepon
        phoneInput.addEventListener('focus', function () {
            if (phoneInput.value.trim() === '') {
                phoneInput.value = '62';
            }
        });

        phoneInput.addEventListener('input', function () {
            const val = phoneInput.value;
            if (val.startsWith('0')) {
                phoneInput.value = '62' + val.slice(1);
            }
        });

        phoneInput.addEventListener('paste', function (e) {
            let pasted = (e.clipboardData || window.clipboardData).getData('text');
            if (/\D/.test(pasted)) {
                e.preventDefault();
            }
        });

        // Auto kapital huruf pertama setiap kata nama
        nameInput.addEventListener('blur', function () {
            let val = nameInput.value.trim();
            if (val.length > 0) {
                nameInput.value = val
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                    .join(' ');
            }
        });
    });
</script>



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
	<!-- <div class="footer fixed">
		<div class="container">
			<a href="{{route('checkout.index', $username)}}" class="btn btn-primary btn-block">MAKE PAYMENT</a>
		</div>
	</div> -->
</div>
@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
<script>
$(document).ready(function() {
    $('#province').select2();
    $('#city').select2();
    $('#subdistrict').select2();
    $('input[name="order_type"]').on('change', function() {
        var orderType = $(this).val();
        updateOrderType(orderType);
    });

    $('#save-customer-details').on('click', function() {
        var customer = $('#customer_name').val();
        var phone = $('#phone_number').val();

        if(customer == ""){
            swal.fire('Oops...', 'Nama wajib diisi!', 'error');
            return
        }
        if(phone == ""){
            swal.fire('Oops...', 'Nomor Telepon wajib diisi!', 'error');
            return
        }
        var customerDetails = {
            _token: '{{ csrf_token() }}',
            customer_name: customer,
            phone_number: phone,
            qrTable: null,
            payment_type: '',
            branch_id: null,
        };
        @if($store->template == 'FNB')
         customerDetails.qrTable = $('#qrTable').find(":selected").val()
        @else
          customerDetails.branch_id = $('#branches').val();
        @endif
        updateCustomerDetails(customerDetails);
    });

    function updateOrderType(orderType) {
        $.ajax({
            url: '{{ route('order.updateOrderType', $username) }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                order_type: orderType
            },
            success: function(response) {
                if (response.success) {
                    console.log('Order type updated successfully to '+orderType);
                } else {
                    console.log('Failed to update order type.');
                }
            }
        });
    }

    function updateCustomerDetails(details) {
        $.ajax({
            url: '{{ route('order.updateCustomerDetails', $username) }}',
            type: 'POST',
            data: details,
            success: function(response) {
                if (response.success) {
                    if(response.orderType == "delivery"){
                        return window.location.href = "{{ route('checkout.delivery', $username)}}";
                    }else{
                        return window.location.href = "{{ route('checkout.index', $username)}}";
                    }
                } else {
                    console.log('Failed to update customer details.');
                }
            }
        });
    }
    $('#branches').on('change', function(){
        var branch = $(this).val();
        console.log("BRANCH", branch)
        if(branch != ''){
            $.ajax({
                url: "/{{$username}}/order/getQrTable/"+branch+"/{{$store->id}}",
                type: 'GET',
                // data: {
                //     '_token': '{{ csrf_token() }}'
                // },
                success: function(res) {
                    $('#qrTable').html('<option value="">-- Pilih Meja --</option>');
                    res.forEach((opt, i) => {
                        $('#qrTable').append(`
                            <option value="${res[i].id}">${res[i].no_meja}</option>
                        `)
                    })
                }
            });
        }
    }).change()

});
</script>
@endsection
