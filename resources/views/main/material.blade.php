@extends('main.master_new')

@section('content')
    <div class="page-wrapper">
        <div class="content" style="background: whitesmoke;">



            <div class="row">

                <div class="col-xxl-12">

                    <div class="card stretch stretch-full">
                        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                            <h5 class="card-title" style="margin: 0;">Daftar Bahan Baku</h5>
                            <div style="display: flex; gap: 10px;">
                                <button onclick="upload_bahan_baku()" class="btn btn-sm btn-insoft btn-warning">
                                    <i class="fa fa-file-excel"></i>&nbsp;&nbsp;Upload Masal Bahan Baku
                                </button>
                                <button onclick="add_material()" class="btn btn-sm btn-insoft btn-success">
                                    <i class="feather-plus"></i>&nbsp;&nbsp;Tambah Bahan Baku Baru
                                </button>
                            </div>
                        </div>

                        <div class="card-body custom-card-action p-0">
                            <div class="container mtop30 main-box">


                                <div class="table-responsive">
                                    <table id="table-material" class="table table-striped mb-0 table-bordered" style="width: 100%">
                                        <thead>
                                            <tr class="border-b">
                                                <th width="3%">Opsi</th>
                                                <th width="0%">ID</th>
                                                <th width="*">Nama Bahan</th>
                                                <th width="10%">SKU (Kode)</th>
                                                <th width="14%">Kategori</th>
                                                <th width="14%">Unit (Satuan)</th>
                                                <th width="14%">Stok</th>
                                                <th width="14%">COGS (HPP)</th>
                                                <th width="18%">Suplier</th>

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
