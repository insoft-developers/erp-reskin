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
                        <li class="breadcrumb-item">Tambah Produk & Barang</li>
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
                                <h5 class="card-title">Tambah Produk / Barang Jadi</h5>

                            </div>
                            <div class="card-body custom-card-action p-0">
                                <form id="form-products-add" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="container mtop30 main-box">

                                        <div class="kartu">
                                            <div class="row">
                                                
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Nama Produk:</label>
                                                        <input type="text" name="name" id="name"
                                                           class="form-control cust-control"
                                                            placeholder="Cth: Nasi Goreng Ikan Asin"
                                                            oninput="generateSKU()">
                                                    </div>
                                                </div>

                                                <div class="col-md-4" style="display: none;">>
                                                    <div class="form-group">
                                                        <label>Shortname Produk (Opsional):</label>
                                                        <input type="text" name="code" id="code"
                                                            class="form-control cust-control"
                                                            placeholder="Cth: nasigorengikanasin">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mtop20">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Kategori Produk :</label>
                                                        <select class="form-control cust-control" id="category_id"
                                                            name="category_id">
                                                            <option value="">Pilih Kategori Produk</option>
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>SKU Produk:</label>
                                                        <input type="text" name="sku" id="sku"
                                                            class="form-control cust-control"
                                                            placeholder="Contoh: NGIA">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mtop30"></div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Jual Default:</label>
                                                    <input type="text" id="price_text"
                                                        class="form-control cust-control"
                                                        placeholder="Cth: 10.000">
                                                    <input type="hidden" id="price" name="price">
                                                </div>
                                                <small class="help-text">Isi dengan nominal harga jual produk / barang. Jika bisnis FnB biasanya harga untuk Dine In (Makan di Tempat)</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Delivery / Take Away (Opsional):</label>
                                                    <input type="text" id="price_ta" name="price_ta"
                                                        class="form-control cust-control"
                                                        placeholder="Cth: 11.000">   
                                                </div>
                                                <small class="help-text">Isi jika perlu harga yang berbeda jika pesanannya dikirim / dibawa pulang</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Marketplace (Opsional):</label>
                                                    <input type="text" id="price_mp" name="price_mp"
                                                        class="form-control cust-control"
                                                        placeholder="Cth: 14.000">
                                                </div>
                                                <small class="help-text">Isi jika perlu harga yang berbeda khusus untuk Shopee, Tokopedia, GrabFood, GoFood DLL</small>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Harga Custom (Opsional):</label>
                                                    <input type="text" id="price_cus" name="price_cus"
                                                        class="form-control cust-control"
                                                        placeholder="Cth: 15.000">
                                                </div>
                                                <small class="help-text">Isi jika perlu harga jual yang berbeda Lainnya</small>
                                            </div>
                                        </div>

                                        <div class="mtop30"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="detailProductInput" style="width: 4em; padding: 15px;">
                                                    <label class="form-check-label" style="padding-top: 8px;padding-left: 8px;" for="flexSwitchCheckDefault">Tambahkan Detail Produk (Jika Dibutuhkan)</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="detailProduct" style="display: none;">
                                            <div class="mtop30"></div>
                                            <div class="kartu bg-beige">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label>Tipe Produk:</label>
                                                            <select class="form-control cust-control" id="is_variant"
                                                                name="is_variant">
                                                               
                                                                <option value="1" <?php echo 'selected'; ?>>Single Product (Tanpa Varian)
                                                                </option>
                                                                <option value="2">Varian Product (Dengan Varian)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div id="varian-table-container"></div>
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
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Satuan Produk:</label>
                                                        <select class="form-control cust-control"
                                                            name="unit">
                                                            {{-- <option value="">Select Unit</option> --}}
                                                            @foreach ($units as $unit)
                                                                <option value="{{ $unit->unit_name }}">{{ $unit->unit_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Barcode Produk (Opsional):</label>
                                                        <input type="text" class="form-control cust-control"
                                                            placeholder="barcode" id="barcode" name="barcode">
    
                                                    </div>
                                                </div>
                    
<div class="col-md-3">
    <div class="form-group">
        <label>Berat Produk - in gram(g):</label>
        <input 
            type="number" 
            name="weight" 
            id="weight"
            class="form-control cust-control"
            value="{{ old('weight', 1000) }}"
            placeholder="Cth: 1000 (opsional)">
    </div>
</div>



                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Harga Bisa Diubah Kasir:</label>
                                                        <select name="is_editable" id="is_editable" class="form-control cust-control">
                                                            <option value="0">No</option>
                                                            <option value="1">Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mtop30"></div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <lable>Buffered Stock/Gunakan Stok:</lable>
                                                        <select class="form-control cust-control" id="buffered_stock"
                                                            name="buffered_stock">
    
                                                            <option <?php echo 'selected'; ?> value="0"> No - Jangan Gunakan Stok</option>
                                                            <option value="1">Yes - Gunakan Stok</option>
    
                                                        </select>
                                                        <small class="help-text">Buffer Stock / Gunakan Stok Produk Otomatis. Jika "YES" maka produk tidak akan bisa dipesan ketika stoknya 0*</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Stock Di Tahan / Stok Minimal</label>
                                                        <input readonly="readonly" type="number"
                                                            class="form-control cust-control" id="stock_alert"
                                                            name="stock_alert">
                                                    </div>
                                                    <small class="help-text">Jika stok produk 10 dan stok di tahan adalah 2, maka hanya 8 yang bisa dipesan</small>
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
                                                                <option value="1" <?php echo 'selected'; ?>>Beli Jadi</option>
                                                                <option value="2">Manufactured</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
    
                                                        <a onclick="tambah_bahan()" style="display: none; color: green;"
                                                            id="tambah-bahan-text" href="javascript:void(0);">(+)
                                                            Tambah Bahan</a>
    
                                                        <div class="form-group" id="manual_hpp" style="display: block;">
                                                            <label>COGS (HPP)</label>
                                                            <input value="0" onkeyup="onChangeCost()" type="text"
                                                                id="costtext" class="form-control cust-control"
                                                                placeholder="masukkan nilai hpp">
                                                            <input value="0" type="hidden" id="cost"
                                                                name="cost">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mtop10">
                                                    <div class="col-md-8">
                                                        <div id="composition-container"></div>
                                                    </div>
                                                </div>
                                                <div class="row mtop20">
                                                    <div class="col-md-12">
                                                        <div class="p-4 bg-soft-warning rounded-3">
                                                            <p class="fs-12 text-dark"><strong>Produk Beli Jadi</strong><br>"Produk Beli Jadi" adalah produk yang langsung dibeli dari pemasok dalam kondisi siap jual, tanpa perlu diproses lebih lanjut. Contohnya, Lampu, Minuman Kaleng, Baju Branded, dan lain-lain.
                                                            </p>
                                                            <p class="fs-12 text-dark"><strong>Produk Manufactured</strong><br>"Produk Manufactured" adalah produk yang memerlukan perakitan atau pencampuran bahan sebelum dijual. Contohnya, kopi latte yang diracik dari berbagai bahan seperti susu dan espresso sesuai resep. Sistem POS akan melacak penggunaan bahan baku dan menjaga stok tetap sesuai.
                                                            </p>
                                                            <p class="fs-12 text-dark"><strong>COGS (HPP)</strong><br>"Harga Pokok Penjualan" adalah biaya yang dikeluarkan untuk memproduksi produk barang atau jasa yang dijual. Jika produknya adalah produk fisik, maka HPP bisa di isikan 0 saja. Karena nanti akan terupdate otomatis oleh sistem.
                                                            </p>
                                                        </div>
                                                    </div>
    
                                                    <div class="col-md-12 mtop20" id="createdBy" style="display: none;">
                                                        <div class="form-group mb-3">
                                                            <label class="mb-3">Opsi Produk Dibuat :</label>
    
                                                            <div class="form-group">
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" onclick="bufferedStockChangeCreatedBy()"
                                                                        name="created_by" id="created_by_0"
                                                                        value="0" checked>
                                                                    <label class="form-check-label"
                                                                        for="created_by_0">Dibuat Terlebih Dahulu</label>
                                                                </div>
        
                                                                <div class="form-check form-check-inline">
                                                                    <input class="form-check-input" type="radio" onclick="bufferedStockChangeCreatedBy()"
                                                                        name="created_by" id="created_by_1"
                                                                        value="1">
                                                                    <label class="form-check-label"
                                                                        for="created_by_1">Dibuat By Pesanan</label>
                                                                </div>
                                                            </div>
                                                        </div>
    
                                                        <div class="col-md-12">
                                                            <div class="p-4 bg-soft-warning rounded-3">
                                                                <p class="fs-12 text-dark"><strong>Produk yang Dibuat Terlebih Dahulu (Make-to-Stock)</strong><br>adalah produk yang diproduksi dan disimpan dalam stok sebelum ada permintaan spesifik dari pelanggan. Contohnya: Roti, Kue, Fashion, Furniture, dan lain-lain
                                                                </p>
                                                                <p class="fs-12 text-dark"><strong>Produk yang Dibuat Berdasarkan Pesanan (Make-to-Order)</strong><br>adalah produk yang hanya diproduksi setelah ada pesanan dari pelanggan. Contohnya: nasi Goreng, Kentang Goreng, Kopi, dan lain-lain
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <div class="row mtop30">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Deskripsi Produk</label>
                                                        <textarea class="form-control cust-cuntrol" id="description" name="description" placeholder="Deskripsi Produk"></textarea>
                                                        <input type="hidden" id="desc_assist" name="desc_assist">
                                                    </div>
    
                                                </div>
                                                                                                    <div class="col-md-12">
                                                            <div class="p-4 bg-soft-warning rounded-3">
                                                                <p class="fs-12 text-dark"><strong>Bingung Nulis Deskripsi? Pakai Randu AI</strong><br>Ketik pada Randu AI "Buatkan saya deskripsi untuk produk Nasi Goreng Ikan Asin" maka Randu AI akan membuatkan deskripsi produk yang keren buat produkmu.
                                                                    </p>
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
                                                    </div>
                                                </div>
    
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

        <script>
            function generateSKU() {
                const nameInput = document.getElementById('name').value.trim();
                const skuInput = document.getElementById('sku');
                
                if (nameInput) {
                    const words = nameInput.split(' ');
                    let sku = '';
                    
                    words.forEach(word => {
                        if (word) {
                            sku += word.charAt(0).toUpperCase(); // Ambil huruf pertama dan jadikan huruf besar
                            sku += word.slice(1).replace(/[^\d]/g, ''); // Tambahkan angka yang ada setelah huruf pertama
                        }
                    });
        
                    // Membuat empat angka random
                    const randomNumbers = Math.floor(Math.random() * 9000) + 1000; // Pastikan selalu empat angka
        
                    // Membuat dua huruf random
                    const randomLetters = Array(2).fill(null).map(() => String.fromCharCode(Math.floor(Math.random() * 26) + 65)).join('');
        
                    // Gabungkan SKU dengan tanda hubung, angka random, dan huruf random
                    sku += '-' + randomNumbers + randomLetters;
                    
                    skuInput.value = sku;
                } else {
                    skuInput.value = ''; // Kosongkan jika Nama Produk dihapus
                }
            }
        </script>
    @endsection
