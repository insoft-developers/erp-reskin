@extends('master')

@section('style')
<style>
    .product-container {
        margin-bottom: 30px;
    }
    .product-container img {
        max-width: 100%;
        height: auto;
    }
    .product-title {
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
        height: 40px;
        line-height: 20px;
        color: black;
    }
    .product-price {
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
        color: black;
    }
    .add-to-cart {
        margin-top: 10px;
    }
    .add-to-cart button {
        background-color: #FFA500;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 25px;
    }
    .product-spec{
        text-decoration: underline;
        font-style: italic;
        font-weight: 800;
        font-size: 12px;
    }
    .btn-keranjang{
        background-color: #05aa5b !important;
        color: white;
        font-weight: 800;
    }
    .bg-randu{
        background-color: #385a9c !important;
        color: white;
    }
</style>
@endsection

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('katalog-randu.index') }}">Katalog Randu</a></li>
                        <li class="breadcrumb-item">Daftar Katalog Randu</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <form action="{{ route('katalog-randu.index') }}" method="get" class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="search" id="search" class="form-control" placeholder="Cari Katalog">
                    </div>
                    <div class="col-md-5 mb-3">
                        <select name="category_id" class="form-control" id="categoryCatalog">
                            <option value="" selected>Pilih Kategori Katalog</option>
                            @foreach ($category_product as $item)
                                <option value="{{ $item->id }}" {{ (\Request::get('category_id') == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 mb-3">
                        <button class="btn bg-randu"><i class="feather-search me-2"></i> Cari</button>
                    </div>
                </form>

                <div class="row">
                    {{-- FLASJ MESSAGE --}}
                    @if (session('success'))
                        <div class="col-md-12">
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        </div>
                    @elseif (session('error'))
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12">
                        <button class="btn bg-randu float-end" type="button" onclick="createData()"><i class="feather-shopping-cart me-2"></i> Keranjang Belanja  <span id="cart-count"> ({{ array_sum(array_column(session('cartKatalogProduct', []), 'quantity')) ?? 0 }})</span></button>
                    </div>

                    @foreach ($data as $item)
                        <div class="col-md-3 col-sm-6 p-3 product-container">
                            <img src="{{ asset('storage/' . $item->image) }}" style="width: 100%; height: auto" alt="{{ $item->name }}">
                            <div class="product-title mb-2">{{ \Str::limit($item->name, 60) }}</div>
                            <div class="mb-3" style="height: 40px;">{!! \Str::limit($item->description, 80, '...') !!}</div>
                            <a href="javascript:void(0)" class="product-spec" onclick="showData({{ $item->id }})">Lihat Spesifikasi Lengkap</a>
                            <div class="product-price">Rp {{ number_format($item->selling_price) }}</div>
                            <div class="add-to-cart">
                                <button class="btn-keranjang" type="button" onclick="addToCart({{ $item->id }})">+ Keranjang</button>
                                <button class="bg-randu" type="button" onclick="showCart({{ $item->id }})"><i class="fas fa-cart-plus"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>

    {{-- MODALS SHOW --}}
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-ce">
        <div class="modal-dialog modal-xl" id="content-modal-ce">
          
        </div>
    </div>

    {{-- MODALS CART --}}
    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-c">
        <div class="modal-dialog modal-fullscreen" id="content-modal-c">
          
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function() {
            // 
        });

        function createData() {
            $.ajax({
                url: "{{ route('katalog-randu.create') }}",
                type: 'GET', 
            })
            .done(function(data) {
                $('#content-modal-c').html(data);

                $("#modal-c").modal("show");
            })
            .fail(function(data) {
                Swal.fire('Error!', 'Keranjang Belanja Kosong, Silahkan Menambahkan Item Terlebih Dahulu', 'error');
            });
        }

        function showData(id) {
            $.ajax({
                url: "{{ route('katalog-randu.show', ':id') }}".replace(':id', id),
                type: 'GET', 
            })
            .done(function(data) {
                $('#content-modal-ce').html(data);

                $("#modal-ce").modal("show");
            })
            .fail(function() {
                Swal.fire('Error!', 'An error occurred while editing the record.', 'error');
            });
        }

        function showCart(id) {
            // SET DELAY
            setTimeout(function() {
                addToCart(id);
            }, 5000);

            createData();
        }

        function addToCart(id) {
            $.ajax({
                url: "{{ route('katalog-randu.add-to-cart', ':id') }}".replace(':id', id),
                type: 'POST', 
                data: {
                    _token: "{{ csrf_token() }}"
                }
            })
            .done(function(data) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    timer: 1000
                });

                $('#cart-count').text(' (' + data.item_count + ')');
            })
            .fail(function() {
                Swal.fire('Error!', 'An error occurred while adding the product to cart.', 'error');
            });
        }
    </script>
@endsection
