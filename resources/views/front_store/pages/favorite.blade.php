@extends('front_store.template.pages')

@section('content-page')
    <div class="product-list">
        <div class="dz-content">
            <h4 class="item-name">
                <a href="cart.html">
                    Goat + Chicken Skinless + Cleaned Prawns
                </a>
            </h4>
            <div class="offer-code">
                FLAT 60% off Code: 636GCP
            </div>
            <div class="price-wrapper">
                <h6 class="current-price"><i class="fa-solid fa-rupiah-sign"></i>930</h6>
                <span class="old-price"><i class="fa-solid fa-rupiah-sign"></i>1100</span>
            </div>
            <div class="footer-wrapper">
                <span class="product-title">Combo pack</span>
            </div>
        </div>
        <div class="text-end">
            <a href="cart.html" class="dz-media media-100">
                <img class="rounded-sm" src="assets/images/product/1.jpg" alt="image">
            </a>
            <a href="cart.html" class="btn btn-sm btn-block btn-outline-primary">ADD</a>
        </div>
    </div>
@endsection
