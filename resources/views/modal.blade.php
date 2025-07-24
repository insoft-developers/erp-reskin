@if (isset($view))
    @if ($view == 'jurnal')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog">
                <form id="form-tambah-jurnal" enctype="multipart/form-data">
                    <div class="modal-content">

                        @csrf
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Transaksi Baru</h5>
                            <button onclick="window.location.href='{{ url('journal_add') }}'"
                                class="btn d-flex align-items-center"
                                style="background-color: #ffffff; color: #1E3A8A; border: 1px solid #1E3A8A; padding: 10px 15px; border-radius: 8px; font-size: 14px; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                                <i class="feather-plus" style="margin-right: 8px; color: #1E3A8A;"></i> METODE DEBET
                                KREDIT
                            </button>

                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="date" id="tanggal_transaksi" value="{{ date('Y-m-d') }}"
                                            name="tanggal_transaksi" class="form-control cust-control">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <select class="form-control cust-control s2" id="jenis_transaksi"
                                            name="jenis_transaksi">
                                            <option value="">Pilih transaksi</option>
                                            @foreach ($list_transaksi as $item)
                                                @if ($item->id < 3 || $item->id > 6)
                                                    <option value="{{ $item->id }}">{{ $item->transaction_name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mtop20"></div>
                            <div class="form-group">
                                <label>Diterima Dari:</label>
                                <select class="form-control cust-control s2" id="receive_from" name="receive_from">
                                    <option value="">Pilih diterima dari</option>
                                </select>
                            </div>
                            <div class="mtop20"></div>
                            <div class="form-group">
                                <label>Disimpan Ke:</label>
                                <select class="form-control cust-control s2" id="save_to" name="save_to">
                                    <option value="">Pilih disimpan ke</option>
                                </select>
                            </div>
                            <div class="mtop20"></div>
                            <div class="form-group">
                                <label>Keterangan:</label>
                                <input type="text" class="form-control cust-control" id="keterangan"
                                    name="keterangan" placeholder="Nama Transaksi">

                            </div>
                            <div class="mtop20"></div>
                            <div class="form-group">
                                <label>Nominal:</label>
                                <input type="text" class="form-control cust-control" id="nominal_text"
                                    name="nominal_text" placeholder="0">
                                <input type="hidden" id="nominal" name="nominal" value="0">
                            </div>
                            <div class="mtop20"></div>
                            <div class="form-group">
                                <label>Upload Dokumen Transaksi:</label>
                                <input type="file" class="form-control cust-control" id="image" name="image"
                                    accept=".jpg, .jpeg, .png">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="modal-lihat">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #2f467a;">
                        <h5 class="modal-title" style="color:white;">Lihat Jurnal</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="content-lihat-saldo-awal"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Oke</button>
                    </div>
                </div>
            </div>
        </div>
    @elseif($view == 'product-category')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-add-category" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nama Kategori</label>
                                        <input type="text" name="name" id="name"
                                            class="form-control cust-control" placeholder="Nama Kategori"
                                            oninput="generateCategoryCode()">
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Kode Kategori</label>
                                        <input type="text" name="code" id="code"
                                            class="form-control cust-control"
                                            placeholder="Masukkan Kode Kategori (Opsional)">
                                    </div>
                                </div>
                            </div>




                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Foto Kategori (500x500 Pixel Ukuran Persegi)</label>
                                        <input type="file" name="image" id="image"
                                            class="form-control cust-control" accept=".jpg, .jpeg, .png, .webp">
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Keterangan (Opsional)</label>
                                        <textarea class="form-control cust-control" style="height: 120px;" id="description" name="description"
                                            placeholder="Masukkan keterangan"></textarea>
                                    </div>
                                </div>

                            </div>

                            <div class="mtop20"></div>
                            <script>
                                function generateCategoryCode() {
                                    const nameInput = document.getElementById('name').value.trim();
                                    const codeInput = document.getElementById('code');

                                    if (nameInput) {
                                        const vowels = /[aeiouAEIOU]/g; // Regex untuk huruf vokal
                                        let categoryCode = nameInput.replace(vowels, '').replace(/\s+/g, '')
                                            .toUpperCase(); // Hapus vokal, spasi, dan ubah menjadi huruf besar

                                        // Membuat empat angka random
                                        const randomNumbers = Math.floor(Math.random() * 9000) + 1000; // Pastikan selalu empat angka

                                        // Membuat dua huruf random
                                        const randomLetters = Array(2).fill(null).map(() => String.fromCharCode(Math.floor(Math.random() * 26) +
                                            65)).join('');

                                        // Gabungkan kode kategori dengan tanda hubung, angka random, dan huruf random
                                        categoryCode += '-' + randomNumbers + randomLetters;

                                        codeInput.value = categoryCode;
                                    } else {
                                        codeInput.value = ''; // Kosongkan jika Nama Kategori dihapus
                                    }
                                }
                            </script>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'product-list')
        <div class="modal fade" id="modal-product-detail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #2f467a;">
                        <h5 class="modal-title" style="color:white;">Detail Product</h5>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-12">
                                <div id="content-product-detail"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Oke</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modal-upload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-upload-product" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}

                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Upload Data Product</h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach your File..</label>
                                        <input type="file" name="file" id="file"
                                            class="form-control cust-control"
                                            placeholder="Upload your excel file here..." accept=".xls, .xlsx">
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tips Upload file Bahan Baku</strong>
                                        <ol>
                                            <li>Download template file excel untuk upload <a
                                                    onclick="download_template_upload()"
                                                    href="javascript:void();">disini.</a>
                                            </li>
                                            <li>Mohon tidak mengubah-ubah judul pada kolom paling atas file excel pada
                                                template.</li>
                                            <li>Jumlah Maksimal Produk yang boleh diupload untuk 1x upload adalah 2.000
                                                (dua
                                                ribu) produk.</li>

                                            <li>Untuk pengisian kolom category silahkan diisi sesuai kebutuhan
                                                user</li>
                                            <li>Untuk pengisian kolom satuan silahkan lihat list di dalam komentar pada
                                                kolom satuan file excel. satuan yang diperbolehkan hanya yang terdapat
                                                pada
                                                list. Jika user mengisi dengan satuan yang tidak terdaftar maka sistem
                                                otomatis akan mengisi dengan satuan default.</li>

                                            <li>Pastikan semua kolom terisi dan tidak ada yang kosong.</li>
                                        </ol>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif ($view == 'main-supplier')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="form-add-supplier" method="POST">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Tambah Data Supplier</h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name"
                                            class="form-control cust-control" placeholder="Nama Supplier">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="contact_name" id="contact_name"
                                            class="form-control cust-control"
                                            placeholder="Nama Kontak PIC (Opsional)">
                                    </div>
                                </div>
                            </div>

                            <div class="mtop20"></div>
                            <div class="row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control cust-control" id="phone"
                                        name="phone" placeholder="No Telepon (Opsional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="email" class="form-control cust-control" id="email"
                                        name="email" placeholder="Alamat Email (Opsional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control cust-control" id="fax"
                                        name="fax" placeholder="No FAX (Opsional)">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control cust-control" id="website"
                                        name="website" placeholder="Website (Opsional)">
                                </div>
                            </div>
                            <div class="mtop20"></div>

                            <div class="row">
                                <div class="col-md-6">
                                    <textarea style="height: 120px;" class="form-control cust-control" name="jalan1" id="jalan1"
                                        placeholder="Alamat Lengkap (Opsional)"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <textarea style="height: 120px;" class="form-control cust-control" name="jalan2" id="jalan2"
                                        placeholder="Alamat Tambahan (Opsional)"></textarea>
                                </div>
                            </div>
                            <div class="mtop20"></div>

                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control cust-control" id="postal_code"
                                        name="postal_code" placeholder="Kode Pos (Opsional)">
                                </div>

                                <div class="col-md-6">
                                    <input type="text" class="form-control cust-control" id="country"
                                        name="country" placeholder="Negara (Opsional)">
                                </div>
                            </div>
                            <div class="mtop20"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <select class="form-control cust-control" id="province" name="province">
                                        <option value="">Pilih provinsi/kota/kecamatan</option>
                                        @foreach ($wilayah as $d)
                                            <option
                                                value="{{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}">
                                                {{ $d->provinsi }}, {{ $d->kabupaten }}, {{ $d->distrik }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'branch')
        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-tambah">
            <div class="modal-dialog">
                <form id="form-tambah-cabang">
                    <div class="modal-content">
                        @csrf
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Cabang Baru</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mtop20">
                                <label>Nama Cabang:</label>
                                <input type="text" name="name" id="name"
                                    class="form-control cust-control" placeholder="Masukkan nama cabang">
                            </div>
                            <div class="form-group mtop20">
                                <label>Nomor Telepon:</label>
                                <input type="text" name="phone" id="phone"
                                    class="form-control cust-control" placeholder="Masukkan nomor telepon">
                            </div>
                            <div class="form-group mtop20">
                                <label>Alamat:</label>
                                <textarea name="address" id="address" cols="30" class="form-control" rows="10"></textarea>
                            </div>
                            <div class="form-group mtop20" id="formSelect">
                                <label>Kecamatan, Kabupaten , Provinsi:</label>
                                <select class="form-control cust-control" id="district" name="district_id">

                                </select>
                                @if ($errors->has('district_id'))
                                    <span class="help-block">{{ $errors->first('district_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @elseif($view == 'staff')
        <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modal-staff">
            <div class="modal-dialog">
                <form id="form-tambah-staff">
                    <div class="modal-content">
                        @csrf
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Staff Baru</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-group mtop20">
                                <label>Nama Staff:</label>
                                <input type="text" name="fullname" id="name"
                                    class="form-control cust-control" placeholder="Masukkan nama staff">
                            </div>
                            <div class="form-group mtop20">
                                <label>Email:</label>
                                <input type="email" name="email" id="email"
                                    class="form-control cust-control" placeholder="Masukkan email">
                            </div>
                            <div class="form-group mtop20" id="formSelectBranchStaff">
                                <label>Cabang:</label>
                                <select name="branch_id" id="selectBranchStaff" class="form-control cust-control">
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mtop20" id="formSelectPositionStaff">
                                <label>Posisi:</label>
                                <select name="position_id" class="form-control" id="selectPositionStaff"></select>
                            </div>
                            <div class="form-group mtop20">
                                <label>Username:</label>
                                <input name="username" id="username" class="form-control cust-control"
                                    placeholder="Masukkan username">
                                <span id="username-feedback"></span>
                            </div>
                            <div class="form-group mtop20">
                                <label>Password:</label>
                                <input name="password" id="password" type="password"
                                    class="form-control cust-control" placeholder="Masukkan password">
                            </div>
                            <div class="form-group mtop20">
                                <label>Nomor Telepon:</label>
                                <input name="phone" id="phone" type="numeric"
                                    onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                    class="form-control cust-control" placeholder="Masukkan nomor telepon"
                                    maxlength="12">
                            </div>
                            <div class="form-group mtop20">
                                <label>Tanggal Mulai Kerja:</label>
                                <input name="start_date" type="date" id="start_date"
                                    class="form-control cust-control">
                            </div>
                            <div class="form-group mtop20">
                                <label>Jam Masuk:</label>
                                <input name="clock_in" type="time" id="clock_in"
                                    class="form-control cust-control">
                            </div>
                            <div class="form-group mtop20">
                                <label>Jam Pulang:</label>
                                <input name="clock_out" type="time" id="clock_out"
                                    class="form-control cust-control">
                            </div>

                            <div class="form-group mtop20">
                                <div class="accordion-item mb-3">
                                    <h2 class="accordion-header" id="panelsStayOpen-headingFour">
                                        <button class="accordion-button collapsed btn btn-primary" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseFour"
                                            aria-expanded="false" aria-controls="panelsStayOpen-collapseFour">
                                            Pengaturan hari Libur
                                        </button>
                                    </h2>
                                    <div id="panelsStayOpen-collapseFour" class="accordion-collapse collapse show"
                                        aria-labelledby="panelsStayOpen-headingFour">
                                        <div class="accordion-body">
                                            <div class="mb-3 row">
                                                <label class="col-sm-2 col-form-label">
                                                    Aktifkan Hari Libur
                                                </label>
                                                <div class="col-sm-10">
                                                    <div id="shippingOption">
                                                        <label class="col-sm-2 col-form-label">
                                                            Hari Libur
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Senin">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Senin" value="Senin">
                                                                        <label class="form-check-label"
                                                                            for="Senin">Senin</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Selasa">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Selasa" value="Selasa">
                                                                        <label class="form-check-label"
                                                                            for="Selasa">Selasa</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Rabu">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Rabu" value="Rabu">
                                                                        <label class="form-check-label"
                                                                            for="Rabu">Rabu</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Kamis">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Kamis" value="Kamis">
                                                                        <label class="form-check-label"
                                                                            for="Kamis">Kamis</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Jumat">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Jumat" value="Jumat">
                                                                        <label class="form-check-label"
                                                                            for="Jumat">Jumat</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Sabtu">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Sabtu" value="Sabtu">
                                                                        <label class="form-check-label"
                                                                            for="Sabtu">Sabtu</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-check shipping"
                                                                        data-method="Minggu">
                                                                        <input class="form-check-input"
                                                                            name="holiday[]" type="checkbox"
                                                                            id="Minggu" value="Minggu">
                                                                        <label class="form-check-label"
                                                                            for="Minggu">Minggu</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mtop20">
                                <label>Status</label>
                                <select name="is_active" class="form-control cust-control" id="is_active">
                                    <option value="1" selected>Aktif</option>
                                    <option value="0">Non Aktif</option>
                                </select>
                            </div>
                            <div class="form-group mtop20">
                                <label>PIN</label>
                                <input name="pin" type="numeric" id="pin"
                                    onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"
                                    value="123123" class="form-control cust-control" placeholder="Masukkan nomor pin"
                                    maxlength="6">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @elseif($view == 'main-material')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="form-add-material" method="POST">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nama Bahan Baku</label>
                                        <input type="text" name="material_name" id="material_name"
                                            class="form-control cust-control" placeholder="Nama Bahan Baku"
                                            oninput="generateMaterialSKU()">
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>SKU Bahan Baku</label>
                                        <input type="text" name="sku" id="sku"
                                            class="form-control cust-control" placeholder="Masukkan SKU">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Kategori Material:</label>
                                        <select name="category_id" id="category_id"
                                            class="form-control cust-control">
                                            <option value="">Pilih Kategori Material</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->category_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Satuan:</label>
                                        <select name="unit" id="unit" class="form-control cust-control">
                                            <option value="">Pilih Satuan</option>
                                            @foreach ($units as $unit)
                                                <option value="{{ $unit->unit_name }}">{{ $unit->unit_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supplier:</label>
                                        <select name="supplier_id" id="supplier_id"
                                            class="form-control cust-control">
                                            <option value="">Pilih Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Stok Minimal:</label>
                                        <input type="number" name="min_stock" id="min_stock"
                                            class="form-control cust-control" placeholder="Minimal stock">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Stok Ideal:</label>
                                        <input type="number" name="ideal_stock" id="ideal_stock"
                                            class="form-control cust-control" placeholder="Stock Ideal">
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Keterangan:</label>
                                        <textarea class="form-control cust-control" style="height: 120px;" id="description" name="description"
                                            placeholder="Masukkan keterangan"></textarea>
                                    </div>
                                </div>
                                <div class="row mtop20">
                                    <div class="col-md-12">
                                        <div class="p-4 bg-soft-warning rounded-3">
                                            <p class="fs-12 text-dark"><strong>Tips Mengisi Bahan
                                                    Baku</strong><br>Gunakan
                                                satuan bahan baku dalam takaran yang dipakai dalam resep/produksi
                                                barang.
                                                Contohnya: Untuk membuat 1 Roti butuh 300gr tepung, maka masukkan satuan
                                                gram (gr) untuk bahan baku tepung. Meskipun saat berbelanja di supplier
                                                nanti satuannya adalah kilogram (Kg)
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-upload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-upload-material" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}

                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Upload Data Bahan Baku</h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach your File..</label>
                                        <input type="file" name="file" id="file"
                                            class="form-control cust-control"
                                            placeholder="Upload your excel file here..." accept=".xls, .xlsx">
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tips Upload file Bahan Baku</strong>
                                        <ol>
                                            <li>Download template file excel untuk upload <a
                                                    onclick="download_template_upload()"
                                                    href="javascript:void();">disini.</a>
                                            </li>
                                            <li>Mohon tidak mengubah-ubah judul pada kolom paling atas file excel pada
                                                template.</li>
                                            <li>Jumlah maksimal material yang boleh diupload untuk 1x upload adalah
                                                2.000
                                                (dua ribu) material.</li>
                                            <li>SKU setiap produk harus unik (tidak boleh sama dengan SKU produk
                                                lainnya).
                                            </li>
                                            <li>Untuk pengisian kolom category dan supplier silahkan diisi sesuai
                                                kebutuhan
                                                user</li>
                                            <li>Untuk pengisian kolom satuan silahkan lihat list di dalam komentar pada
                                                kolom satuan file excel. satuan yang diperbolehkan hanya yang terdapat
                                                pada
                                                list. Jika user mengisi dengan satuan yang tidak terdaftar maka sistem
                                                otomatis akan mengisi dengan satuan default.</li>
                                            <li>Pastikan semua kolom terisi dan tidak ada yang kosong.</li>
                                        </ol>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'product-purchase')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="form-tambah" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="kartu">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Supplier:</label>
                                            <select class="form-control cust-control" id="supplier_id"
                                                name="supplier_id">
                                                <option value="">Pilih Supplier</option>
                                                @foreach ($supplier as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama / No.Refrensi:</label>
                                            <input type="text" class="form-control cust-control" id="reference"
                                                name="reference"
                                                placeholder="masukkan Nama / No. refrensi transaksi anda">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Dokumen Transaksi:</label>
                                            <input type="file" class="form-control cust-control" id="image"
                                                name="image" accept=" .jpg, .jpeg, .png">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal Transaksi:</label>
                                            <input type="date" value="{{ date('Y-m-d') }}"
                                                class="form-control cust-control" id="transaction_date"
                                                name="transaction_date">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipe Pembayaran:</label>
                                            <select class="form-control cust-control" id="payment_type"
                                                name="payment_type">
                                                <option value="0">Tunai</option>
                                                <option value="1">Utang</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bayar Pakai:</label>
                                            <select class="form-control cust-control" id="account_id"
                                                name="account_id">
                                                <option value="">Pilih</option>
                                                @foreach ($accounts as $account)
                                                    <option
                                                        value="{{ $account->id }}_{{ $account->account_code_id }}">
                                                        {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jumlah Produk:</label>
                                            <input type="number" class="form-control cust-control"
                                                id="product_count" name="product_count" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Hanya Untuk Produk "Beli Jadi" dan "Gunakan
                                                Stok
                                                = YES"</strong><br>Produk atau barang yang bisa ditransaksikan dalam
                                            menu
                                            ini hanya produk dengan tipe "Beli Jadi" dan tipe "Gunakan Stok = YES",
                                            Untuk
                                            tipe produk Manufaktur silakan gunakan menu Transaksi Buat Produk
                                            (Manufaktur)<br><br>Menu ini juga bisa dipakai untuk banyak produk dalam 1
                                            kali
                                            transaksi, asalkan masih dalam 1 nota pembelian atau 1 nota transaksi.
                                        </p>


                                    </div>
                                </div>
                            </div>

                            <div class="kartu mtop20 bg-beige" id="product-container">
                                <div class='radio-type-input'>
                                    <input class="rb-input" type='radio' id='radio_1' checked="checked"
                                        name='type' value='1'><label for="radio_1">&nbsp;Input Berdasarkan
                                        Harga
                                        Satuan</label>
                                    <input class="rb-input" style="margin-left:20px;" type='radio' id='radio_2'
                                        name='type' value='2'><label for="radio_2">&nbsp;Input Berdasarkan
                                        Harga
                                        Subtotal</label>
                                </div>
                                <h5>Masukkan Item Produk Yang Dibeli</h5>
                                <div class="row" id="label_komposisi">
                                    <div class="col-md-4"><strong>Nama Produk</strong></div>
                                    <div class="col-md-2"><strong>Harga Satuan</strong></div>
                                    <div class="col-md-2"><strong>Quantity Pembelian</strong></div>
                                    <div class="col-md-3"><strong>Harga Total Pembelian</strong></div>
                                </div>
                                <div class="row bariss" id="bariss_1">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control cust-control select-item" id="product_id_1"
                                                name="product_id[]">
                                                <option value="">Pilih Produk</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }} -
                                                        {{ $product->unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-md-2" id="bagian_unit_1">
                                        <div class="form-group">
                                            <input onkeyup="onchange_unit_price(1)" type="text"
                                                class="form-control cust-control" id="unit_price_text_1"
                                                placeholder="Harga Satuan">
                                            <input type="hidden" id="unit_price_1" name="unit_price[]">
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input onkeyup="onchange_quantity(1)" type="text"
                                                class="form-control cust-control" id="quantity_text_1"
                                                placeholder="Quantity Pembelian">
                                            <input type="hidden" id="quantity_1" name="quantity[]">
                                        </div>
                                    </div>


                                    <div class="col-md-3" id="bagian_total_1">
                                        <div class="form-group">
                                            <input type="text" class="form-control cust-control"
                                                id="purchase_amount_text_1" onkeyup="onchange_purchase_amount(1)"
                                                placeholder="Harga Total Pembelian" readonly>
                                            <input class="purchase-amount" type="hidden" id="purchase_amount_1"
                                                name="purchase_amount[]">
                                        </div>
                                    </div>


                                    <div class="col-md-1 button-product-action">
                                        <center><a title="Tambah Produk" href="javascript:void(0);"
                                                onclick="tambah_item()"
                                                class="avatar-text avatar-md bg-success text-white"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                    class="fa fa-plus"></i></a></center>
                                        {{-- <center><a style="margin-left: 5px;" title="Hapus Produk"
                                            href="javascript:void(0);" onclick="hapus_item(1)"
                                            class="avatar-text avatar-md bg-danger text-white"
                                            data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                class="fa fa-trash"></i></a></center> --}}
                                    </div>

                                </div>
                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Pajak Pembelian (+)</label>
                                            <input onkeyup="onchange_tax()" type="text"
                                                class="form-control cust-control" id="tax_text"
                                                placeholder="Pajak (Rupiah)">
                                            <input type="hidden" id="tax" name="tax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan / Diskon Pembelian (-)</label>
                                            <input onkeyup="onchange_discount()" type="text"
                                                class="form-control cust-control" id="discount_text"
                                                placeholder="Diskon/Potongan">
                                            <input type="hidden" id="discount" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Lain-lain Pembelian (+)</label>
                                            <input onkeyup="onchange_other_expense()" type="text"
                                                class="form-control cust-control" id="other_expense_text"
                                                placeholder="Ongkir / Ongkos Kerja DLL">
                                            <input type="hidden" id="other_expense" name="other_expense">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Total Transaksi</label>
                                            <input type="text" class="form-control" id="total_transaksi_text"
                                                placeholder="Total transaski" readonly>
                                            <input type="hidden" id="total_transaksi" name="total_purchase">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">

                                    <div class="p-4 bg-soft-success rounded-3">
                                        <p class="fs-12 text-dark"><strong>Penjelasan Fitur Transaksi Beli
                                                Produk</strong><br>Transaksi ini digunakan saat dilakukan
                                            pembelian/kulak
                                            dari supplier yang mana stok produk akan langsung bertambah setelah
                                            transaksi
                                            ini sukses dibuat.
                                            <br><br>
                                            Harga Pokok Penjualan / COGS pada Produk yang dibuat akan otomatis tergitung
                                            dari Total Transaksi dibagi dengan jumlah produk yang dibeli. Contoh: Jika
                                            Total
                                            Transaksi = Rp100.000 dan produk yang dibeli adalah 5pcs maka masing masing
                                            produknya HPP nya adalah 100.000 / 5 = Rp20.000
                                            <br><br>
                                            Gunakan tipe Utang jika sistem pembeliannya menggunakan sistem Utang, Fitur
                                            utang juga bisa digunakan untuk sistem konsiyasi bisnis / Supplier
                                            menitipkan
                                            produk untuk dijual.
                                        </p>


                                    </div>
                                </div>
                            </div>

                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="modal fade" id="modal-upload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-upload-product-purchase" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}

                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Upload Transaksi Pembelian Barang Jadi</h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image-upload" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach your File..</label>
                                        <input type="file" name="file" id="file"
                                            class="form-control cust-control"
                                            placeholder="Upload your excel file here..." accept=".xls, .xlsx"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tips Upload file Pembelian Barang
                                                Jadi</strong>
                                        <ol>
                                            <li>Download template file excel untuk upload <a
                                                    onclick="download_template_upload()"
                                                    href="javascript:void();">disini.</a>
                                            </li>
                                            <li>Mohon tidak mengubah-ubah judul pada kolom paling atas file excel pada
                                                template.</li>
                                            <li>Kolom yang diisi hanya yang berwarna kuning.</li>
                                            <li>Isi tanggal transaksi dan jumlah pembelian</li>
                                            <li>Kolom yang jumlah pembeliannya kosong tidak akan di proses ke data
                                                pembelian
                                            </li>
                                            <li>Untuk Data Supplier akan dibuatkan otomatis oleh sistem ke Supplier
                                                Gudang
                                                Persediaan Barang Jadi.</li>
                                            <li>Jurnal akan tersinkronisasi otomatis pada akun kas di kredit dan akun
                                                persediaan produk di debet. jika ingin melakukan penyesuaian silahkan
                                                lakukan edit jurnal</li>
                                            <li>Pastikan data yang terisi sudah benar dan sesuai dengan data pembelian
                                                Anda.
                                            </li>
                                        </ol>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'material-purchase')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="form-tambah" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">

                            <div class="kartu">
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Supplier:</label>
                                            <select class="form-control cust-control" id="supplier_id"
                                                name="supplier_id">
                                                <option value="">Pilih Supplier</option>
                                                @foreach ($supplier as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama / No.Refrensi:</label>
                                            <input type="text" class="form-control cust-control" id="reference"
                                                name="reference"
                                                placeholder="masukkan Nama / No.refrensi transaksi anda">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Foto Dokumen Transaksi:</label>
                                            <input type="file" class="form-control cust-control" id="image"
                                                name="image" accept=" .jpg, .jpeg, .png">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="kartu">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal Transaksi :</label>
                                            <input type="date" value="{{ date('Y-m-d') }}"
                                                class="form-control cust-control" id="transaction_date"
                                                name="transaction_date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tipe Pembayaran :</label>
                                            <select class="form-control cust-control" id="payment_type"
                                                name="payment_type">
                                                <option value="0">Tunai</option>
                                                <option value="1">Utang</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bayar Pakai:</label>
                                            <select class="form-control cust-control" id="account_id"
                                                name="account_id">
                                                <option value="">Pilih</option>
                                                @foreach ($accounts as $account)
                                                    <option
                                                        value="{{ $account->id }}_{{ $account->account_code_id }}">
                                                        {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jumlah Produk:</label>
                                            <input type="number" class="form-control cust-control"
                                                id="product_count" name="product_count" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige" id="product-container">
                                <div class='radio-type-input'>
                                    <input class="rb-input" type='radio' id='radio_1' checked="checked"
                                        name='type' value='1'><label for="radio_1">&nbsp;Input Berdasarkan
                                        Harga
                                        Satuan</label>
                                    <input class="rb-input" style="margin-left:20px;" type='radio' id='radio_2'
                                        name='type' value='2'><label for="radio_2">&nbsp;Input Berdasarkan
                                        Harga
                                        Subtotal</label>
                                </div>
                                <h5>Masukkan item bahan baku</h5>
                                <div class="row" id="label_komposisi">
                                    <div class="col-md-4"><strong>Nama Bahan Baku</strong></div>
                                    <div class="col-md-2"><strong>Harga Satuan</strong></div>
                                    <div class="col-md-2"><strong>Quantity Pembelian</strong></div>
                                    <div class="col-md-3"><strong>Harga Total Pembelian</strong></div>
                                </div>
                                <div class="row bariss" id="bariss_1">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select class="form-control cust-control select-item" id="product_id_1"
                                                name="product_id[]">
                                                <option value="">Pilih Bahan Baku</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->material_name }} -
                                                        {{ $material->unit }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>




                                    <div class="col-md-2" id="bagian_unit">
                                        <div class="form-group">
                                            <input onkeyup="onchange_unit_price(1)" type="text"
                                                class="form-control cust-control" id="unit_price_text_1"
                                                placeholder="Harga Satuan">
                                            <input type="hidden" id="unit_price_1" name="unit_price[]">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input onkeyup="onchange_quantity(1)" type="text"
                                                class="form-control cust-control" id="quantity_text_1"
                                                placeholder="Quantity Pembelian">
                                            <input type="hidden" id="quantity_1" name="quantity[]">
                                        </div>
                                    </div>

                                    <div class="col-md-3" id="bagian_total">
                                        <div class="form-group">
                                            <input type="text" class="form-control cust-control"
                                                id="purchase_amount_text_1" onkeyup="onchange_purchase_amount(1)"
                                                placeholder="Harga total pembelian" readonly>
                                            <input class="purchase-amount" type="hidden" id="purchase_amount_1"
                                                name="purchase_amount[]">
                                        </div>
                                    </div>



                                    <div class="col-md-1 button-product-action">
                                        <center><a title="Tambah Produk" href="javascript:void(0);"
                                                onclick="tambah_item()"
                                                class="avatar-text avatar-md bg-success text-white"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                    class="fa fa-plus"></i></a></center>

                                    </div>

                                </div>
                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Pajak Pembelian (+)</label>
                                            <input onkeyup="onchange_tax()" type="text"
                                                class="form-control cust-control" id="tax_text"
                                                placeholder="Pajak (Rupiah)">
                                            <input type="hidden" id="tax" name="tax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan / Diskon Pembelian (-)</label>
                                            <input onkeyup="onchange_discount()" type="text"
                                                class="form-control cust-control" id="discount_text"
                                                placeholder="Diskon/Potongan">
                                            <input type="hidden" id="discount" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Lain-lain Pembelian (+)</label>
                                            <input onkeyup="onchange_other_expense()" type="text"
                                                class="form-control cust-control" id="other_expense_text"
                                                placeholder="Ongkir / Ongkos Kerja DLL">
                                            <input type="hidden" id="other_expense" name="other_expense">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Total Transaksi</label>
                                            <input type="text" class="form-control" id="total_transaksi_text"
                                                placeholder="Total transaski" readonly>
                                            <input type="hidden" id="total_transaksi" name="total_purchase">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">

                                    <div class="p-4 bg-soft-success rounded-3">
                                        <p class="fs-12 text-dark"><strong>Penjelasan Fitur Transaksi Beli Bahan
                                                Baku</strong><br>Transaksi ini digunakan saat dilakukan pembelian/kulak
                                            dari
                                            supplier yang mana stok bahan baku akan langsung bertambah setelah transaksi
                                            ini
                                            sukses dibuat.
                                            <br><br>
                                            Harga Pokok Penjualan / COGS pada Produk yang dibuat akan otomatis tergitung
                                            dari Total Transaksi dibagi dengan jumlah bahan baku yang dibeli. Contoh:
                                            Jika
                                            Total Transaksi = Rp100.000 dan bahan baku yang dibeli adalah 5pcs maka
                                            masing
                                            masing produknya HPP nya adalah 100.000 / 5 = Rp20.000
                                            <br><br>
                                            Gunakan tipe Utang jika sistem pembeliannya menggunakan sistem Utang.


                                    </div>
                                </div>
                            </div>

                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-upload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-upload-material-purchase" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}

                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Upload Transaksi Pembelian Bahan Baku</h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image-upload" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach your File..</label>
                                        <input type="file" name="file" id="file"
                                            class="form-control cust-control"
                                            placeholder="Upload your excel file here..." accept=".xls, .xlsx"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tips Upload file Pembelian Bahan
                                                Baku</strong>
                                        <ol>
                                            <li>Download template file excel untuk upload <a
                                                    onclick="download_template_upload()"
                                                    href="javascript:void();">disini.</a>
                                            </li>
                                            <li>Mohon tidak mengubah-ubah judul pada kolom paling atas file excel pada
                                                template.</li>
                                            <li>Kolom yang diisi hanya yang berwarna kuning.</li>
                                            <li>Isi tanggal transaksi (format tanggal : 2024-10-12)/jika tanggal
                                                dikosongkan
                                                maka akan tersimpan tanggal saat uplod. dan isi jumlah pembelian</li>
                                            <li>Kolom yang jumlah pembeliannya kosong tidak akan di proses ke data
                                                pembelian
                                            </li>
                                            <li>Untuk Data Supplier akan dibuatkan otomatis oleh sistem ke Supplier
                                                Gudang
                                                Persediaan Bahan Baku.</li>
                                            <li>Jurnal akan tersinkronisasi otomatis pada akun kas di kredit dan akun
                                                persediaan Bahan Baku di debet. jika ingin melakukan penyesuaian
                                                silahkan
                                                lakukan edit jurnal</li>
                                            <li>Pastikan data yang terisi sudah benar dan sesuai dengan data pembelian
                                                Anda.
                                            </li>
                                        </ol>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'inter-purchase')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="form-tambah" method="POST">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="kartu">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Product Setengah Jadi :</label>
                                            <select class="form-control cust-control" id="product_id"
                                                name="product_id">
                                                <option value="">Pilih</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->product_name }} -
                                                        {{ $material->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal Transaksi :</label>
                                            <input type="date" value="{{ date('Y-m-d') }}"
                                                class="form-control cust-control" id="transaction_date"
                                                name="transaction_date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bayar Biaya Pakai:</label>
                                            <select class="form-control cust-control" id="account_id"
                                                name="account_id">
                                                <option value="">Pilih</option>
                                                @foreach ($accounts as $account)
                                                    <option
                                                        value="{{ $account->id }}_{{ $account->account_code_id }}">
                                                        {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Jumlah yang di Produksi:</label>
                                            <input type="number" class="form-control cust-control"
                                                id="product_count" name="product_count" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tekan Tombol Tab atau Enter pada Keyboard
                                                Untuk
                                                Memproses Komposisi</strong><br>Setelah menentukan Jumlah yang di
                                            Produksi
                                            (Qty), tekan tombol Tab atau Enter pada keyboard untuk memproses komposisi
                                            otomatis.
                                        </p>


                                    </div>
                                </div>
                            </div>


                            <div class="kartu mtop20 bg-beige">
                                <h5>Komposisi Barang Setengah Jadi</h5>
                                <div id="product-container"></div>

                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Pajak Produksi (+)</label>
                                            <input onkeyup="onchange_tax()" type="text"
                                                class="form-control cust-control" id="tax_text"
                                                placeholder="Pajak (Rupiah)">
                                            <input type="hidden" id="tax" name="tax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan / Diskon Produksi (-)</label>
                                            <input onkeyup="onchange_discount()" type="text"
                                                class="form-control cust-control" id="discount_text"
                                                placeholder="Diskon/Potongan">
                                            <input type="hidden" id="discount" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Lain-lain Produksi (+)</label>
                                            <input onkeyup="onchange_other_expense()" type="text"
                                                class="form-control cust-control" id="other_expense_text"
                                                placeholder="Ongkir / Ongkos Kerja DLL">
                                            <input type="hidden" id="other_expense" name="other_expense">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Total Transaksi</label>
                                            <input type="text" class="form-control" id="total_transaksi_text"
                                                placeholder="Total transaski" readonly>
                                            <input type="hidden" id="total_transaksi" name="total_purchase">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">

                                    <div class="p-4 bg-soft-success rounded-3">
                                        <p class="fs-12 text-dark"><strong>Penjelasan Fitur Transaksi Buat Barang
                                                Setengah
                                                Jadi</strong><br>Stok produk akan langsung bertambah setelah transaksi
                                            ini
                                            sukses dibuat, dan stok Bahan Baku serta Barang Setengah Jadi akan berkurang
                                            otomatis sesuai dengan komposisi bahan pada produk.
                                            <br>
                                            Harga Pokok Penjualan / COGS pada Barang yang dibuat akan otomatis tergitung
                                            dari Total Transaksi dibagi dengan jumlah barang yang di produksi. Contoh:
                                            Jika
                                            Total Transaksi = Rp100.000 dan barang yang di produksi adalah 5pcs maka
                                            masing
                                            masing barang HPP nya adalah 100.000 / 5 = Rp20.000
                                        </p>


                                    </div>
                                </div>
                            </div>



                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'product-manufacture')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="form-tambah" method="POST">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="kartu">

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pilih Produk:</label>
                                            <select class="form-control cust-control" id="product_id"
                                                name="product_id">
                                                <option value="">Pilih</option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">{{ $material->name }} -
                                                        {{ $material->unit }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal Transaksi:</label>
                                            <input type="date" value="{{ date('Y-m-d') }}"
                                                class="form-control cust-control" id="transaction_date"
                                                name="transaction_date">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Bayar Biaya Pakai:</label>
                                            <select class="form-control cust-control" id="account_id"
                                                name="account_id">
                                                <option value="">Pilih</option>
                                                @foreach ($accounts as $account)
                                                    <option
                                                        value="{{ $account->id }}_{{ $account->account_code_id }}">
                                                        {{ $account->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Jumlah yang di Produksi:</label>
                                            <input type="number" class="form-control cust-control"
                                                id="product_count" name="product_count" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tekan Tombol Tab atau Enter pada Keyboard
                                                Untuk
                                                Memproses Komposisi</strong><br>Setelah menentukan Jumlah yang di
                                            Produksi
                                            (Qty), tekan tombol Tab atau Enter pada keyboard untuk memproses komposisi
                                            otomatis.
                                        </p>


                                    </div>
                                </div>
                            </div>

                            <div class="kartu mtop20 bg-beige">
                                <h5>Komposisi Barang / Resep Menu</h5>
                                <div id="product-container"></div>

                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Pajak Produksi (+)</label>
                                            <input onkeyup="onchange_tax()" type="text"
                                                class="form-control cust-control" id="tax_text"
                                                placeholder="Pajak (Rupiah)">
                                            <input type="hidden" id="tax" name="tax">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan / Diskon Produksi (-)</label>
                                            <input onkeyup="onchange_discount()" type="text"
                                                class="form-control cust-control" id="discount_text"
                                                placeholder="Diskon/Potongan">
                                            <input type="hidden" id="discount" name="discount">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Biaya Lain-lain Produksi (+)</label>
                                            <input onkeyup="onchange_other_expense()" type="text"
                                                class="form-control cust-control" id="other_expense_text"
                                                placeholder="Ongkir / Ongkos Kerja DLL">
                                            <input type="hidden" id="other_expense" name="other_expense">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Total Transaksi</label>
                                            <input type="text" class="form-control" id="total_transaksi_text"
                                                placeholder="Total Transaksi" readonly>
                                            <input type="hidden" id="total_transaksi" name="total_purchase">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">

                                    <div class="p-4 bg-soft-success rounded-3">
                                        <p class="fs-12 text-dark"><strong>Penjelasan Fitur Transaksi Buat Produk
                                                Manufaktur</strong><br>Stok produk akan langsung bertambah setelah
                                            transaksi
                                            ini sukses dibuat, dan stok Bahan Baku serta Barang Setengah Jadi akan
                                            berkurang
                                            otomatis sesuai dengan komposisi bahan pada produk.
                                            <br>
                                            Harga Pokok Penjualan / COGS pada Produk yang dibuat akan otomatis tergitung
                                            dari Total Transaksi dibagi dengan jumlah produk yang di produksi. Contoh:
                                            Jika
                                            Total Transaksi = Rp100.000 dan produk yang di produksi adalah 5pcs maka
                                            masing
                                            masing produknya HPP nya adalah 100.000 / 5 = Rp20.000
                                        </p>


                                    </div>
                                </div>
                            </div>

                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($view == 'inter-product')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="form-add-material" method="POST">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Nama Barang Setengah Jadi</label>
                                        <input type="text" name="product_name" id="product_name"
                                            class="form-control cust-control"
                                            placeholder="Nama Produk Setengah Jadi"
                                            oninput="generateSemiFinishedProductSKU()">
                                    </div>
                                </div>
                                <div class="row mtop20">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>SKU Barang Setengah Jadi</label>
                                            <input type="text" name="sku" id="sku"
                                                class="form-control cust-control" placeholder="Masukkan SKU">
                                        </div>
                                    </div>

                                    <script>
                                        function generateSemiFinishedProductSKU() {
                                            const productNameInput = document.getElementById('product_name').value.trim();
                                            const skuInput = document.getElementById('sku');

                                            if (productNameInput) {
                                                const words = productNameInput.split(' ');
                                                let sku = '';

                                                // Mengambil huruf pertama dari setiap kata dan mengubahnya menjadi huruf besar
                                                words.forEach(word => {
                                                    if (word) {
                                                        sku += word.charAt(0).toUpperCase();
                                                    }
                                                });

                                                // Membuat empat angka random
                                                const randomNumbers = Math.floor(Math.random() * 9000) + 1000; // Pastikan selalu empat angka

                                                // Membuat dua huruf random
                                                const randomLetters = Array(2).fill(null).map(() => String.fromCharCode(Math.floor(Math.random() * 26) +
                                                    65)).join('');

                                                // Gabungkan SKU dengan tanda hubung, angka random, dan huruf random
                                                sku += '-' + randomNumbers + randomLetters;

                                                skuInput.value = sku;
                                            } else {
                                                skuInput.value = ''; // Kosongkan jika Nama Barang Setengah Jadi dihapus
                                            }
                                        }
                                    </script>


                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Kategori</label>
                                            <select name="category_id" id="category_id"
                                                class="form-control cust-control">
                                                <option value="">Select category or Input new category</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}">
                                                        {{ $category->inter_category }}
                                                    </option>
                                                @endforeach

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mtop20">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Satuan</label>
                                            <select name="unit" id="unit"
                                                class="form-control cust-control">
                                                <option value="">Select unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->unit_name }}">{{ $unit->unit_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Minimal Stock</label>
                                            <input type="number" name="min_stock" id="min_stock"
                                                class="form-control cust-control" placeholder="Minimal stock">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Ideal Stock</label>
                                            <input type="number" name="ideal_stock" id="ideal_stock"
                                                class="form-control cust-control" placeholder="Stock Ideal">
                                        </div>
                                    </div>
                                </div>
                                <hr />
                                <div class="row mtop20">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="text-white">Komposisi Produk</h5>
                                                <center><a onclick="add_composition_item()"
                                                        href="javascript:void(0);"
                                                        class="avatar-text avatar-md bg-success text-white"
                                                        data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                            class="fa fa-plus"></i></a></center>
                                            </div>
                                            <div class="card-body">
                                                <div class="col-md-12">
                                                    <div class="composition-container">

                                                        <div class="row baris" id="baris_1">
                                                            <div class="col-md-8">
                                                                <select class="form-control cust-control select-item"
                                                                    id="composition_1" name="composition[]">
                                                                    <option value="">Pilih komposisi bahan
                                                                    </option>
                                                                    <optgroup label="Bahan Baku">
                                                                        @foreach ($materials as $material)
                                                                            <option value="{{ $material->id }}_1">
                                                                                {{ $material->material_name }}
                                                                                - {{ $material->unit }}
                                                                            </option>
                                                                        @endforeach
                                                                    </optgroup>
                                                                    <optgroup label="Barang Setengah Jadi">
                                                                        @foreach ($inters as $inter)
                                                                            <option value="{{ $inter->id }}_2">
                                                                                {{ $inter->product_name }} -
                                                                                {{ $inter->unit }}</option>
                                                                        @endforeach
                                                                    </optgroup>

                                                                </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text"
                                                                    class="form-control cust-control"
                                                                    id="quantity_1" name="quantity[]"
                                                                    placeholder="quantitiy">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <center><a disabled="disabled"
                                                                        href="javascript:void(0);"
                                                                        class="avatar-text avatar-md bg-danger text-white"
                                                                        data-bs-toggle="dropdown"
                                                                        data-bs-auto-close="outside"><i
                                                                            class="fa fa-trash"></i></a></center>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mtop20">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Keterangan</label>
                                            <textarea class="form-control cust-control" style="height: 120px;" id="description" name="description"
                                                placeholder="Masukkan keterangan"></textarea>
                                        </div>
                                    </div>


                                    <div class="row mtop20">
                                        <div class="col-md-12">
                                            <div class="p-4 bg-soft-warning rounded-3">
                                                <p class="fs-12 text-dark"><strong>Tips Mengisi Barang Setengah
                                                        Jadi</strong><br>Gunakan satuan porsi dalam mengisi barang
                                                    setengah
                                                    jadi jika produknya FnB, Misal untuk membuat 1 Porsi Nasi Goreng
                                                    membutuhkan 1 Porsi Nasi Putih.

                                                    Fitur ini juga bisa untuk pengelompokkan bahan baku misal: Untuk
                                                    mengelompokkan Sayuran dalam Nasi Goreng tinggal ditulis "Sayuran"
                                                    dalam
                                                    Nama Barang Setengah Jadinya, sedangkan komposisinya bisa dimasukkan
                                                    Sawi, Wortel, Kubis, dan lain lain. Bisa juga untuk mengelompokkan
                                                    "Rempah" dan "Bumbu"
                                                </p>


                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="mtop20"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>


    @endif


    @if ($view == 'konversi')
        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form id="form-tambah" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}
                        <input type="hidden" id="id" name="id">
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img id="loading-image" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="kartu">
                                <div class="row">

                                    <div class="col-md-4">

                                        <div class="form-group">
                                            <label>Tanggal Transaksi:</label>
                                            <input type="date" value="{{ date('Y-m-d') }}"
                                                class="form-control cust-control" id="transaction_date"
                                                name="transaction_date">
                                        </div>

                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama / No.Refrensi:</label>
                                            <input type="text" class="form-control cust-control" id="reference"
                                                name="reference"
                                                placeholder="masukkan Nama / No. refrensi transaksi anda">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Bahan Baku / Bahan 1/2 Jadi:</label>
                                            <select class="form-control cust-control select-item" id="product_id"
                                                name="product_id">
                                                <option value="">Pilih</option>
                                                <optgroup label="Material">
                                                    @foreach ($mat as $m)
                                                        <option value="{{ $m['id'] }}_{{ $m['type'] }}">
                                                            {{ $m['name'] }}</option>
                                                    @endforeach
                                                </optgroup>

                                                <optgroup label="Bahan Setengah Jadi">
                                                    @foreach ($int as $in)
                                                        <option value="{{ $in['id'] }}_{{ $in['type'] }}">
                                                            {{ $in['name'] }}</option>
                                                    @endforeach
                                                </optgroup>


                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row" style="margin-top:10px;">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Unit:</label>
                                            <input type="text" readonly class="form-control cust-control"
                                                id="unit" name="unit">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Stok:</label>
                                            <input type="text" readonly class="form-control cust-control"
                                                id="stock_text" name="stock_text">
                                            <input type="hidden" id="stock" name="stock">
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Quantity:</label>
                                            <input readonly onkeyup="qty_onchange()" type="text"
                                                class="form-control cust-control" id="product_quantity_text"
                                                name="product_quantity_text">
                                            <input type="hidden" id="product_quantity" name="product_quantity">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>HPP:</label>
                                            <input type="text" readonly class="form-control cust-control"
                                                id="product_price_text" name="product_price_text">
                                            <input type="hidden" id="product_price" name="product_price">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Total Harga:</label>
                                            <input type="text" readonly class="form-control cust-control"
                                                id="total_price_text" name="total_price_text">
                                            <input type="hidden" id="total_price" name="total_price">
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <div class="kartu mtop20 bg-beige" id="product-container" style="display: none;">
                                <h5>Tambahkan item Produk Jadi yang akan dikonversi</h5>
                                <div class="row bariss" id="bariss_1">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Produk Jadi</label>
                                            <select class="form-control cust-control select-item" data-id="1"
                                                id="item_1" name="item[]">
                                                <option value="">Pilih Produk</option>
                                                <optgroup label="Produk Jadi">
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}_1">{{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="Produk Setengah Jadi">
                                                    @foreach ($inters as $inter)
                                                        <option value="{{ $inter->id }}_2">
                                                            {{ $inter->product_name }}</option>
                                                    @endforeach
                                                </optgroup>

                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Jumlah</label>
                                            <input onkeyup="onchange_jumlah(1)" type="text"
                                                class="form-control cust-control" id="jumlah_text_1"
                                                placeholder="Quantity">
                                            <input class="jumlah" type="hidden" id="jumlah_1" name="jumlah[]">
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Harga</label>
                                            <input readonly type="text" class="form-control cust-control"
                                                id="item_price_text_1" placeholder="Harga">
                                            <input class="purchase-amount" type="hidden" id="item_price_1"
                                                name="item_price[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Total Harga</label>
                                            <input readonly type="text" class="form-control cust-control"
                                                id="item_total_text_1" placeholder="Total Harga">
                                            <input class="total-amount" type="hidden" id="item_total_1"
                                                name="item_total[]">
                                        </div>
                                    </div>


                                    <div class="col-md-1 button-product-action" style="margin-top:24px;">
                                        <center><a title="Tambah Produk" href="javascript:void(0);"
                                                onclick="tambah_item()"
                                                class="avatar-text avatar-md bg-success text-white"
                                                data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                    class="fa fa-plus"></i></a></center>

                                    </div>

                                </div>
                            </div>

                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Total Material/Bahan 1/2 Jadi</label>
                                            <input type="text" class="form-control" id="total_material_text"
                                                placeholder="Total Material" readonly>
                                            <input type="hidden" id="total_material" name="total_material">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Total Barang Produksi</label>
                                            <input type="text" class="form-control" id="total_product_text"
                                                placeholder="Total Produk" readonly>
                                            <input type="hidden" id="total_product" name="total_product">
                                            <input type="hidden" id="total_product_jadi"
                                                name="total_product_jadi">
                                            <input type="hidden" id="total_product_setengah_jadi"
                                                name="total_setengah_jadi">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sisa Material (in Rp)</label>
                                            <input type="text" class="form-control" id="total_sisa_text"
                                                placeholder="Sisa Material / Bahan 1/2 Jadi" readonly>
                                            <input type="hidden" id="total_sisa" name="total_sisa">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Sisa Material (in Qty)</label>
                                            <input type="text" class="form-control" id="total_sisa2_text"
                                                placeholder="Sisa Material / Bahan 1/2 Jadi" readonly>
                                            <input type="hidden" id="total_sisa2" name="total_sisa2">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kartu mtop20 bg-beige">
                                <h5>Tambahkan Biaya Produksi (Jika Ada)</h5>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <label>Biaya Menggunakan : </label>
                                                    <select class="form-control cust-control" id="cost_account"
                                                        name="cost_account">
                                                        <option value="">Pilih</option>
                                                        <optgroup label="CASH">
                                                            @foreach ($lancar as $l)
                                                                <option
                                                                    value="{{ $l->id }}_{{ $l->account_code_id }}_1">
                                                                    {{ $l->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                        <optgroup label="UTANG">
                                                            @foreach ($pendek as $p)
                                                                <option
                                                                    value="{{ $p->id }}_{{ $p->account_code_id }}_2">
                                                                    {{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                            @foreach ($panjang as $j)
                                                                <option
                                                                    value="{{ $j->id }}_{{ $j->account_code_id }}_2">
                                                                    {{ $j->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>

                                <div class="row" style="margin-top:15px;">
                                    <div class="col-md-8">
                                        <div class="row baris-biaya" id="baris_biaya_1">
                                            <div class="col-md-7">
                                                <div class="form-group">
                                                    <input type="text" class="form-control cust-control"
                                                        id="nama_biaya_1" placeholder="Nama Biaya"
                                                        name="nama_biaya[]">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input onkeyup="biaya_onchange(1)" type="text"
                                                        class="form-control cust-control" id="jumlah_biaya_text_1"
                                                        placeholder="Jumlah Biaya">
                                                    <input class="jumlah-biaya" type="hidden" id="jumlah_biaya_1"
                                                        name="jumlah_biaya[]">
                                                </div>
                                            </div>
                                            <div class="col-md-1 button-product-action">
                                                <center><a title="Tambah Biaya" href="javascript:void(0);"
                                                        onclick="tambah_biaya()"
                                                        class="avatar-text avatar-md bg-success text-white"
                                                        data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                                            class="fa fa-plus"></i></a></center>

                                            </div>
                                        </div>
                                        <hr />
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label>Total Biaya</label>
                                                    <input type="text" class="form-control"
                                                        id="total_biaya_text" placeholder="Total Biaya" readonly>
                                                    <input type="hidden" id="total_biaya" name="total_biaya">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">

                                    </div>
                                </div>
                            </div>


                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($view == 'opname')
        <div class="modal fade" id="modal-detail">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header" style="background-color: #2f467a;">
                        <h5 class="modal-title" style="color:white;">Daftar Produk Stock Opname</h5>
                    </div>
                    <div class="modal-body">
                        <img class="loading-gambar" id="loading-opname-detail" style="display:none;"
                            src="{{ asset('template/main/images/loading.gif') }}">
                        <div class="kartu mtop20 bg-beige">
                            <input type="hidden" id="sid-detail">
                            <div class="baris" id="content-opname-detail">
                            </div>
                        </div>
                        <div class="mtop20"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button id="btn-sesuaikan-stock" onclick="sesuaikan_stock()" type="button"
                            class="btn btn-primary">Sesuaikan Stok Dengan Fisik</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-tambah">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-tambah" method="POST">
                        {{ csrf_field() }}
                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;"></h5>
                        </div>
                        <div class="modal-body">
                            <img class="loading-gambar" id="loading-image-opname" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="kartu mtop20 bg-beige">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Deskripsi</label>
                                            <textarea required style="height:180px;" class="form-control" name="description" id="description"
                                                placeholder="Berikan catatan anda mengenai stock opname yang akan dilakukan"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mtop20"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-upload">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-upload-opname" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }} {{ method_field('POST') }}

                        <div class="modal-header" style="background-color: #2f467a;">
                            <h5 class="modal-title" style="color:white;">Upload Data Stock Opname</h5>
                        </div>
                        <div class="modal-body">
                            <img class="loading-gambar" id="loading-pro" style="display:none;"
                                src="{{ asset('template/main/images/loading.gif') }}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Attach your File..</label>
                                        <input type="hidden" id="soid" name="soid">
                                        <input type="file" name="file" id="file"
                                            class="form-control cust-control"
                                            placeholder="Upload your excel file here..." accept=".xls, .xlsx"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <div class="p-4 bg-soft-warning rounded-3">
                                        <p class="fs-12 text-dark"><strong>Tips Upload file Stock Opname</strong>
                                        <ol>
                                            <li>Download template file excel untuk upload <a
                                                    onclick="download_template_opname()"
                                                    href="javascript:void();">disini.</a>
                                            </li>
                                            <li>Mohon tidak mengubah-ubah judul pada kolom paling atas file excel pada
                                                template.</li>
                                            <li>Isikan jumlah produk fisik anda pada kolom yang disediakan.</li>

                                            <li>Setelah selesai silahkan upload file yang sudah diisi tersebut</li>
                                            <li>Untuk melihat hasil isian anda bisa tekan tombol detail pada menu. Dan
                                                jika anda ingin menyesuaikan jumlah stock komputer agar sama dengan
                                                stock fisik anda silahkan tekan tombol "Sesuaikan" yang terletak dibawah
                                                form.</li>

                                            <li>Silahkan cek kembali hasil stock opname anda. terima kasih.</li>
                                        </ol>
                                        </p>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif
