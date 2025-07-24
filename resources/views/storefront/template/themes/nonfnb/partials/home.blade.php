<div class="container">
    <div class="row g-3">
        @foreach ($products as $product)
            <div class="col-6">
                <div class="shop-card">
                    <div class="dz-media">
                        <a href="{{ route('product.detail', ['username' => $username, 'product' => $product->id]) }}"
                            class="dz-media media-100 mb-2">
                            @if (!empty($product->productImages) && count($product->productImages) > 0)
                                <img class="rounded-sm"
                                    src="{{ Storage::url('images/product/' . $product->productImages[0]->url) }}"
                                    alt="image">
                            @else
                                <img class="rounded-sm"
                                    src="{{ asset('template/main/images/product-placeholder.png') }}" alt="image">
                            @endif
                        </a>
                    </div>
                    <div class="dz-content">
                        <span class="font-12">
                            {{ $product->category_name }}
                        </span>
                        <h6 class="title"><a
                                href="{{ route('product.detail', ['username' => $username, 'product' => $product->id]) }}">{{ $product->name }}</a>
                        </h6>
                        <h6 class="price">Rp{{ number_format($product->price, 0, ',', '.') }}</h6>
                        <span class="font-12">
                            @if ($product->buffered_stock == 1)
                                @if ($product->quantity > 0)
                                    <span style="color:green;">Stok Tersedia :
                                        {{ number_format($product->quantity) }}</span>
                                @else
                                    <span style="color: red;">Stok Kosong</span>
                                @endif
                            @else
                                <span style="color:green;">Stok Tersedia</span>
                            @endif
                        </span>
                    </div>
                    @if ($product->is_variant == 2)
                        <div class="product-tag">
                            <span class="badge badge-secondary">
                                {{ $varian->where('product_id', $product->id)->count() }} Varian
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
