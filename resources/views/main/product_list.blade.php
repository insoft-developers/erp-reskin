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
                        <li class="breadcrumb-item"><a href="#">Manajemen Produk</a></li>
                        <li class="breadcrumb-item">Daftar Produk & Barang</li>
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
    <h5 class="card-title" style="margin: 0;">Daftar Produk / Barang Jadi</h5>
    <div style="display: flex; gap: 10px;">
        <button onclick="upload_product()" class="btn btn-sm btn-warning" style="display: flex; align-items: center;">
            <i class="fa fa-file-excel"></i>&nbsp;&nbsp;Upload Masal Produk
        </button>
        <button onclick="add_product_module()" class="btn btn-sm btn-success" style="display: flex; align-items: center;">
            <i class="feather-plus"></i>&nbsp;&nbsp;Tambah Produk Baru
        </button>
    </div>
</div>

                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-control head-control" id="filter_category">
                                                <option value="">Filter Product Category</option>
                                                @foreach ($product_category as $pc)
                                                    <option value="{{ $pc->id }}">{{ $pc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <select class="form-control head-control" id="persamaan">
                                                <option value="1">Stock&nbsp;></option>
                                                <option value="2">Stock&nbsp;< </option>
                                                <option value="3">Stock&nbsp;=</option>

                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" id="stock" value="0"
                                                class="form-control head-control">
                                        </div>
                                        <div class="col-md-6 btn-container">
                                            <button id="btn_search" class="btn btn-sm btn-top btn-info"><i
                                                    class="fa fa-search"></i>
                                                &nbsp;Search</button>

                                            <button disabled="disabled" id="btn_delete_all"
                                                class="btn btn-sm btn-top btn-danger"><i class="fa fa-trash"></i>
                                                &nbsp;Hapus</button>

                                            <button id="btn_download" class="btn btn-sm btn-top btn-success"><i
                                                    class="fa fa-download"></i>
                                                &nbsp;Export</button>
                                        </div>
                                    </div>
                                    <div class="mtop40"></div>
                                    <div class="table-responsive">
                                        <table id="table-product-list" class="table table-striped mb-0 table-bordered">
                                            <thead>
                                                <tr class="border-b">
                                                    <th width="0%">ID</th>
                                                    <th width="0%"><input type="checkbox" id="check-all-product"></th>
                                                    <th width="15%">Opsi</th>
                                                    <th width="0%">Tampil di<br>Storefront</th>
                                                    <th width="0%">Kasir Bisa<br>Edit Harga</th>
                                                    <th width="0%">Gunakan<br>Stok</th>
                                                    <th width="3%">Gambar</th>
                                                    <th width="*">Nama Produk</th>
                                                    <th width="15%">Jenis<br>Komposisi</th>
                                                    <th width="15%">SKU (Kode)</th>
                                                    <th width="15%">Kategori</th>
                                                    <th width="15%">Harga Jual</th>
                                                    <th width="15%">COGS (HPP)</th>
                                                    <th width="15%">Stok</th>
                                                    <th width="15%">Unit (Satuan)</th>
                                                    <th width="15%">Nilai Barang</th>
                                                    <th width="10%">Margin</th>
                                                    <th width="10%">% Margin</th>
                                                    <th width="10%">Stok Ditahan</th>

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
