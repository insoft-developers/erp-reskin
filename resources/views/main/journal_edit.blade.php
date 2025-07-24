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
                        <li class="breadcrumb-item">Sunting Transaksi</li>
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
                                <h5 class="card-title">Sunting Transaksi</h5>

                                {{-- 
                                <a href="javascript:void(0);" onclick="add_item()"
                                    class="avatar-text avatar-md bg-default text-white pull-right;"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="feather-plus bg-dark"></i>
                                </a> --}}
                                <button onclick="add_item()" class="btn btn-sm btn-primary btn-insoft"><i
                                        class="fa fa-plus"></i> Tambah
                                    AKun</button>


                            </div>
                            @php
                                $tanggal = date('Y-m-d', $data->created);
                                $akun1 = empty($detail[0]->rf_accode_id)
                                    ? $detail[0]->st_accode_id
                                    : $detail[0]->rf_accode_id;
                                $akun2 = empty($detail[1]->rf_accode_id)
                                    ? $detail[1]->st_accode_id
                                    : $detail[1]->rf_accode_id;

                                $total_item = $detail->count();

                                $debit1 = $detail[0]->debet;
                                $kredit1 = $detail[0]->credit;

                                $debit2 = $detail[1]->debet;
                                $kredit2 = $detail[1]->credit;

                                $kick_note1 = $detail[0]->description;
                                $kick_note2 = $detail[1]->description;

                                // for ($i=0; $i < $total_item; $i++) {
                                //     $akun_code[] = $detail[$i]->rf_accode_id;
                                // }

                            @endphp
                            <form id="form-update-jurnal" method="POST" action="" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="transaction_id" name="transaction_id"
                                    value="{{ Request::segment(2) }}">
                                <div class="card-body custom-card-action p-0">
                                    <div class="container mtop30 main-box">
                                        <div class="row">

                                            <div class="col-md-3 tdate">
                                                <input type="date" value="{{ $tanggal }}" id="transaction_date"
                                                    name="transaction_date" class="form-control cust-control">
                                            </div>
                                            <div class="col-md-9 tname">
                                                <input type="text" value="{{ $data->transaction_name }}"
                                                    class="form-control cust-control" placeholder="Nama Transaksi"
                                                    id="transaction_name" name="transaction_name">
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
                                            <div class="col-md-3">
                                                <label class="l-kicknote">Catatan</label>
                                            </div>
                                        </div>
                                        <div class="row row-atas" id="row_1">
                                            <input type="hidden" id="journal_list_id_1" name="journal_list_id[]">
                                            <div class="col-md-4">
                                                <select class="form-control cust-control takun" id="akun_1"
                                                    name="akun[]">
                                                    <option value="">Pilih</option>
                                                    @foreach ($akun['group'] as $a)
                                                        <optgroup label={{ $a }}>
                                                            @foreach ($akun['data'] as $i)
                                                                @if ($i['group'] == $a)
                                                                    <option <?php if ($akun1 == $i['id'] . '_' . $i['account_code_id']) {
                                                                        echo 'selected';
                                                                    } ?>
                                                                        value="{{ $i['id'] }}_{{ $i['account_code_id'] }}">
                                                                        <?= $i['name'] ?></option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" value="{{ ribuan($debit1) }}" onkeyup="set_debit(1)"
                                                    class="form-control cust-control tdebit" placeholder="0"
                                                    id="debittext_1">
                                                <input type="hidden" name="debit[]" id="debit_1"
                                                    value="{{ $debit1 }}">

                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" value="{{ ribuan($kredit1) }}" onkeyup="set_kredit(1)"
                                                    class="form-control cust-control tkredit" placeholder="0"
                                                    id="kredittext_1">
                                                <input type="hidden" name="kredit[]" id="kredit_1"
                                                    value="{{ $kredit1 }}">

                                            </div>
                                            <div class="col-md-3">
                                                <input value="{{ $kick_note1 }}" type="text"
                                                    class="form-control cust-control tkicknote" placeholder="Catatan"
                                                    id="kick_note_1" name="kick_note[]">
                                                <button disabled="disabled" href="javascript:void(0);"
                                                    onclick="delete_item(1)" type="button"
                                                    class="btn btn-sm del-item tdelete"><i
                                                        class="fa fa-remove"></i></button>
                                            </div>
                                        </div>

                                        <div class="row row-item" id="row_2">
                                            <input type="hidden" id="journal_list_id_2" name="journal_list_id[]">
                                            <div class="col-md-4">

                                                <select class="form-control cust-control takun" id="akun_2"
                                                    name="akun[]">
                                                    <option value="">Pilih</option>
                                                    @foreach ($akun['group'] as $a)
                                                        <optgroup label={{ $a }}>
                                                            @foreach ($akun['data'] as $i)
                                                                @if ($i['group'] == $a)
                                                                    <option <?php if ($akun2 == $i['id'] . '_' . $i['account_code_id']) {
                                                                        echo 'selected';
                                                                    } ?>
                                                                        value="{{ $i['id'] }}_{{ $i['account_code_id'] }}">
                                                                        <?= $i['name'] ?></option>
                                                                @endif
                                                            @endforeach
                                                        </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-2">

                                                <input type="text" value="{{ ribuan($debit2) }}"
                                                    onkeyup="set_debit(2)" class="form-control cust-control tdebit"
                                                    placeholder="0" id="debittext_2">
                                                <input type="hidden" name="debit[]" id="debit_2"
                                                    value="{{ $debit2 }}">
                                            </div>
                                            {{-- {{ dd($akun['group']) }} --}}
                                            <div class="col-md-2">

                                                <input type="text" value="{{ ribuan($kredit2) }}"
                                                    onkeyup="set_kredit(2)" class="form-control cust-control tkredit"
                                                    placeholder="0" id="kredittext_2">
                                                <input type="hidden" name="kredit[]" id="kredit_2"
                                                    value="{{ $kredit2 }}">

                                            </div>
                                            <div class="col-md-3">
                                                <input value="{{ $kick_note2 }}" type="text"
                                                    class="form-control cust-control tkicknote" placeholder="Catatan"
                                                    id="kick_note_2" name="kick_note[]">
                                                <button disabled="disabled" href="javascript:void(0);"
                                                    onclick="delete_item(2)" type="button"
                                                    class="btn btn-sm del-item tdelete"><i
                                                        class="fa fa-remove"></i></button>
                                            </div>
                                        </div>

                                        @php

                                        @endphp
                                        @for ($s = 2; $s < $total_item; $s++)
                                            @php
                                                $b = +$s + +1;

                                                $selected_akun = empty($detail[$s]->rf_accode_id)
                                                    ? $detail[$s]->st_accode_id
                                                    : $detail[$s]->rf_accode_id;
                                                $debits = $detail[$s]->debet;
                                                $kredits = $detail[$s]->credit;
                                                $kick_notes = $detail[$s]->description;

                                            @endphp
                                            <div class="row row-item" id="row_{{ $b }}">
                                                <div class="col-md-4">

                                                    <select class="form-control cust-control takun"
                                                        id="akun_{{ $b }}" name="akun[]">
                                                        <option value="">Pilih</option>
                                                        @foreach ($akun['group'] as $a)
                                                            <optgroup label={{ $a }}>
                                                                @foreach ($akun['data'] as $i)
                                                                    @if ($i['group'] == $a)
                                                                        <option <?php if ($selected_akun == $i['id'] . '_' . $i['account_code_id']) {
                                                                            echo 'selected';
                                                                        } ?>
                                                                            value="{{ $i['id'] }}_{{ $i['account_code_id'] }}">
                                                                            <?= $i['name'] ?></option>
                                                                    @endif
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2">

                                                    <input type="text" value="{{ ribuan($debits) }}"
                                                        onkeyup="set_debit({{ $b }})"
                                                        class="form-control cust-control tdebit" placeholder="0"
                                                        id="debittext_{{ $b }}">
                                                    <input type="hidden" name="debit[]" id="debit_{{ $b }}"
                                                        value="{{ $debits }}">
                                                </div>
                                                {{-- {{ dd($akun['group']) }} --}}
                                                <div class="col-md-2">

                                                    <input type="text" value="{{ ribuan($kredits) }}"
                                                        onkeyup="set_kredit({{ $b }})"
                                                        class="form-control cust-control tkredit" placeholder="0"
                                                        id="kredittext_{{ $b }}">
                                                    <input type="hidden" name="kredit[]"
                                                        id="kredit_{{ $b }}" value="{{ $kredits }}">

                                                </div>
                                                <div class="col-md-3">
                                                    <input value="{{ $kick_notes }}" type="text"
                                                        class="form-control cust-control tkicknote" placeholder="Catatan"
                                                        id="kick_note_{{ $b }}" name="kick_note[]">
                                                    <a href="javascript:void(0);"
                                                        onclick="delete_item({{ $b }})" type="button"
                                                        class="btn btn-sm del-item tdelete"><i
                                                            class="fa fa-remove"></i></a>
                                                </div>
                                            </div>
                                        @endfor


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
                                                placeholder="tulis catatan anda disini...">{{ $data->description }}</textarea>
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
