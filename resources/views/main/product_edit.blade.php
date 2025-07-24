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
                        <li class="breadcrumb-item"><a href="{{ url('product') }}">Daftar Produk & Barang</a></li>
                        <li class="breadcrumb-item">Ubah Produk & Barang</li>
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
                            <div class="card-header">
                                <h5 class="card-title">Ubah Produk / Barang Jadi</h5>

                            </div>
                            <div class="card-body custom-card-action p-0">
                                <form id="form-products-add" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{ method_field('PATCH') }}
                                    <input type="hidden" id="id" name="id" value="{{ $product->id }}">
                                    <div class="container mtop30 main-box">

                                        <div class="kartu">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Nama Produk:</label>
                                                        <input value="{{ $product->name }}" type="text" name="name"
                                                            id="name" class="form-control cust-control"
                                                            placeholder="Cth: Nasi Goreng Ikan Asin">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Shortname Produk (Opsional) :</label>
                                                        <input value="{{ $product->code }}" type="text" name="code"
                                                            id="code" class="form-control cust-control"
                                                            placeholder="Cth: nasigorengikanasin">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mtop20">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Kategori Produk:</label>
                                                        <select class="form-control cust-control" id="category_id"
                                                            name="category_id">
                                                            <option value="">Pilih Kategori Produk</option>
                                                            @foreach ($categories as $category)
                                                                <option <?php if ($product->category_id == $category->id) {
                                                                    echo 'selected';
                                                                } ?> value="{{ $category->id }}">
                                                                    {{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>SKU Produk:</label>
                                                        <input value="{{ $product->sku }}" type="text" name="sku"
                                                            id="sku" class="form-control cust-control"
                                                            placeholder="Contoh: NGIA">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Berat Produk - in gram(g) :</label>
                                                        <input type="number" name="weight" id="weight"
                                                            class="form-control cust-control" value="{{ $product->weight }}"
                                                            placeholder="Cth: 100 (opsional)">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Harga Bisa Diubah Kasir:</label>
                                                        <select name="is_editable" id="is_editable" class="form-control cust-control">
                                                            <option value="0" {{ $product->is_editable == 0 ? 'selected' : '' }}>No</option>
                                                            <option value="1" {{ $product->is_editable == 1 ? 'selected' : '' }}>Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mtop30"></div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Jual Default:</label>
                                                    <input value="{{ ribuan($product->price) }}" type="text"
                                                        id="price_text" class="form-control cust-control"
                                                        placeholder="Cth: 10.000">
                                                    <input value="{{ $product->price }}" type="hidden" id="price"
                                                        name="price">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Delivery (Take Away):</label>
                                                    <input type="text" id="price_ta" name="price_ta"
                                                        value="{{ number_format($product->price_ta, 0, ',', '.') }}"
                                                        class="form-control cust-control" placeholder="Cth: 10.000">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Marketplace (GrabFood, GoFood, Shopee, Toped):</label>
                                                    <input type="text" id="price_mp" name="price_mp"
                                                        value="{{ number_format($product->price_mp, 0, ',', '.') }}"
                                                        class="form-control cust-control" placeholder="Cth: 10.000">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Custom (Opsional):</label>
                                                    <input type="text" id="price_cus" name="price_cus"
                                                        value="{{ number_format($product->price_cus, 0, ',', '.') }}"
                                                        class="form-control cust-control" placeholder="Cth: 10.000">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mtop30"></div>
                                        <div class="kartu bg-beige">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Tipe Produk:</label>
                                                        <select class="form-control cust-control" id="is_variant"
                                                            name="is_variant">

                                                            <option <?php if ($product->is_variant == 1) {
                                                                echo 'selected';
                                                            } ?> value="1">Single Product
                                                                (Tanpa Varian)
                                                            </option>
                                                            <option <?php if ($product->is_variant == 2) {
                                                                echo 'selected';
                                                            } ?> value="2">Varian Product
                                                                (Dengan Varian)
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="varian-table-container">
                                                        @if ($product->is_variant == 2)
                                                            <table id="varian-table"
                                                                class="table table-bordered table-striped mtop20">
                                                                <thead>
                                                                    <tr>
                                                                        <th colspan="5">
                                                                            <h5 style="text-align: center;">Varian Product
                                                                                List</h5>
                                                                        </th>
                                                                        <th>
                                                                            <center><a href="javascript:void(0);"
                                                                                    onclick="add_varian_group()"
                                                                                    class="avatar-text avatar-md bg-success text-white"
                                                                                    data-bs-toggle="dropdown"
                                                                                    data-bs-auto-close="outside"><i
                                                                                        class="feather-plus"></i></a>
                                                                            </center></a>
                                                                        </th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>
                                                                    @foreach ($group as $index => $g)
                                                                        <tr class="baris_{{ $index + 1 }}">

                                                                            <td colspan="5"><input type="text"
                                                                                    id="vg_{{ $index + 1 }}"
                                                                                    value="{{ $g->varian_group }}"
                                                                                    onkeyup="onVarianChange(1)"
                                                                                    class="form-control cust-control"
                                                                                    placeholder="Varian Group"></td>


                                                                            <td>
                                                                                <center><a href="javascript:void(0);"
                                                                                        onclick="add_varian_item({{ $index + 1 }})"
                                                                                        class="avatar-text avatar-md bg-info text-white"
                                                                                        data-bs-toggle="dropdown"
                                                                                        data-bs-auto-close="outside"><i
                                                                                            class="feather-plus"></i></a>
                                                                                </center>
                                                                            </td>
                                                                        </tr>

                                                                        @foreach ($varians as $in => $varian)
                                                                            @if ($varian->varian_group == $g->varian_group)
                                                                                <tr
                                                                                    class="row_middle_{{ $index + 1 }} baris_{{ $index + 1 }}">

                                                                                    <td width="*"><input
                                                                                            value="{{ $varian->varian_name }}"
                                                                                            id="varian_name_{{ $in + 1 }}"
                                                                                            name="varian_name[]"
                                                                                            type="text"
                                                                                            class="form-control cust-control"
                                                                                            placeholder="Varian Name"></td>

                                                                                    <td width="20%"><input
                                                                                            value="{{ $varian->sku }}"
                                                                                            id="sku_{{ $in + 1 }}"
                                                                                            name="varian_sku[]"
                                                                                            type="text"
                                                                                            class="form-control cust-control"
                                                                                            placeholder="SKU"></td>

                                                                                    <td width="20%"><input
                                                                                            value="{{ ribuan($varian->varian_price) }}"
                                                                                            id="varian_price_text_{{ $in + 1 }}"
                                                                                            onkeyup="varian_price_keyup({{ $in + 1 }})"
                                                                                            type="text"
                                                                                            class="form-control cust-control"
                                                                                            placeholder="Harga"><input
                                                                                            value ="{{ $varian->varian_group }}"
                                                                                            type="hidden"
                                                                                            class="varian_group_{{ $index + 1 }}"
                                                                                            name="varian_group[]"><input
                                                                                            value="{{ $varian->varian_price }}"
                                                                                            type="hidden"
                                                                                            id="varian_price_{{ $in + 1 }}"
                                                                                            name="varian_price[]"></td>

                                                                                    <td width="1%"><input
                                                                                            <?= $varian->single_pick == 1 ? 'checked' : '' ?>
                                                                                            onclick="on_change_check({{ $in + 1 }})"
                                                                                            title="Single Pick"
                                                                                            id="sp_{{ $in + 1 }}"
                                                                                            class="chk-item"
                                                                                            type="checkbox"><input
                                                                                            type="hidden"
                                                                                            id="single_pick_{{ $in + 1 }}"
                                                                                            name="single_pick[]"
                                                                                            value="{{ $varian->single_pick }}">
                                                                                    </td>
                                                                                    <td width="15%"><input
                                                                                            type="number"
                                                                                            id="max_quantity_{{ $in + 1 }}"
                                                                                            name="max_quantity[]"
                                                                                            placeholder="max qty"
                                                                                            value="{{ $varian->max_quantity }}"
                                                                                            class="form-control cust-control">
                                                                                    </td>

                                                                                    <td width="2%">
                                                                                        <center><a
                                                                                                href="javascript:void(0);"
                                                                                                id="btn_delete_item_{{ $in + 1 }}"
                                                                                                onclick="delete_varian_item({{ $in + 1 }})"
                                                                                                class="avatar-text avatar-md bg-danger text-white"
                                                                                                data-bs-toggle="dropdown"
                                                                                                data-bs-auto-close="outside"><i
                                                                                                    class="fa fa-trash"></i></a>
                                                                                        </center>
                                                                                    </td>
                                                                                </tr>
                                                                            @endif
                                                                        @endforeach

                                                                        <tr class="baris_{{ $index + 1 }}">

                                                                            <td colspan="6"
                                                                                style="border-bottom:1px solid orange;"><a
                                                                                    href="javascript:void(0);"
                                                                                    onclick="delete_varian_group({{ $index + 1 }})"
                                                                                    class="avatar-text avatar-md bg-danger text-white"
                                                                                    data-bs-toggle="dropdown"
                                                                                    data-bs-auto-close="outside"><i
                                                                                        class="fa fa-remove"></i></a></td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mtop20">
                                                <div class="col-md-12">
                                                    <div class="p-4 bg-soft-warning rounded-3">
                                                        <p class="fs-12 text-dark"><strong>Single
                                                                Pick</strong><br>"Single Pick"
                                                            memungkinkan pelanggan memilih hanya satu varian dari beberapa
                                                            pilihan yang tersedia. Cocok untuk produk yang hanya bisa
                                                            dipilih satu varian, seperti satu ukuran atau satu warna pakaian
                                                            per transaksi.
                                                        </p>
                                                        <p class="fs-12 text-dark"><strong>Max Quantity</strong><br>Opsi
                                                            "Max Quantity" membatasi jumlah maksimal varian yang bisa
                                                            dipilih dalam satu transaksi. Berguna untuk mengelola stok dan
                                                            mencegah over-ordering, seperti membatasi maksimal 3 jenis
                                                            topping pada satu minuman.
                                                        </p>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="mtop30"></div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Satuan Produk:</label>
                                                    <select class="form-control cust-control" name="unit">
                                                        <option value="">Select Unit</option>
                                                        @foreach ($units as $unit)
                                                            <option <?php if ($product->unit == $unit->unit_name) {
                                                                echo 'selected';
                                                            } ?> value="{{ $unit->unit_name }}">
                                                                {{ $unit->unit_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Barcode Produk (Opsional):</label>
                                                    <input value="{{ $product->barcode }}" type="text"
                                                        class="form-control cust-control" placeholder="barcode"
                                                        id="barcode" name="barcode">

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <lable>Buffered Stock/Gunakan Stok:</lable>
                                                    <select class="form-control cust-control" id="buffered_stock"
                                                        name="buffered_stock">

                                                        @if ($product->created_by == 1 && $product->is_manufactured == 2)
                                                            <option <?php if ($product->buffered_stock == 0) {
                                                                echo 'selected';
                                                            } ?> value="0">No - Jangan Gunakan Stok
                                                            </option>
                                                        @elseif($product->created_by == 0 && $product->is_manufactured == 2)
                                                            <option <?php if ($product->buffered_stock == 1) {
                                                                echo 'selected';
                                                            } ?> value="1">Yes - Gunakan Stok
                                                            </option>
                                                        @else
                                                            <option <?php if ($product->buffered_stock == 0) {
                                                                echo 'selected';
                                                            } ?> value="0">No - Jangan Gunakan Stok
                                                            </option>
                                                            <option <?php if ($product->buffered_stock == 1) {
                                                                echo 'selected';
                                                            } ?> value="1">Yes - Gunakan Stok
                                                            </option>
                                                        @endif

                                                    </select>
                                                    <small class="help-text">Buffer Stock / Gunakan Stok Produk Otomatis.
                                                        Jika "YES" maka produk tidak akan bisa dipesan ketika stoknya
                                                        0*</small>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Stock Alert / Stok Minimal</label>
                                                    @if ($product->buffered_stock == 1)
                                                        <input value="{{ $product->stock_alert }}" type="number"
                                                            class="form-control cust-control" id="stock_alert"
                                                            name="stock_alert">
                                                    @else
                                                        <input value="{{ $product->stock_alert }}" readonly="readonly"
                                                            type="number" class="form-control cust-control"
                                                            id="stock_alert" name="stock_alert">
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mtop30"></div>
                                        <div class="kartu bg-beige">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Jenis HPP/COGS Produk:</label>
                                                        <select class="form-control cust-control" id="is_manufactured"
                                                            name="is_manufactured">
                                                            <option <?php if ($product->is_manufactured == 1) {
                                                                echo 'selected';
                                                            } ?> value="1">Beli Jadi</option>
                                                            <option <?php if ($product->is_manufactured == 2) {
                                                                echo 'selected';
                                                            } ?> value="2">Manufactured
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    @if ($product->is_manufactured == 2)
                                                        <a onclick="tambah_bahan()" id="tambah-bahan-text"
                                                            href="javascript:void(0);" style="color: green;">(+)
                                                            Tambah Bahan</a>
                                                        
                                                        <div class="form-group" id="manual_hpp" style="display:none;">
                                                            <label>COGS (HPP)</label>
                                                            <input value="{{ ribuan($product->cost) }}"
                                                                onkeyup="onChangeCost()" type="text" id="costtext"
                                                                class="form-control cust-control"
                                                                placeholder="masukkan nilai hpp">
                                                            <input type="hidden" id="cost" name="cost"
                                                                value="{{ $product->cost }}">
                                                        </div>


                                                    @endif
                                                    @if ($product->is_manufactured == 1)
                                                        <a onclick="tambah_bahan()" style="display: none; color: green;"
                                                            id="tambah-bahan-text" href="javascript:void(0);">(+)
                                                            Tambah Bahan</a>
                                                        <div class="form-group" id="manual_hpp" style="display:block;">
                                                            <label>COGS (HPP)</label>


                                                            @if($product->buffered_stock == 1)
                                                            <input readonly value="{{ ribuan($product->cost) }}"
                                                                onkeyup="onChangeCost()" type="text" id="costtext"
                                                                class="form-control cust-control"
                                                                placeholder="masukkan nilai hpp">
                                                            @else
                                                            <input value="{{ ribuan($product->cost) }}"
                                                                onkeyup="onChangeCost()" type="text" id="costtext"
                                                                class="form-control cust-control"
                                                                placeholder="masukkan nilai hpp">

                                                            @endif

                                                            <input type="hidden" id="cost" name="cost"
                                                                value="{{ $product->cost }}">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row mtop10">
                                                <div class="col-md-8">
                                                    <div id="composition-container">
                                                        @if ($product->is_manufactured == 2)
                                                            @foreach ($komposisi as $com_index => $com)
                                                                <div class="row baris mtop10 baris-tambahan"
                                                                    id="baris_{{ $com_index }}">
                                                                    <div class="col-md-8">
                                                                        <select
                                                                            class="form-control cust-control select-item"
                                                                            id="composition_{{ $com_index }}"
                                                                            name="composition[]">
                                                                            <option value="">Pilih komposisi bahan
                                                                            </option>
                                                                            <optgroup label="Bahan Baku">
                                                                                @foreach ($materials as $material)
                                                                                    <option <?php if ($material->id . '_1' == $com->material_id . '_' . $com->product_type) {
                                                                                        echo 'selected';
                                                                                    } ?>
                                                                                        value="{{ $material->id }}_1">
                                                                                        {{ $material->material_name }} -
                                                                                        {{ $material->unit }}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                            <optgroup
                                                                                label="Barang
                                                                                        Setengah Jadi">
                                                                                @foreach ($inters as $inter)
                                                                                    <option <?php if ($inter->id . '_2' == $com->material_id . '_' . $com->product_type) {
                                                                                        echo 'selected';
                                                                                    } ?>
                                                                                        value="{{ $inter->id }}_2">
                                                                                        {{ $inter->product_name }} -
                                                                                        {{ $inter->unit }}</option>
                                                                                @endforeach
                                                                            </optgroup>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <input value="{{ $com->quantity }}"
                                                                            type="text"
                                                                            class="form-control cust-control"
                                                                            id="quantity_{{ $com_index }}"
                                                                            name="quantity[]" placeholder="quantitiy">
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <center><a
                                                                                onclick="delete_composition_item({{ $com_index }})"
                                                                                href="javascript:void(0);"
                                                                                class="avatar-text avatar-md bg-danger text-white"
                                                                                data-bs-toggle="dropdown"
                                                                                data-bs-auto-close="outside"><i
                                                                                    class="fa fa-trash"></i></a></center>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" id="createdBy"
                                                {{ $product->created_by == 1 || $product->is_manufactured == 2 ? 'style=display:block' : 'style=display:none' }}>
                                                <div class="row mtop20">
                                                    <div class="col-md-12">
                                                        <div class="p-4 bg-soft-warning rounded-3">
                                                            <p class="fs-12 text-dark"><strong>Produk Beli
                                                                    Jadi</strong><br>"Produk Beli Jadi" adalah produk yang
                                                                langsung dibeli dari pemasok dalam kondisi siap jual, tanpa
                                                                perlu diproses lebih lanjut. Contohnya, Lampu, Minuman
                                                                Kaleng, Baju Branded, dan lain-lain.
                                                            </p>
                                                            <p class="fs-12 text-dark"><strong>Produk
                                                                    Manufactured</strong><br>"Produk Manufactured" adalah
                                                                produk yang memerlukan perakitan atau pencampuran bahan
                                                                sebelum dijual. Contohnya, kopi latte yang diracik dari
                                                                berbagai bahan seperti susu dan espresso sesuai resep.
                                                                Sistem POS akan melacak penggunaan bahan baku dan menjaga
                                                                stok tetap sesuai.
                                                            </p>
                                                            <p class="fs-12 text-dark"><strong>COGS
                                                                    (HPP)</strong><br>"Harga Pokok Penjualan" adalah biaya
                                                                yang dikeluarkan untuk memproduksi produk barang atau jasa
                                                                yang dijual. Jika produknya adalah produk fisik, maka HPP
                                                                bisa di isikan 0 saja. Karena nanti akan terupdate otomatis
                                                                oleh sistem.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12 mtop20 mb-3">
                                                        <div class="form-group">
                                                            <label class="mb-3">Opsi Produk Dibuat:</label>

                                                            <div class="form-group">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        onclick="bufferedStockChangeCreatedBy()"
                                                                        name="created_by" id="created_by_0"
                                                                        value="0"
                                                                        {{ $product->created_by == 0 ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="created_by_0">Dibuat Terlebih Dahulu</label>
                                                                </div>

                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio"
                                                                        onclick="bufferedStockChangeCreatedBy()"
                                                                        name="created_by" id="created_by_1"
                                                                        value="1"
                                                                        {{ $product->created_by == 1 ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="created_by_1">Dibuat By Pesanan</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="p-4 bg-soft-warning rounded-3">
                                                            <p class="fs-12 text-dark"><strong>Produk yang Dibuat Terlebih
                                                                    Dahulu (Make-to-Stock)</strong><br>adalah produk yang
                                                                diproduksi dan disimpan dalam stok sebelum ada permintaan
                                                                spesifik dari pelanggan. Contohnya: Roti, Kue, Fashion,
                                                                Furniture, dan lain-lain
                                                            </p>
                                                            <p class="fs-12 text-dark"><strong>Produk yang Dibuat
                                                                    Berdasarkan Pesanan (Make-to-Order)</strong><br>adalah
                                                                produk yang hanya diproduksi setelah ada pesanan dari
                                                                pelanggan. Contohnya: nasi Goreng, Kentang Goreng, Kopi, dan
                                                                lain-lain
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="row mtop30">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Deskripsi Produk</label>
                                                        <textarea class="form-control cust-cuntrol" id="description" name="description" placeholder="Deskripsi Produk"><?= $product->description ?></textarea>
                                                        <input type="hidden" id="desc_assist" name="desc_assist"
                                                            value="<?= $product->description ?>">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row mtop30">
                                                <div class="col-md-12">
                                                    <div class="kartu bg-beige">
                                                        <div class="form-group">
                                                            <label>Upload Gambar 500x500 piksel (Persegi)</label>
                                                            <input style="display: none;" type="file" name="image[]"
                                                                id="image" multiple accept=".jpg, .jpeg, .png, .webp">
                                                        </div>
                                                        <img id="upload-product-image" class="upload-image"
                                                            src="{{ asset('template/main/images/upload-icon.png') }}">
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row mtop20">
                                                <div class="col-md-12">
                                                    <label>Upload Preview</label>
                                                    <div class="kartu bg-beige" id="preview-container">
                                                        @if ($gambar->count() > 0)
                                                            @foreach ($gambar as $gb_index => $gb)
                                                                <img id="image_{{ $gb_index }}" class="img-preview"
                                                                    src="{{ Storage::url('images/product/' . $gb->url) }}">
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
<div>
    <button style="float: left; margin-bottom: 30px; margin-right: 8px;" type="submit"
        class="btn btn-primary mtop30">Simpan</button>
    <a href="{{ url('product') }}">
        <button style="float: left; margin-bottom: 30px;" type="button" class="btn btn-danger mtop30">
            Kembali Ke Daftar Produk
        </button>
    </a>
</div>


                                </form>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>


        </div>
    @endsection
