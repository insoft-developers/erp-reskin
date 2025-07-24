@extends('storefront.template.layouts.app')

@section('content')
<header class="header">
	<div class="main-bar">
		<div class="container">
			<div class="header-content">
				<div class="left-content">
					<a href="javascript:void(0);" class="back-btn">
						<i class="fa-solid fa-arrow-left"></i>
					</a>
					<h4 class="title mb-0 text-nowrap">Product List</h4>
				</div>
				<div class="mid-content">
				</div>
				<div class="right-content d-flex align-items-center">
				</div>
			</div>
		</div>
	</div>
</header>
<div class="page-content">
    <div class="container mb-5">
        @if($products)
        <div class="row g-3">
        @foreach($products as $product)
		<div class="col-6">
			<div class="shop-card">
				<div class="dz-media">
                <a href="{{route('product.detail', ['username' => $username, 'product' => $product->id])}}" class="dz-media media-100 mb-2">
					@if(!empty($product->productImages) && count($product->productImages) > 0)
                    <img class="rounded-sm" src="{{ Storage::url('images/product/' . $product->productImages[0]->url) }}" alt="image">
                    @else
			    	<img class="rounded-sm" src="{{ asset('template/main/images/product-placeholder.png') }}" alt="image">
                    @endif
			    </a>
				</div>
				<div class="dz-content">
					<span class="font-12">
                        {{$product->category_name}}
                    </span>
					<h6 class="title"><a href="{{route('product.detail', ['username' => $username, 'product' => $product->id])}}">{{$product->name}}</a></h6>
					<h6 class="price">Rp{{number_format($product->price,0,',','.')}}</h6>
                    <div style="margin-top:10px"></div>
                    @if($product->buffered_stock == 1)
                    @if($product->quantity > 0)
                    <p style="color:green;">Stok Tersedia : {{ number_format($product->quantity) }}</p>
                    @else
                    <p style="color:red;">Stok Kosong</p>
                    @endif
                    
                    @else
                    <p style="color: green;">Stok Tersedia</p>
                    @endif
				</div>
                @if($product->is_variant == 2)
				<div class="product-tag">
					<span class="badge badge-secondary">
				        {{$varian->where('product_id', $product->id)->count()}} Varian
                    </span>
                </div>
                @endif
			</div>
		</div>
		@endforeach
	</div>
        @else
        <div class="text-center">
            <h5>Produk belum tersedia</h5>
        </div>
        @endif
	</div>
</div>

@endsection
@section('js')
<!-- In your Blade template (e.g., cart.blade.php) -->
<script>
$(document).ready(function() {
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
        console.log("HERE", totals)
        var subtotal = parseInt(totals.subtotal)
        var total = parseInt(totals.total)

        $('#cart-count').text(totals.totalQuantity);
        $('#cart-subtotal').text(subtotal.toLocaleString('id-ID'));
        $('#cart-total').text(total.toLocaleString('id-ID'));
        $('#footer-total').text(total.toLocaleString('id-ID'));
    }

    $('.update-quantity').change(function() {
        var input = $(this);
        var productId = input.data('product-id');
        var quantity = parseInt(input.val());
        // var variant = input.data('product-varian');

        $.ajax({
            url: '{{ route('cart.updateQuantity', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ productId: productId, quantity: quantity }),
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
            data: JSON.stringify({ productId: productId }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.status === 'Product removed from cart') {
                    $('div[data-product-id="' + productId + '"]').remove();
                    updateCartDisplay(response);

                    console.log("TOTAL",response.total)
                }
            }
        });
    });

    $.ajax({
        url: '{{ route('cart.data', $username) }}',
        method: 'GET',
        success: function(response) {

            updateCartDisplay(response);
        }
    });
    $('.product-note').change(function() {
        var input = $(this);
        var productId = input.data('product-id');
        var notes = input.val();

        $.ajax({
            url: '{{ route('cart.updateNotes', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ productId: productId, notes: notes }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("UPDATE", response.totals);
                if(quantity == 0){
                    $('div[data-product-id="' + productId + '"]').remove();
                }
                updateCartDisplay(response.totals);
            }
        });
    });
    $('#applyVoucher').on('click', function(){
        var input = $(this);
        var voucher = input.val();

        $.ajax({
            url: '{{ route('cart.applyVoucher', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ voucher: voucher }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log("UPDATE", response.totals);
                if(quantity == 0){
                    $('div[data-product-id="' + productId + '"]').remove();
                }
                updateCartDisplay(response.totals);
            }
        });
    })
});
</script>
@endsection
