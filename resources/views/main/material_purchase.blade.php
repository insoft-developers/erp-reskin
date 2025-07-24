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
                        <li class="breadcrumb-item"><a href="#">Pembelian & Produksi</a></li>
                        <li class="breadcrumb-item">Transaksi Bahan Baku</li>
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
    <h5 class="card-title" style="margin: 0;">Transaksi Bahan Baku</h5>
    <div style="display: flex; gap: 10px;">
        <button onclick="upload_material_purchase()" class="btn btn-sm btn-insoft btn-warning">
            <i class="fa fa-file-excel"></i>&nbsp;&nbsp;Upload Masal Pembelian
        </button>
        <button onclick="add_data()" class="btn btn-sm btn-insoft btn-success" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <i class="feather-plus"></i>&nbsp;&nbsp;Buat Pembelian Bahan Baku
        </button>
    </div>
</div>

                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">


                                    <div class="table-responsive">
                                        <table id="table-material-purchase" class="table table-striped mb-0 table-bordered">
                                            <thead>
                                                <tr class="border-b">
                                                    <th width="3%">Opsi</th>
                                                    <th width="0%">ID</th>
                                                    <th width="8%">Sync Jurnal</th>
                                                    <th width="12%">Tanggal Transaksi</th>
                                                    <th width="12%">Nama/No.Refrensi</th>
                                                    <th width="12%">Supplier</th>
                                                    <th width="12%">Foto</th>
                                                    <th width="*">Bahan Baku Yang di Beli | Qty Pembelian | Harga Satuan | Total Pembelian</th>
                                                    <th width="10%">Subtotal</th>
                                                    <th width="14%">Pajak, Diskon & Lain-lain</th>
                                                    <th width="14%">Grand Total</th>

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
