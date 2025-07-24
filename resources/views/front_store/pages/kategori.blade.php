@extends('front_store.template.pages')

@section('content-page')
    <div class="row catagore-bx g-4">
        @foreach ($kategori as $item)
            <div class="col-4 text-center">
                <a href="product-list.html">
                    <div class="dz-media media-60">
                        <img src="{{ asset('front_store/default.jpg') }}" alt="image">
                    </div>
                    <span> {{ $item->name }} </span>
                </a>
            </div>
        @endforeach
    </div>
@endsection
