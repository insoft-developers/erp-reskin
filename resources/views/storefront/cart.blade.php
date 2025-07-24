@extends('storefront.template.layouts.app')

@section('content')
<header class="header">
		<div class="main-bar">
			<div class="container">
				<div class="header-content">
					<div class="left-content">
						<a href="/{{$username}}" class="back-btn" data-quantity="{{$totalQuantity}}">
							<i class="fa-solid fa-arrow-left"></i>
						</a>
						<h4 class="title mb-0 text-nowrap"> Keranjang Belanja</h4>
					</div>
					<div class="mid-content"></div>
					<div class="right-content"></div>
				</div>
			</div>
		</div>
	</header>
<div class="page-content">
    <div class="container fb" style="margin-bottom: 100px;">
        @if($cart)
            @foreach($cart as $index => $product)
            
		    <div class="product-list-cart" data-product-id="{{ $index }}">
		    	<div class="dz-content row">
                    <div class="col-8">
                        <h4 class="item-name">
                            <a href="#">
                                {{$product['name']}}
                            </a>
                        </h4>
                        <div class="price-wrapper">
                            <h6 class="current-price"><i class="fa-solid fa-rupiah-sign"></i>{{number_format($product['price'],0,',','.')}}</h6>
                            <!-- <span class="old-price"><i class="fa-solid fa-rupiah-sign"></i>1100</span> -->
                        </div>
                        @if(isset($product['variants']))
                        <div class="divider divider-dotted border-light"></div>
                        @foreach ($product['variants'] as $groupName => $variants)
                        <div class="variant-group" id="group-{{ $loop->index + 1 }}" >
                                <h5 class="mt-3">{{$groupName}}</h5>
                                @foreach($variants['data'] as $variant)
                                <label class="row">

                                        <small class="col-6">{{ $variant['name'] }}</small>

                                        <small class="col-6 text-end">+ Rp{{ number_format($variant['price'],0,',','.') }}</small>

                                </label>
                                @endforeach
                                <label for="" class="text-light">Catatan: {{$variants['notes']}}</label>

                        </div>
                        @endforeach
                        <div class="divider divider-dotted border-light"></div>
                        @endif
                    </div>
                    <div class="col-4">
                        <a href="javascript:void(0);" class="dz-media media-100">
                        @php 
                        $image = \App\Models\ProductImages::where('product_id',$product['id'])->first();
                        @endphp
                        @if($image)
				            <img class="rounded-sm" src="{{ Storage::url('images/product/' . $image->url) }}" alt="image">
                        @else
				            <img class="rounded-sm" src="{{ asset('template/main/images/product-placeholder.png') }}" alt="image">
                        @endif
                        </a>
                    </div>
		    	</div>
                <div class="row mt-4">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-clipboard"></i></span>
                        <input type="text" class="form-control product-note" placeholder="Catatan" aria-label="Username" aria-describedby="basic-addon1" data-product-id="{{ $index }}" value="{{$product['notes']}}">
                    </div>
                </div>
		    	<div class="row">
                    <div class="col-6">
                        <div class="dz-stepper style-5 border-1 rounded-stepper">
                            <input readonly="" class="stepper form-control update-quantity" type="text" value="{{ $product['quantity'] }}" name="demo3" data-product-id="{{ $index }}">
                        </div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="remove-from-cart text-danger m-3" data-product-id="{{ $index }}">
                            <i class="fa-solid fa-trash"></i>
                        </div>
                    </div>
		    	</div>
		    </div>
            @endforeach

            <div id="voucherApplied" style="display:none">
                    <div class="alert alert-primary light alert-dismissible fade show mb-2">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        Kamu menggunakan voucher <strong id="voucherDisplay">{{@$order['voucher']['voucher_code']}}</strong>
                        <button class="btn-close" id="removeVoucher">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
            </div>

            <div class="card" id="voucherInput">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between col-12">
                        <input type="text" class="form-control" placeholder="Masukan kode voucher..." id="voucher">&nbsp;
                        <div class="p-l10"><button class="btn btn-primary" id="applyVoucher">Apply</button></div>
                    </div>
                    <span class="text-danger" id="voucherNotFound"></span>
                </div>
            </div>

            @if($cart)
		    <div class="bill-detail mt-3">
		    	<h6>Detail Pembayaran</h6>
		    	<ul>
		    		<li>
                        <div class="row">
                            <div class="col-7">
                                <span>Sub Total</span>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-1">
                                <span class="price"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <span class="price" id="cart-subtotal">{{ number_format($totals['subtotal'],0,',','.') }}</span>
                            </div>
                        </div>
		    		</li>
                    <li id="diskonVoucher">
                        <div class="row">
                            <div class="col-7">
                                <span>Diskon</span>
                            </div>
                            <div class="col-1">-</div>
                            <div class="col-1">
                                <span class="price"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <input type="hidden" name="" id="voucher-amount">
                                <span class="price" id="diskon-voucher">{{ number_format(@$order['voucher']['voucher_amount'],0,',','.') }}</span>
                            </div>
                        </div>
		    		</li>
                    <li>
                        <div class="row">
                            <div class="col-7">
                                <span>Pajak</span>
                            </div>
                            <div class="col-1">+</div>
                            <div class="col-1">
                                <span class="price"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <span class="price" id="tax">{{ number_format(0,0,',','.') }}</span>
                            </div>
                        </div>
		    		</li>
                    <li>
                        <div class="row">
                            <div class="col-7">
                                <span>Biaya</span>
                            </div>
                            <div class="col-1">+</div>
                            <div class="col-1">
                                <span class="price"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <span class="price" id="cart-subtotal">0</span>
                            </div>
                        </div>
		    		</li>

		    		<li>
                        <div class="row">
                            <div class="col-7">
                                <h6 class="mb-0 text-bold">Total</h6>
                            </div>
                            <div class="col-1"></div>

                            <div class="col-1">
                                <span class="text-danger"><i class="fa-solid fa-rupiah-sign"></i></span>
                            </div>
                            <div class="col-2">
                                <input type="hidden" name="" id="total">
                                <span class="text-danger" id="cart-total">{{ number_format($totals['total']-$totals['shipping'],0,',','.') }}</span>
                            </div>
                        </div>
		    		</li>
		    	</ul>
		    </div>
            @endif
        @else
            <div class="bill-detail text-center">
                <h4>Keranjang kosong...</h4>
                <p>Silahkan melanjutkan belanja</p>
            </div>
        @endif
	</div>
</div>
@if($cart)
<div class="footer fixed">
	<div class="container">
		<a href="{{route('order.create', $username)}}" class="payment-btn rounded-sm {{ $storeFront->template == 'FNB' ? 'btn' : '' }}">
			<div class="total-price">
				<span id="cart-count">{{ $totalQuantity }} </span>&nbsp;<span> items</span>
				<div class="mide-line"></div>
				<span class="mb-0 font-14 text-white"><i class="font-14 fa-solid fa-rupiah-sign"></i><span id="footer-total">{{number_format($totals['total']-@$order['voucher']['voucher_amount'],0,',','.')}}</span></span>
			</div>
			<div class="d-flex align-items-center">
				<span class="mb-0 title">Checkout</span>
				<svg class="ms-2" width="10" height="18" viewBox="0 0 10 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M1.25005 17.25C1.05823 17.25 0.866234 17.1767 0.719797 17.0303C0.426734 16.7372 0.426734 16.2626 0.719797 15.9698L7.68955 9.00001L0.719797 2.03025C0.426734 1.73719 0.426734 1.26263 0.719797 0.969751C1.01286 0.676875 1.48742 0.676688 1.7803 0.969751L9.2803 8.46976C9.57336 8.76282 9.57336 9.23738 9.2803 9.53026L1.7803 17.0303C1.63386 17.1767 1.44186 17.25 1.25005 17.25Z" fill="#7D8FAB"></path>
				</svg>
			</div>
		</a>
	</div>
</div>
@endif
@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
<script>
$(document).ready(function() {
    $.ajax({
        url: '{{ route('cart.data', $username) }}',
        method: 'GET',
        success: function(response) {
            updateCartDisplay(response);
        }
    });
    $.ajax({
        url: '{{ route('cart.checkVoucher', $username) }}',
        method: 'GET',
        success: function(res) {
            if(res.success){
                var shipping = parseFloat(res.totals.shipping)
                var total = parseFloat(res.totals.total)-shipping
                var discount = parseFloat(res.totals.discount)
                $('#voucherInput').hide();
                $('#voucherApplied').show();
                $('#voucherDisplay').text(res.voucher.voucher_code);
                $('#diskon-voucher').text(discount.toLocaleString('id-ID'));
                updateCartDisplay(res);
            }
        }
    });
    $('.back-btn-cart').on('click',function(){
        var qty = $(this).data('quantity');
        if(qty == 0){
            window.location = "/{{$username}}";

        }else{
            if ('referrer' in document) {
                window.location = document.referrer;
                /* OR */
                //location.replace(document.referrer);
            } else {
                window.history.back();
            }
        }
	})
    function updateCartDisplay(data) {
        const totals = data.totals;
        var subtotal = parseFloat(totals.subtotal);
        var shipping = parseFloat(totals.shipping);
        var total = parseFloat(totals.total)-shipping;

        $('#cart-count').text(totals.totalQuantity);
        $('#cart-subtotal').text(subtotal.toLocaleString('id-ID'));
        $('#tax').text(totals.tax);
        $('#total').val(total);
        $('#cart-total').text(total.toLocaleString('id-ID'));
        $('#footer-total').text(total.toLocaleString('id-ID'));
    }

    $('.update-quantity').change(function() {
        var input = $(this);
        var productId = input.data('product-id');
        var quantity = parseFloat(input.val());
        // var variant = input.data('product-varian');

        $.ajax({
            url: '{{ route('cart.updateQuantity', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ productId: productId, quantity: quantity, username: '{{$username}}' }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("UPDATE", response.totals);
                if(quantity == 0){
                    $('div[data-product-id="' + productId + '"]').remove();
                }
                updateCartDisplay(response);
            }
        });
    });

    $('.remove-from-cart').click(function() {
        console.log("REMOVE")
        var button = $(this);
        var productId = button.data('product-id');

        $.ajax({
            url: '{{ route('cart.remove', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ productId: productId, username: '{{$username}}' }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'Product removed from cart') {
                    $('div[data-product-id="' + productId + '"]').remove();
                    updateCartDisplay(response);
                    if(response.totalQuantity == 0){
                        location.reload();
                    }
                    console.log("TOTAL",response)
                }
            }
        });
    });

    $('.product-note').change(function() {
        var input = $(this);
        var productId = input.data('product-id');
        var notes = input.val();

        $.ajax({
            url: '{{ route('cart.updateNotes', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ productId: productId, notes: notes, username: '{{$username}}' }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("UPDATE", response.totals);
                updateCartDisplay(response.totals);
            }
        });
    });
    $('#voucher').on('input', function(){
        $('#voucherNotFound').text('');
    })
    $('#applyVoucher').on('click', function(){
        var voucher = $('#voucher').val();
        console.log("VOUCHER", voucher)
        $.ajax({
            url: '{{ route('cart.applyVoucher', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ voucher: voucher, total: "{{$totals['total']}}" , username: '{{$username}}' }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                console.log(res)
                if(res.success){
                    var discount = parseFloat(res.totals.discount);
                    var shipping = parseFloat(res.totals.shipping)
                    var total = parseFloat(res.totals.total) - shipping;
                    var tax = parseFloat(res.totals.tax)
                    $('#voucherInput').hide();
                    $('#voucherApplied').show();
                    $('#voucherDisplay').text(res.voucher.code);
                    $('#diskonVoucher').show();
                    $('#voucher-amount').val(discount);
                    $('#diskon-voucher').text(discount.toLocaleString('id-ID'));
                    updateCartDisplay(res);
                }else{
                    $('#voucherNotFound').text(res.status)
                }
            }
        });
    })

    $('#removeVoucher').on('click', function(){
        $.ajax({
            url: '{{ route('cart.removeVoucher', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ total: total, username: '{{$username}}' }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(res) {
                console.log(res)
                var discount = parseFloat(res.totals.discount);
                var shipping = parseFloat(res.totals.shipping);
                var total = parseFloat(res.totals.total) + discount - shipping;
                var tax = parseFloat(res.totals.tax)
                if(res.success){
                    $('#voucherInput').show();
                    $('#voucherApplied').hide();
                    $('#voucherDisplay').text('');
                    $('#diskonVoucher').hide();
                    $('#diskon-voucher').text(0);
                    updateCartDisplay(res);
                }
            }
        });
    });

});
</script>
@endsection
