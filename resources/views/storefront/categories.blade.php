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
					<h4 class="title mb-0 text-nowrap">Categories</h4>
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
    <div class="container bottom-content">
		<div class="row catagore-bx g-4">
        @if($categories)
            @foreach($categories as $category)
		        <div class="col-4 text-center">
					<a href="{{route('product.category', ['username' => $username, 'category' => $category->id])}}">
						<div class="dz-media media-60">
                        @if($category->image)
                            <img src="{{Storage::url('images/category/')}}{{$category->image}}" alt="image">
                        @else
				            <img src="/storefront/assets/images/categore/5.png" alt="image">
                        @endif
						</div>
						<span>{{$category->name}}</span>
					</a>
				</div>
            @endforeach
        @else
        <div class="text-center">
            <h5>Kategori belum tersedia</h5>
        </div>
        @endif
        </div>
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
