@extends('main.master_new')

@section('content')
    <div class="page-wrapper">




        <div class="content" style="background: whitesmoke;">
            <div class="row">

                <div class="col-xxl-12">
                    <div class="card stretch stretch-full">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="card-title" style="margin: 0;">Kategori Produk</h5>
                            <button onclick="add()" class="btn btn-sm btn-insoft btn-success" data-bs-toggle="dropdown"
                                data-bs-auto-close="outside" style="display: flex; align-items: center;">
                                <i class="feather-plus"></i>&nbsp;&nbsp;Tambah Kategori Baru
                            </button>
                        </div>

                        <div class="card-body custom-card-action p-0">
                            <div class="container mtop30 main-box">


                                <div class="table-responsive">
                                    <table id="table-product-category" class="table table-striped mb-0 table-bordered">
                                        <thead>
                                            <tr class="border-b">
                                                <th width="3%">Opsi</th>
                                                <th width="0%">ID</th>
                                                <th width="30%">Kategori</th>
                                                <th width="15%">KODE</th>
                                                <th width="15%">GAMBAR</th>
                                                <th width="*">DESKRIPSI</th>


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
