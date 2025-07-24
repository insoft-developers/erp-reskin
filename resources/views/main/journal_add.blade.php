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
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Jurnal</a></li>
                        <li class="breadcrumb-item">Tambah Transaksi</li>
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
<div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h5 class="card-title" style="margin: 0;">Tambah Transaksi</h5>
    <button onclick="add_item()" 
        class="btn btn-sm btn-success" 
        style="display: flex; align-items: center;">
        <i class="fa fa-plus" style="margin-right: 5px;"></i> Tambah Akun Estimasi
    </button>
</div>

                            <form id="form-tambah-jurnal" method="POST" action="" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body custom-card-action p-0">
                                    <div class="container mtop30 main-box">
                                        <div class="row row-mobile">
                                            <div class="col-md-3 tdate">
                                                <input type="date" value="{{ date('Y-m-d') }}" id="transaction_date"
                                                    name="transaction_date" class="form-control cust-control">
                                            </div>
                                            <div class="col-md-9 tname">
                                                <input type="text" class="form-control cust-control"
                                                    placeholder="Nama Transaksi" id="transaction_name"
                                                    name="transaction_name">
                                            </div>
                                        </div>
                                        <div class="mtop20"></div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="l-estimasi">Estimasi</label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="l-debit">Debit</label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="l-kredit">Kredit</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="l-kicknote">Catatan</label>
                                            </div>
                                        </div>
                                        <div class="row row-atas" id="row_1">
                                            <div class="col-md-4">

                                                <select class="form-control cust-control takun" id="akun_1"
                                                    name="akun[]">
                                                    <option value="">Pilih</option>
                                                    @foreach ($akun['group'] as $a)
                                                        <optgroup label="{{ $a }}">
                                                            @foreach ($akun['data'] as $i)
                                                                @if ($i['group'] == $a)
                                                                    <option
                                                                        value="{{ $i['id'] }}_{{ $i['account_code_id'] }}">
                                                                        <?= $i['name'] ?></option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">

                                                <input type="text" onkeyup="set_debit(1)"
                                                    class="form-control cust-control tdebit" placeholder="0"
                                                    id="debittext_1">
                                                <input type="hidden" id="debit_1" name="debit[]">
                                            </div>
                                            <div class="col-md-2">

                                                <input type="text" onkeyup="set_kredit(1)"
                                                    class="form-control cust-control tkredit" placeholder="0"
                                                    id="kredittext_1">
                                                <input type="hidden" id="kredit_1" name="kredit[]">

                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control cust-control tkicknote"
                                                    placeholder="Catatan" id="kick_note_1" name="kick_note[]">

                                                <a href="javascript:void(0);" onclick="delete_item(1)" type="button"
                                                    class="btn btn-sm del-item tdelete"><i class="fa fa-remove"></i></a>
                                            </div>

                                        </div>
                                        <div class="row row-item" id="row_2">
                                            <div class="col-md-4">

                                                <select class="form-control cust-control takun" id="akun_2"
                                                    name="akun[]">
                                                    <option value="">Pilih</option>
                                                    @foreach ($akun['group'] as $a)
                                                        <optgroup label="{{ $a }}">
                                                            @foreach ($akun['data'] as $i)
                                                                @if ($i['group'] == $a)
                                                                    <option
                                                                        value="{{ $i['id'] }}_{{ $i['account_code_id'] }}">
                                                                        <?= $i['name'] ?></option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">

                                                <input type="text" onkeyup="set_debit(2)"
                                                    class="form-control cust-control tdebit" placeholder="0"
                                                    id="debittext_2">
                                                <input type="hidden" id="debit_2" name="debit[]">
                                            </div>
                                            {{-- {{ dd($akun['group']) }} --}}
                                            <div class="col-md-2">

                                                <input type="text" onkeyup="set_kredit(2)"
                                                    class="form-control cust-control tkredit" placeholder="0"
                                                    id="kredittext_2">
                                                <input type="hidden" id="kredit_2" name="kredit[]">

                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" class="form-control cust-control tkicknote"
                                                    placeholder="Catatan" id="kick_note_2" name="kick_note[]">
                                                <a href="javascript:void(0);" onclick="delete_item(2)" type="button"
                                                    class="btn btn-sm del-item tdelete"><i class="fa fa-remove"></i></a>
                                            </div>
                                        </div>
                                        <div id="input_add_container"></div>

                                        <div class="mtop20"></div>
                                        <hr />
                                        <div class="row" id="row_total">
                                            <div class="col-md-4">
                                                <label class="label-total">TOTAL</label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="label-debit">0</label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="label-kredit">0</label>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="mtop20"></div>
                                        <div class="form-group">
                                            <label>Catatan:</label>
                                            <textarea style="height: 120px;" class="form-control" id="description" name="description"
                                                placeholder="tulis catatan anda disini..."></textarea>
                                        </div>
                                        <div class="mtop20"></div>
                                        <div class="form-group">
                                            <label>Upload Foto Dokumen Transaksi:</label>
                                            <input type="file" class="form-control" id="image" name="image"
                                                accept=".jpg, .jpeg, .png">
                                        </div>
<div class="mtop20"></div>
<div class="row">
    <div class="col-md-12">
        <button style="float: left; margin-top: 20px;" class="btn btn-primary">Simpan</button>
    </div>
</div>


                                        <div class="mtop30"></div>

                                    </div>
                                </div>
                            </form>
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
