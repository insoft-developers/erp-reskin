<div class="accordion-item">
    <h2 class="accordion-header" id="panelsStayOpen-headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
            Pengaturan Umum
        </button>
    </h2>
    <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
        <div class="accordion-body">
<div class="mb-3 row">
    <label for="username" class="col-sm-2 col-form-label">
        Username Toko
    </label>
    <div class="col-sm-6">
        <input type="text" name="username" id="username" class="form-control"
               value="{{ $account ? $account->username : '' }}" 
               oninput="sanitizeUsername(this)">
        <div id="username-status"></div>
                <small class="form-text text-muted">
            Username hanya boleh terdiri dari huruf kecil dan angka, tanpa spasi atau karakter khusus.
        </small>
    </div>
</div>

<script>
    function sanitizeUsername(input) {
        // Hanya izinkan huruf kecil a-z dan angka 0-9
        input.value = input.value.toLowerCase().replace(/[^a-z0-9]/g, '');
    }
</script>

            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">
                    Alamat Gudang
                </label>
                <div class="col-sm-6">
                    <textarea type="text" name="" id="address" class="form-control">{{$info ? $info->store_address : ''}}</textarea>
                    <div class="row">
                        <div class="col-12">
                            <label for="">Provinsi</label>
                            <select name="" id="provinces" class="form-control" required>
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach($provinces as $prov)
                                <option value="{{$prov->province_id}}" {{$info ? $info->province_id == $prov->province_id ? 'selected' : '' : ''}} >{{$prov->province_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="">Kabupaten/Kota</label>
                            <select name="" id="cities" class="form-control" required>
                                <option value="">-- Pilih Provinsi Dahulu --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="">Kecamatan</label>
                            <select name="" id="subdistricts" class="form-control" required>
                                <option value="">-- Pilih Kota Dahulu --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
