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
                        <li class="breadcrumb-item"><a href="{{ url('/setting') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">Pengaturan Modal Awal</li>
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
                                <h5 class="card-title">Pengaturan Modal Awal</h5>


                                <a href="javascript:void(0);" onclick="add_item()"
                                    class="avatar-text avatar-md bg-default text-white pull-right;"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="feather-plus bg-dark"></i>
                                </a>


                            </div>
                            @php

                                if (empty($data)) {
                                    $tanggal = date('Y-m-01');
                                    $akun1 = '';
                                    $akun2 = '';

                                    $total_item = 0;

                                    $debit1 = 0;
                                    $kredit1 = 0;

                                    $debit2 = 0;
                                    $kredit2 = 0;
                                    $transaction_name = '';
                                } else {
                                    $tanggal = date('Y-m-01', $data->created);
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
                                    $transaction_name = $data->transaction_name;
                                }

                                // for ($i=0; $i < $total_item; $i++) {
                                //     $akun_code[] = $detail[$i]->rf_accode_id;
                                // }

                            @endphp
                            <form id="form-update-jurnal" method="POST" action="">
                                @csrf
                                <input type="hidden" id="transaction_id" name="transaction_id"
                                    value="{{ empty($data) ? '' : $data->id }}">
                                <div class="card-body custom-card-action p-0">
                                    <div class="container mtop30 main-box">
                                        <div class="row row-mobile">

                                            <div class="col-md-3 tdate">
                                                <input readonly type="date" value="{{ $tanggal }}"
                                                    id="transaction_date" name="transaction_date"
                                                    class="form-control cust-control">
                                            </div>
                                            <div class="col-md-9 tname">
                                                <input readonly type="text" value="Saldo Awal"
                                                    class="form-control cust-control" placeholder="Nama Transaksi"
                                                    id="transaction_name" name="transaction_name">
                                            </div>
                                        </div>
                                        <div class="mtop20"></div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="l-estimasi">Estimasi</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="l-debit">Debit</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="l-kredit">Kredit</label>
                                            </div>
                                            <div class="row" id="row_1">
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
                                                <div class="col-md-4">
                                                    <input type="text" value="{{ ribuan($debit1) }}"
                                                        onkeyup="set_debit(1)" class="form-control cust-control tdebit"
                                                        placeholder="0" id="debittext_1">
                                                    <input type="hidden" value="{{ $debit1 }}" id="debit_1"
                                                        name="debit[]">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" value="{{ ribuan($kredit1) }}"
                                                        onkeyup="set_kredit(1)" class="form-control cust-control tkredit"
                                                        placeholder="0" id="kredittext_1">
                                                    <input type="hidden" value="{{ $kredit1 }}" id="kredit_1"
                                                        name="kredit[]">
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

                                                <div class="col-md-4">

                                                    <input type="text" value="{{ ribuan($debit2) }}"
                                                        onkeyup="set_debit(2)" class="form-control cust-control tdebit"
                                                        placeholder="0" id="debittext_2">
                                                    <input type="hidden" id="debit_2" name="debit[]"
                                                        value="{{ $debit2 }}">
                                                </div>
                                                {{-- {{ dd($akun['group']) }} --}}
                                                <div class="col-md-4">

                                                    <input type="text" value="{{ ribuan($kredit2) }}"
                                                        onkeyup="set_kredit(2)" class="form-control cust-control tkredit"
                                                        placeholder="0" id="kredittext_2">
                                                    <input type="hidden" id="kredit_2" name="kredit[]"
                                                        value="{{ $kredit2 }}">
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

                                                @endphp
                                                <div class="row row-item" id="row_{{ $b }}">
                                                    <div class="col-md-4">

                                                        <select class="form-control cust-control"
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

                                                    <div class="col-md-4">

                                                        <input type="text" value="{{ ribuan($debits) }}"
                                                            onkeyup="set_debit({{ $b }})"
                                                            class="form-control cust-control" placeholder="0"
                                                            id="debittext_{{ $b }}">
                                                        <input type="hidden" id="debit_{{ $b }}"
                                                            name="debit[]" value="{{ $debits }}">
                                                    </div>
                                                    {{-- {{ dd($akun['group']) }} --}}
                                                    <div class="col-md-4">

                                                        <input type="text" value="{{ ribuan($kredits) }}"
                                                            onkeyup="set_kredit({{ $b }})"
                                                            class="form-control cust-control" placeholder="0"
                                                            id="kredittext_{{ $b }}">
                                                        <input type="hidden" id="kredit_{{ $b }}"
                                                            name="kredit[]" value="{{ $kredits }}">
                                                        <a href="javascript:void(0);"
                                                            onclick="delete_item({{ $b }})" type="button"
                                                            class="btn btn-sm del-item"><i class="fa fa-remove"></i></a>
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
                                                <div class="col-md-4">
                                                    <label class="label-debit">0</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="label-kredit">0</label>
                                                </div>
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
