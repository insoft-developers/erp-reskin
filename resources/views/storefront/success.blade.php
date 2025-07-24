@extends('storefront.template.layouts.app')

@section('content')
<!-- Header -->
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="/{{$username}}" class="back-btn">
						<i class="fa-solid fa-home"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">Payment Status</h4>
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
        @if($status)
            @if($order->status != "Pending")
                <div class="title-bar">
                    <h5 class="mb-2">Halo {{$order->customer->name}}</h5>
		        	<small class="mb-0">Pembayaranmu telah selesai</small>
		        </div>
                <div>
                        <h5 class="text-center">Pembayaran telah berhasil!</h5>
                </div>
            @else
            <div class="title-bar">
                    <h5 class="mb-2">Halo {{$order->customer->name}}</h5>
		        	<small class="mb-0">Pembayaranmu gagal</small>
		        </div>
                <div>
                        <h5 class="text-center">Pembayaran belum berhasil, silahkan ulangi beberapa saat lagi</h5>
                </div>
            @endif
        @else
            <div class="bill-detail text-center">
                <h4>Order tidak ditemukan...</h4>
                <p>Silahkan melanjutkan belanja</p>
            </div>
        @endif
	</div>
</div>
@endsection
@section('js')
<script>
$(document).ready(function() {
    $('input[name="payment_type"]').on('change', function() {
        var paymentType = $(this).val();
        var customerDetails = {
            _token: '{{ csrf_token() }}',
            payment_type: paymentType
        };
        $('#submitOrder').removeClass('d-none');
        $.ajax({
            url: '{{ route('order.updatePaymentDetails', $username) }}',
            type: 'POST',
            data: customerDetails,
            success: function(response) {
                if (response.success) {
                    console.log('Payment details updated successfully!', response);
                } else {
                    console.log('Failed to update payment details.');
                }
            }
        });
    });
    $('#tunai').on('click', function() {
        $('#tunai').addClass('bg-primary-subtle');
        $('#transfer').removeClass('bg-primary-subtle');
        $('#online').removeClass('bg-primary-subtle');
    })
    $('#transfer').on('click', function() {
        $('#tunai').removeClass('bg-primary-subtle');
        $('#transfer').addClass('bg-primary-subtle');
        $('#online').removeClass('bg-primary-subtle');
    })
    $('#online').on('click', function() {
        $('#tunai').removeClass('bg-primary-subtle');
        $('#transfer').removeClass('bg-primary-subtle');
        $('#online').addClass('bg-primary-subtle');
    })
    function updateCustomerDetails(details) {
        $.ajax({
            url: '{{ route('order.updateCustomerDetails', $username) }}',
            type: 'POST',
            data: details,
            success: function(response) {
                if (response.success) {
                    console.log('Customer details updated successfully!');
                } else {
                    console.log('Failed to update customer details.');
                }
            }
        });
    }
    $('#submitOrder').on('click', function(){
        var details = {
            _token: '{{ csrf_token() }}',
            username: "{{$username}}",
            customer_name: "{{$order['customer_name']}}",
            phone_number: "{{$order['phone_number']}}",
            order_type: "{{$order['order_type']}}",
            payment_type: "{{$order['payment_type']}}"
        };
        $.ajax({
            url: '{{ route('checkout.process', $username) }}',
            type: 'POST',
            data: details,
            success: function(response) {
                if (response.success) {
                    const order = response.orderid;
                    console.log(order)
                    return window.location.href = `/{{$username}}/order/confirmation/${order}`;
                } else {
                    console.log('Failed to update customer details.');
                }
            }
        });
    })
    $('#onlinePayment').on('click', function(){
        let order = $(this).data('order');
        return window.location.href = `/{{$username}}/order/payment/${order}`;
    })
});
</script>
@endsection
