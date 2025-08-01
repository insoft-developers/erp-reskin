@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        <div class="content" style="background: whitesmoke;">

            <div class="row">

                <div class="col-xxl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="card-title" style="margin: 0;">Daftar Barang Setengah Jadi</h5>
                            <button onclick="add_data()" class="btn btn-sm btn-success" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" style="display: flex; align-items: center;">
                                <i class="feather-plus"></i>&nbsp;&nbsp;Tambah Barang ½ Jadi Baru
                            </button>
                        </div>

                        <div class="card-body custom-card-action p-0">
                            <div class="container mtop30 main-box">


                                <div class="table-responsive">
                                    <table id="table-inter-product" class="table table-striped mb-0 table-bordered">
                                        <thead>
                                            <tr class="border-b">
                                                <th width="3%">Opsi</th>
                                                <th width="0%">ID</th>
                                                <th width="*">Product</th>
                                                <th width="10%">SKU (Kode)</th>
                                                <th width="12%">Kategori</th>
                                                <th width="10%">Unit (Satuan)</th>
                                                <th width="18%">Komposisi</th>
                                                <th width="10%">Stok</th>
                                                <th width="14%">COGS (HPP)</th>

                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="mtop30"></div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>




        </div>
    </div>
@endsection
