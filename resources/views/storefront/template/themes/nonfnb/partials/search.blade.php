<div class="recent-search-list">
    @if($products->count() > 0)
        <ul>
            @foreach($products as $product)
			<li>
				<a href="product.html" class="search-content">
					<i class="icon feather icon-clock me-2"></i>
					<span>{{ $product->name }}</span>
					<i class="icon feather icon-arrow-up-left"></i>
				</a>
			</li>
            @endforeach
		</ul>
    @else
        <p>No products found</p>
    @endif
</div>