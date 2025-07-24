@extends('master')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">

            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        {{-- <h5 class="m-b-10">Daftar Produk / Barang Jadi</h5> --}}
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">COGS Manufaktur</a></li>
                        <li class="breadcrumb-item">Daftar Bahan Baku</li>
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

            <div class="main-content">
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
                                        <table id="table-material" class="table table-striped mb-0 table-bordered">
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
