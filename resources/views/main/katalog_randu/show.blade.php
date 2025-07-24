<div class="modal-content">
    <div class="modal-header" style="background-color: #2f467a;">
        <h5 class="modal-title" style="color:white;">Detail {{ $data->name }}</h5>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 p-3">
                {{-- <img src="{{ asset('storage/' . $data->image) }}" style="width: 100%; height: auto;" alt="{{ $data->name }}"> --}}
                <img src="{{ 'storage/' . $data->image }}" style="width: 100%; height: auto;" alt="{{ $data->name }}">
            </div>
            <div class="col-md-6 p-3">
                <h2 class="product-title mb-3">{{ $data->name }}</h2>
                <div class="product-price mb-3">Rp {{ number_format($data->selling_price) }}</div>
                <p class="product-description mb-5">
                    {!! $data->description !!}
                </p>

                <button type="button" class="btn btn-secondary" style="border-radius: 25px; font-size: 12px;" data-bs-dismiss="modal">Tutup</button>

            </div>
        </div>
    </div>
</div>
