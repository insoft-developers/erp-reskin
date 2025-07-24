@extends('storefront.template.layouts.app')
@section('content')
@include('storefront.template.themes.fnb.components.header')
<!-- Page Content -->
<div class="page-content">
    <div class="container bottom-content">
        @include('storefront.template.themes.fnb.partials.categories')
        @include('storefront.template.themes.fnb.partials.offer')
        @include('storefront.template.themes.fnb.partials.home')
	</div>
</div>
<!-- Page Content End-->

@endsection
@section('js')
<script>
$(document).ready(function() {
    function updateCartDisplay(totalQuantity) {
        $('#cart-count').text(totalQuantity);
    }

    $('.add-to-cart').click(function() {
        var button = $(this);
        var productId = button.data('product-id');
        var quantityInput = $('.quantity[data-product-id="' + productId + '"]');
        var quantity = parseInt(1);

        var product = {
            id: button.data('product-id'),
            name: button.data('product-name'),
            price: button.data('product-price'),
            image: button.data('product-image'),
            unit: button.data('product-unit'),
            quantity: quantity,
            notes: ''
        };

        $.ajax({
            url: '{{ route('cart.add', $username) }}',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ product: product, quantity: quantity }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                updateCartDisplay(response.totalQuantity);
                swal.fire('Informasi', 'Berhasil memasukan ke keranjang', 'success').then(() => {
                })
            }
        });
    });

    $.ajax({
        url: '{{ route('cart.index', $username) }}',
        method: 'GET',
        success: function(response) {
            updateCartDisplay(response.totalQuantity);
        }
    });
});
</script>

@endsection


