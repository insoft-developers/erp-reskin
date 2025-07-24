@extends('master')

@section('content')
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10"></h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pengaturan Perusahaan</li>
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
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                <div class="row">
                    <!-- [Leads] start -->
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Pengaturan Perusahaan</h5>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div class="container mtop30 main-box">
                                    @if (session()->has('success'))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-success">
                                                    {!! session('success') !!}
                                                </div>
                                            </div>
                                        </div>
                                    @elseif (session()->has('warning'))
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="alert alert-warning">
                                                    {!! session('warning') !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <form id="form-company-setting" method="POST" enctype="multipart/form-data"
                                        action="{{ url('company_setting_update') }}">
                                        @csrf

                                        <input name="id" type="hidden" value="{{ $data == null ? '' : $data->id }}">
                                        <div class="row" id="editBusinessGroup">
                                            <div class="col-md-12 mtop20">
                                                <div class="form-group">
                                                    @if ($data != null && $data->logo != null)
                                                    <div class="mb-3">
                                                        <img src="{{ asset('storage/' . $data->logo) }}" width="100px" />
                                                    </div>
                                                    @endif
                                                    
                                                    <label>Logo Perusahaan</label>
                                                    <input name="logo"
                                                        value="{{ $data == null ? '' : $data->logo }}"
                                                        type="file" class="form-control cust-control">
                                                    @if ($errors->has('logo'))
                                                        <span
                                                            class="help-block">{{ $errors->first('logo') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Email Perusahaan</label>
                                                    <input name="company_email"
                                                        value="{{ $data == null ? '' : $data->company_email }}"
                                                        type="email" class="form-control cust-control"
                                                        placeholder="Contoh: marketing@randu.co.id" required>
                                                    @if ($errors->has('company_email'))
                                                        <span
                                                            class="help-block">{{ $errors->first('company_email') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Nama Perusahaan</label>
                                                    <input name="company_name"
                                                        value="{{ $data == null ? '' : $data->branch_name }}" type="text"
                                                        class="form-control cust-control" placeholder="Contoh: PT Randu Bertumbuh Digital"
                                                        required>
                                                    @if ($errors->has('company_name'))
                                                        <span class="help-block">{{ $errors->first('company_name') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Pajak Perusahaan (%)</label>
                                                    <input name="tax" type="text"
                                                        value="{{ $data == null ? '' : $data->tax }}"
                                                        class="form-control cust-control"
                                                        placeholder="Isi Dengan Total Pajak Yang Dibayar Konsumen/Klien" required>
                                                    @if ($errors->has('tax'))
                                                        <span class="help-block">{{ $errors->first('tax') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Bidang Usaha</label>
                                                    <select name="business_category" id="business_category"
                                                        class="form-control cust-control select2" required>
                                                        <option value="" selected disabled> Kategori Usaha</option>
                                                        @foreach ($category as $cat)
                                                            @if (isset($data->business_category))
                                                                <option value="{{ $cat->id }}"
                                                                    {{ $data->business_category == $cat->id ? 'selected' : '' }}>
                                                                    {{ $cat->category_name }}</option>
                                                            @else
                                                                <option value="{{ $cat->id }}">
                                                                    {{ $cat->category_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @if ($errors->has('business_category'))
                                                        <span
                                                            class="help-block">{{ $errors->first('business_category') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>NPWP Perusahaan (Optional)</label>
                                                    <input name="npwp" value="{{ $data == null ? '' : $data->npwp }}"
                                                        type="text" class="form-control cust-control" placeholder="Nomor Pokok Wajib Pajak">
                                                    @if ($errors->has('npwp'))
                                                        <span class="help-block">{{ $errors->first('npwp') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Telepon Perusahaan</label>
                                                    <input name="phone_number"
                                                        value="{{ $data == null ? '' : $data->business_phone }}"
                                                        type="text" class="form-control cust-control"
                                                        placeholder="Contoh: 03123345627" required>
                                                    @if ($errors->has('phone_number'))
                                                        <span
                                                            class="help-block">{{ $errors->first('phone_number') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-12 mtop20">
                                                <div class="alert alert-success">
                                                    Data Rekening Bank yang digunakan untuk pencairan Saldo Randu Wallet, Hanya dapat diubah sekali, jika ingin
                                                    mengganti data bank silahkan hubungi customer service di <a
                                                        href="https://help.randu.co.id" style="color: blue;"
                                                        target="_blank">https://help.randu.co.id</a>
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label>Nama Bank</label>
                                                    @if (!isset($data->bank_id))
                                                        <select name="bank_id" type="text"
                                                            class="form-control cust-control" required>
                                                            <option value="" selected disabled>Pilih</option>
                                                            @foreach ($bank as $item)
                                                                <option value="{{ $item->id }}">
                                                                    {{ $item->bank_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <h3>{{ $data == null ? '' : $data->bank->bank_name ?? null }}</h3>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label>Nomor Rekening</label>
                                                    @if (!isset($data->no_rekening))
                                                        <input name="no_rekening" type="text"
                                                            class="form-control cust-control"
                                                            placeholder="Masukkan Nomor Rekening" required>
                                                    @else
                                                        <h3>{{ $data == null ? '' : $data->no_rekening }}</h3>
                                                    @endif

                                                    @if ($errors->has('no_rekening'))
                                                        <span
                                                            class="help-block">{{ $errors->first('no_rekening') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label>Atas Nama Rekening</label>
                                                    @if (!isset($data->rekening_name))
                                                        <input name="rekening_name" type="text"
                                                            class="form-control cust-control"
                                                            placeholder="Masukkan Atas Nama Rekening" required>
                                                    @else
                                                        <h3>{{ $data == null ? '' : $data->rekening_name }}</h3>
                                                    @endif
                                                    @if ($errors->has('rekening_name'))
                                                        <span
                                                            class="help-block">{{ $errors->first('rekening_name') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- <div class="col-md-12 mtop20">
                                                <div class="form-group" id="formSelectDomisili">
                                                    <label for="domicile">Domisili</label>
                                                    <select class="form-control cust-control" id="domicile"
                                                        name="domicile" required>
                                                        <option
                                                            value="{{ $data == null ? '' : $data->business_district }}"
                                                            selected>{{ $data == null ? '' : $data->business_district }}
                                                        </option>
                                                    </select>
                                                    @if ($errors->has('domicile'))
                                                        <span class="help-block">{{ $errors->first('domicile') }}</span>
                                                    @endif
                                                </div>
                                            </div> --}}

                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label for="customer-province">Provinsi: </label>
                                                    <select id="customer-province" name="province_id"
                                                        class="form-control" required>
                                                        <option value="{{ $data->province_id }}" selected>
                                                            {{ $data->province->province_name ?? null }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label for="customer-city">Kota/Kabupaten: </label>
                                                    <select id="customer-city" name="city_id" class="form-control" required>
                                                        <option value="{{ $data->city_id }}" selected>
                                                            {{ $data->city->city_name ?? null }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <div class="form-group">
                                                    <label for="customer-district">Kecamatan: </label>
                                                    <select id="customer-district" name="district_id"
                                                        class="form-control" required>
                                                        <option value="{{ $data->district_id }}" selected>
                                                            {{ $data->district->subdistrict_name ?? null }}</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Alamat Lengkap</label>
                                                    <textarea name="address" style="height: 120px;" class="form-control cust-control"
                                                        placeholder="Contoh: Jalan Sapudi No 4, Kelurahan Gubeng, Kecamatan Gubeng, Kota Surabaya, Jawa Timur, Indonesia 60281" required>{{ $data == null ? '' : $data->business_address }}</textarea>
                                                    @if ($errors->has('address'))
                                                        <span class="help-block">{{ $errors->first('address') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-5">
                                                <h2>Data Cabang</h2>
                                            </div>
                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Nama Cabang</label>
                                                    <input name="branches_name"
                                                        value="{{ $cabang == null ? 'Cabang Pusat' : $cabang->name }}" type="text"
                                                        class="form-control cust-control" placeholder="Nama Cabang Perusahaan"
                                                        required>
                                                    @if ($errors->has('branches_name'))
                                                        <span
                                                            class="help-block">{{ $errors->first('branches_name') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Kontak Telpon Cabang</label>
                                                    <input name="branches_phone"
                                                        value="{{ $cabang == null ? '' : $cabang->phone }}"
                                                        type="text" class="form-control cust-control"
                                                        placeholder="Telepon Cabang / Perusahaan">
                                                    @if ($errors->has('branches_phone'))
                                                        <span
                                                            class="help-block">{{ $errors->first('branches_phone') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Kecamatan, Kota, Provinsi</label>
                                                    <select class="form-control cust-control" id="districtEdit"
                                                        name="branches_district_id"
                                                        value="{{ $cabang == null ? '' : $cabang->district_id }}">
                                                        <option value="">Pilih Lokasi</option>
                                                        @foreach ($district as $d)
                                                            <option value="{{ $d->district_id }}"
                                                                {{ $cabang->district_id == $d->district_id ? 'selected' : '' }}>
                                                                {{ $d->provinsi }}, {{ $d->kabupaten }},
                                                                {{ $d->distrik }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div> --}}
                                            <div class="col-md-6 mtop20">
                                                <div class="form-group">
                                                    <label>Alamat Cabang</label>
                                                    <input name="branches_address"
                                                        value="{{ $cabang == null ? '' : $cabang->address }}"
                                                        type="text" class="form-control cust-control"
                                                        placeholder="Alamat Cabang / Perusahaan" required>
                                                    @if ($errors->has('branches_address'))
                                                        <span
                                                            class="help-block">{{ $errors->first('branches_address') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
<div class="row mtop40">
    <div class="col-md-12">
        <button class="btn btn-primary" style="float: left;">Simpan Perubahan</button>
    </div>
</div>


                                    </form>
                                    <div class="mtop60"></div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- [Recent Orders] end -->
                    <!-- [] start -->
                </div>

            </div>
            <!-- [ Main Content ] end -->

        </div>
    </main>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        var prov_id = '{{ $data->province_id }}'
        var city_id = '{{ $data->city_id }}'
        var dist_id = '{{ $data->district_id }}'

        $('#business_category').select2();
        $('#districtEdit').select2()
        $('#customer-province').select2({
            dropdownParent: $("#editBusinessGroup"),
            ajax: {
                url: '{{url('')}}/v1/administrative/provinces',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term // search term
                    };
                },
                processResults: function(data) {
                    console.log(data);
                    apiResults = data.data.data.map(function(item) {
                        return {
                            text: item.province_name,
                            id: item.province_id
                        };
                    });

                    return {
                        results: apiResults
                    };
                },
                cache: false
            },
        })
        $('#customer-province').on('change', function(e) {
            var selectedValue = $(this).val();
            // var selectedText = $(this).find("option:selected").text();

            prov_id = selectedValue
            onSelectCity()
        });
        onSelectCity();
        onselectdistrict();

        function onSelectCity() {
            console.log(prov_id);
            $('#customer-city').select2({
                dropdownParent: $("#editBusinessGroup"),
                ajax: {
                    url: '{{url('')}}/v1/administrative/cities?province_id=' + prov_id,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        apiResults = data.data.data.map(function(item) {
                            return {
                                text: item.city_name,
                                id: item.city_id
                            };
                        });

                        return {
                            results: apiResults
                        };
                    },
                    cache: false
                },
            })
            $('#customer-city').on('change', function(e) {
                var selectedValue = $(this).val();

                city_id = selectedValue
                onselectdistrict()
            });
        }

        function onselectdistrict() {
            $('#customer-district').select2({
                dropdownParent: $("#editBusinessGroup"),
                ajax: {
                    url: '{{url('')}}/v1/administrative/districts?city_id=' + city_id,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term // search term
                        };
                    },
                    processResults: function(data) {
                        apiResults = data.data.data.map(function(item) {
                            return {
                                text: item.subdistrict_name,
                                id: item.subdistrict_id
                            };
                        });

                        return {
                            results: apiResults
                        };
                    },
                    cache: false
                },
            })
            $('#customer-district').on('change', function(e) {
                var selectedValue = $(this).val();
                dist_id = selectedValue
            });
        }
    </script>
@endsection
