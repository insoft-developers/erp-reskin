<div class="title-bar">
    <span class="title mb-0">Pilih produk</span>
</div>
<div>
    @foreach ($products as $product)
        <div class="product-list">
            <div class="dz-content">
                <h4 class="item-name">
                    <a href="{{ route('product.detail', ['username' => $username, 'product' => $product->id]) }}">
                        {{ $product->name }}
                    </a>
                </h4>

                <div class="price-wrapper">
                    <h6 class="current-price"><i
                            class="fa-solid fa-rupiah-sign"></i>{{ number_format($product->price, 0, ',', '.') }}</h6>
                    <!-- <span class="old-price"><i class="fa-solid fa-indian-rupee-sign"></i>1100</span> -->
                </div>
                <br>

                <div class="item-name">
                    @if ($product->buffered_stock == 1)
                        @if ($product->quantity > 0)
                            <p style="color: green;"><i class="fa-solid"></i>Stok Tersedia :
                                {{ number_format($product->quantity) }}</p>
                        @else
                            <p style="color: red;"><i class="fa-solid"></i>Stok Kosong</p>
                        @endif
                    @else
                        <p style="color: green;"><i class="fa-solid"></i>Stok Tersedia</p>
                    @endif
                    <!-- <span class="old-price"><i class="fa-solid fa-indian-rupee-sign"></i>1100</span> -->
                </div>
                <div class="offer-code">
                    @if ($product->is_variant == 2)
                        {{ $varian->where('product_id', $product->id)->count() }} Varian
                    @endif
                </div>
                <div class="footer-wrapper">
                    <span class="text-muted" style="font-size: 12px;">{!! strlen($product->description) > 110 ? substr($product->description, 0, 110) . '...' : $product->description !!}</span>
                </div>
            </div>
            <div class="text-end">
                <a href="{{ route('product.detail', ['username' => $username, 'product' => $product->id]) }}"
                    class="dz-media media-100 mb-2">
                    @if (!empty($product->productImages) && count($product->productImages) > 0)
                        <img class="rounded-sm"
                            src="{{ Storage::url('images/product/' . $product->productImages[0]->url) }}" alt="image"
                            style="height:100px;">
                    @else
                        <img class="rounded-sm" src="{{ asset('template/main/images/product-placeholder.png') }}"
                            alt="image">
                    @endif
                </a>
                <a href="{{ route('product.detail', ['username' => $username, 'product' => $product->id]) }}"
                    class="btn btn-sm btn-block btn-outline-primary">Tambah</a>
            </div>
        </div>
    @endforeach
</div>
