@extends('storefront.template.layouts.app')

@section('content')
<!-- Header -->
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="javascript:void(0);" class="back-btn">
						<i class="fa-solid fa-arrow-left"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">Order Confirmation</h4>
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
        <!-- <form action="{{route('checkout.process', $username)}}" method="post"> -->
		<div class="title-bar">
            <h5 class="mb-2">Halo {{$order['customer_name']}}</h5>
			<small class="mb-0">Select Payment Method</small>
		</div>

		<div class="accordion style-2 circle-radio" id="accordionExample">

		</div>
        <div class="bill-detail">
		    	<ul>
		    		<li>
                        <div class="row">
                            <div class="col-8">
                                <h6 class="mb-0">Total Belanja</h6>
                            </div>
                            <div class="col-1">
                                <span class="text-danger"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <span class="text-danger" id="cart-total">{{ number_format($totals['total'],0,',','.') }}</span>
                            </div>
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
    @if($cart)
	<div class="footer fixed">
		<div class="container">
			<button type="submit" class="btn btn-primary btn-block d-none" id="submitOrder">Checkout Order</button>
		</div>
	</div>
    @endif
    <!-- </form> -->
</div>
@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
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
});
</script>
@endsection
