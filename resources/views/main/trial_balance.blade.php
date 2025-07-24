@extends(isset($userKey) ? 'master-preview' : 'master')

@section('content')
    @if (!$userKey)
        <main class="nxl-container">
            <div class="nxl-content">
                <!-- [ page-header ] start -->
                <div class="page-header">
                    <div class="page-header-left d-flex align-items-center">
                        <div class="page-header-title">
                            <h5 class="m-b-10"></h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('report') }}">Laporan</a></li>
                            <li class="breadcrumb-item">Neraca Saldo</li>
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
                                    <h5 class="card-title">Neraca Saldo</h5>
                                    @php
                                        $bulan_ini = date('F');
                                        $tahun_ini = date('Y');

                                        $awal = strtotime(date('Y-m-01'));
                                        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                                        $end = date('Y') . '-' . date('m') . '-' . $tanggal_akhir;

                                        $akhir = strtotime($end);
                                    @endphp
                                    <div class="button-export" style="display: flex;">
                                        <button onclick="export_excel()" style="margin-right:8px";
                                            class="btn btn-sm btn-success">Export XLS</button>
                                        <button onclick="export_pdf()" class="btn btn-sm btn-danger">Export PDF</button>
                                    </div>
                                </div>
                                <div class="card-body custom-card-action p-0">
                                    <div class="container mtop30 main-box">
                                        <form id="form-trial-balance-submit" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12" style="display: inline-flex">
                                                    <div class="form-group">
                                                        <select style="width:200px;" class="form-control cust-control"
                                                            id="month_from" name="month_from">
                                                            <option value="">Semua bulan</option>
                                                            <option <?php if ($bulan_ini == 'January') {
                                                                echo 'selected';
                                                            } ?> value="01">January</option>
                                                            <option <?php if ($bulan_ini == 'February') {
                                                                echo 'selected';
                                                            } ?> value="02">February</option>
                                                            <option <?php if ($bulan_ini == 'March') {
                                                                echo 'selected';
                                                            } ?> value="03">March</option>
                                                            <option <?php if ($bulan_ini == 'April') {
                                                                echo 'selected';
                                                            } ?> value="04">April</option>
                                                            <option <?php if ($bulan_ini == 'May') {
                                                                echo 'selected';
                                                            } ?> value="05">May</option>
                                                            <option <?php if ($bulan_ini == 'June') {
                                                                echo 'selected';
                                                            } ?> value="06">June</option>
                                                            <option <?php if ($bulan_ini == 'July') {
                                                                echo 'selected';
                                                            } ?> value="07">July</option>
                                                            <option <?php if ($bulan_ini == 'August') {
                                                                echo 'selected';
                                                            } ?> value="08">August</option>
                                                            <option <?php if ($bulan_ini == 'September') {
                                                                echo 'selected';
                                                            } ?> value="09">September</option>
                                                            <option <?php if ($bulan_ini == 'October') {
                                                                echo 'selected';
                                                            } ?> value="10">October</option>
                                                            <option <?php if ($bulan_ini == 'November') {
                                                                echo 'selected';
                                                            } ?> value="11">November</option>
                                                            <option <?php if ($bulan_ini == 'December') {
                                                                echo 'selected';
                                                            } ?> value="12">December</option>

                                                        </select>
                                                    </div>


                                                    <select style="width:200px;margin-left:5px;"
                                                        class="form-control cust-control" name="year_from" id="year_from">
                                                        <option value="">Semua tahun</option>
                                                        <option <?php if ($tahun_ini == date('Y')) {
                                                            echo 'selected';
                                                        } ?> value="{{ date('Y') }}">
                                                            {{ date('Y') }}</option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}
                                                        </option>
                                                    </select>



                                                    <div class="form-group">
                                                        <select style="width:200px;" class="form-control cust-control"
                                                            name="month_to" id="month_to">
                                                            <option value="">Semua bulan</option>
                                                            <option <?php if ($bulan_ini == 'January') {
                                                                echo 'selected';
                                                            } ?> value="01">January</option>
                                                            <option <?php if ($bulan_ini == 'February') {
                                                                echo 'selected';
                                                            } ?> value="02">February</option>
                                                            <option <?php if ($bulan_ini == 'March') {
                                                                echo 'selected';
                                                            } ?> value="03">March</option>
                                                            <option <?php if ($bulan_ini == 'April') {
                                                                echo 'selected';
                                                            } ?> value="04">April</option>
                                                            <option <?php if ($bulan_ini == 'May') {
                                                                echo 'selected';
                                                            } ?> value="05">May</option>
                                                            <option <?php if ($bulan_ini == 'June') {
                                                                echo 'selected';
                                                            } ?> value="06">June</option>
                                                            <option <?php if ($bulan_ini == 'July') {
                                                                echo 'selected';
                                                            } ?> value="07">July</option>
                                                            <option <?php if ($bulan_ini == 'August') {
                                                                echo 'selected';
                                                            } ?> value="08">August</option>
                                                            <option <?php if ($bulan_ini == 'September') {
                                                                echo 'selected';
                                                            } ?> value="09">September</option>
                                                            <option <?php if ($bulan_ini == 'October') {
                                                                echo 'selected';
                                                            } ?> value="10">October</option>
                                                            <option <?php if ($bulan_ini == 'November') {
                                                                echo 'selected';
                                                            } ?> value="11">November</option>
                                                            <option <?php if ($bulan_ini == 'December') {
                                                                echo 'selected';
                                                            } ?> value="12">December</option>

                                                        </select>
                                                    </div>

                                                    <select style="width:200px;margin-left:5px;"
                                                        class="form-control cust-control" name="year_to" id="year_to">
                                                        <option value="">Semua tahun</option>
                                                        <option <?php if ($tahun_ini == date('Y')) {
                                                            echo 'selected';
                                                        } ?> value="{{ date('Y') }}">
                                                            {{ date('Y') }}</option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}
                                                        </option>
                                                        <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                            echo 'selected';
                                                        } ?>
                                                            value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                            {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}
                                                        </option>
                                                    </select>

                                                    <button style="float: right;margin-left:5px;margin-top:1px;"
                                                        id="btn-submit-profit-loss" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="mtop20"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table" id="table-trial-balance">
                                                        <tr>

                                                            <th style="border-bottom: 2px solid black;">Keterangan</th>
                                                            <th style="border-bottom: 2px solid black;">Debit</th>
                                                            <th style="border-bottom: 2px solid black;">Kredit</th>
                                                        </tr>
                                                        @php
                                                            $total_debet = 0;
                                                            $total_credit = 0;
                                                        @endphp
                                                        @foreach ($dt['current_asset'] as $key)
                                                            @php
                                                                $ca = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 1)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($ca > 0) {
                                                                    $debit = abs($ca);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($ca);
                                                                }

                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format($kredit) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        @foreach ($dt['income'] as $key)
                                                            @php
                                                                $inc = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 7)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));

                                                                if ($inc > 0) {
                                                                    $debit = 0;
                                                                    $kredit = abs($inc);
                                                                } else {
                                                                    $debit = abs($inc);
                                                                    $kredit = 0;
                                                                }

                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($dt['nb_income'] as $key)
                                                            @php
                                                                $inc = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 11)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));

                                                                if ($inc > 0) {
                                                                    $debit = 0;
                                                                    $kredit = abs($inc);
                                                                } else {
                                                                    $debit = abs($inc);
                                                                    $kredit = 0;
                                                                }

                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($dt['fixed_asset'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 2)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($fa > 0) {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($dt['akumulasi'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 3)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));

                                                                if ($fa > 0) {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                } else {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;
                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($dt['cost_good'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 8)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($fa > 0) {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;
                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach


                                                        @foreach ($dt['selling_cost'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 9)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($fa > 0) {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;
                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @foreach ($dt['admin_cost'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 10)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($fa > 0) {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;
                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach


                                                        @foreach ($dt['nb_cost'] as $key)
                                                            @php
                                                                $fa = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 12)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));

                                                                if ($fa > 0) {
                                                                    $debit = abs($fa);
                                                                    $kredit = 0;
                                                                } else {
                                                                    $debit = 0;
                                                                    $kredit = abs($fa);
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;
                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach


                                                        @foreach ($dt['short_debt'] as $key)
                                                            @php
                                                                $sd = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 4)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));

                                                                if ($sd > 0) {
                                                                    $debit = 0;
                                                                    $kredit = abs($sd);
                                                                } else {
                                                                    $debit = abs($sd);
                                                                    $kredit = 0;
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        @foreach ($dt['long_debt'] as $key)
                                                            @php
                                                                $ld = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 5)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));

                                                                if ($ld > 0) {
                                                                    $debit = 0;
                                                                    $kredit = abs($ld);
                                                                } else {
                                                                    $debit = abs($ld);
                                                                    $kredit = 0;
                                                                }
                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        @foreach ($dt['capital'] as $key)
                                                            @php

                                                                $nd = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $key->id)
                                                                    ->where('account_code_id', 6)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir);

                                                                if ($key->code == 'prive') {
                                                                    $ld = $nd->sum(\DB::raw('debet - credit'));
                                                                    if ($ld > 0) {
                                                                        $debit = abs($ld);
                                                                        $kredit = 0;
                                                                    } else {
                                                                        $debit = 0;
                                                                        $kredit = abs($ld);
                                                                    }
                                                                } else {
                                                                    $ld = $nd->sum(\DB::raw('credit - debet'));
                                                                    if ($ld > 0) {
                                                                        $debit = 0;
                                                                        $kredit = abs($ld);
                                                                    } else {
                                                                        $debit = abs($ld);
                                                                        $kredit = 0;
                                                                    }
                                                                }

                                                                $total_debet = $total_debet + $debit;
                                                                $total_credit = $total_credit + $kredit;

                                                            @endphp
                                                            @if ($debit + $kredit != 0)
                                                                <tr>
                                                                    <td>{{ $key->name }}</td>
                                                                    <td>{{ number_format($debit) }}</td>
                                                                    <td>{{ number_format(abs($kredit)) }}</td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>

                                                            <td style="border-top:2px solid black;"></td>
                                                            <td style="border-top:2px solid black;">
                                                                <strong>{{ number_format($total_debet) }}</strong>
                                                            </td>
                                                            <td style="border-top:2px solid black;">
                                                                <strong>{{ number_format(abs($total_credit)) }}</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>

                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mtop50"></div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                <!-- [ Main Content ] end -->

            </div>
            <!-- [ Footer ] start -->
        </main>
    @else
        <div class="row">
            <!-- [Leads] start -->
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if ($from === 'desktop')
                        <div class="card-header">
                            <h5 class="card-title">Neraca Saldo</h5>
                            <div class="button-export" style="display: flex;">
                                <button onclick="export_excel()" style="margin-right:8px";
                                    class="btn btn-sm btn-success">Export XLS</button>
                                <button onclick="export_pdf()" class="btn btn-sm btn-danger">Export PDF</button>
                            </div>
                        </div>
                    @endif
                    @php
                        $bulan_ini = date('F');
                        $tahun_ini = date('Y');

                        $awal = strtotime(date('Y-m-01'));
                        $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                        $end = date('Y') . '-' . date('m') . '-' . $tanggal_akhir;

                        $akhir = strtotime($end);
                    @endphp
                    <div class="card-body custom-card-action p-0">
                        <div class="containerx mtop30 main-box">
                            <form id="form-trial-balance-submit" method="POST">
                                @csrf
                                <div class="row">
                                    @if ($from !== 'desktop')
                                        <div class="col-12 mb-3">
                                            <div class="button-export" style="display: flex;">
                                                <button onclick="export_excel()" style="margin-right:8px";
                                                    class="btn btn-sm btn-success">Export XLS</button>
                                                <button onclick="export_pdf()" class="btn btn-sm btn-danger">Export PDF</button>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-md-12" style="display: inline-flex">
                                        <div class="form-group">
                                            <select style="width:200px;" class="form-control cust-control"
                                                id="month_from" name="month_from">
                                                <option value="">Semua bulan</option>
                                                <option <?php if ($bulan_ini == 'January') {
                                                    echo 'selected';
                                                } ?> value="01">January</option>
                                                <option <?php if ($bulan_ini == 'February') {
                                                    echo 'selected';
                                                } ?> value="02">February</option>
                                                <option <?php if ($bulan_ini == 'March') {
                                                    echo 'selected';
                                                } ?> value="03">March</option>
                                                <option <?php if ($bulan_ini == 'April') {
                                                    echo 'selected';
                                                } ?> value="04">April</option>
                                                <option <?php if ($bulan_ini == 'May') {
                                                    echo 'selected';
                                                } ?> value="05">May</option>
                                                <option <?php if ($bulan_ini == 'June') {
                                                    echo 'selected';
                                                } ?> value="06">June</option>
                                                <option <?php if ($bulan_ini == 'July') {
                                                    echo 'selected';
                                                } ?> value="07">July</option>
                                                <option <?php if ($bulan_ini == 'August') {
                                                    echo 'selected';
                                                } ?> value="08">August</option>
                                                <option <?php if ($bulan_ini == 'September') {
                                                    echo 'selected';
                                                } ?> value="09">September</option>
                                                <option <?php if ($bulan_ini == 'October') {
                                                    echo 'selected';
                                                } ?> value="10">October</option>
                                                <option <?php if ($bulan_ini == 'November') {
                                                    echo 'selected';
                                                } ?> value="11">November</option>
                                                <option <?php if ($bulan_ini == 'December') {
                                                    echo 'selected';
                                                } ?> value="12">December</option>

                                            </select>
                                        </div>

                                        <select style="width:200px;margin-left:5px;" class="form-control cust-control"
                                            name="year_from" id="year_from">
                                            <option value="">Semua tahun</option>
                                            <option <?php if ($tahun_ini == date('Y')) {
                                                echo 'selected';
                                            } ?> value="{{ date('Y') }}">
                                                {{ date('Y') }}</option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}
                                            </option>
                                        </select>

                                        <div class="form-group">
                                            <select style="width:200px;" class="form-control cust-control"
                                                name="month_to" id="month_to">
                                                <option value="">Semua bulan</option>
                                                <option <?php if ($bulan_ini == 'January') {
                                                    echo 'selected';
                                                } ?> value="01">January</option>
                                                <option <?php if ($bulan_ini == 'February') {
                                                    echo 'selected';
                                                } ?> value="02">February</option>
                                                <option <?php if ($bulan_ini == 'March') {
                                                    echo 'selected';
                                                } ?> value="03">March</option>
                                                <option <?php if ($bulan_ini == 'April') {
                                                    echo 'selected';
                                                } ?> value="04">April</option>
                                                <option <?php if ($bulan_ini == 'May') {
                                                    echo 'selected';
                                                } ?> value="05">May</option>
                                                <option <?php if ($bulan_ini == 'June') {
                                                    echo 'selected';
                                                } ?> value="06">June</option>
                                                <option <?php if ($bulan_ini == 'July') {
                                                    echo 'selected';
                                                } ?> value="07">July</option>
                                                <option <?php if ($bulan_ini == 'August') {
                                                    echo 'selected';
                                                } ?> value="08">August</option>
                                                <option <?php if ($bulan_ini == 'September') {
                                                    echo 'selected';
                                                } ?> value="09">September</option>
                                                <option <?php if ($bulan_ini == 'October') {
                                                    echo 'selected';
                                                } ?> value="10">October</option>
                                                <option <?php if ($bulan_ini == 'November') {
                                                    echo 'selected';
                                                } ?> value="11">November</option>
                                                <option <?php if ($bulan_ini == 'December') {
                                                    echo 'selected';
                                                } ?> value="12">December</option>

                                            </select>
                                        </div>

                                        <select style="width:200px;margin-left:5px;" class="form-control cust-control"
                                            name="year_to" id="year_to">
                                            <option value="">Semua tahun</option>
                                            <option <?php if ($tahun_ini == date('Y')) {
                                                echo 'selected';
                                            } ?> value="{{ date('Y') }}">
                                                {{ date('Y') }}</option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-1 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-1 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-2 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-2 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-3 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-3 year', strtotime(date('Y')))) }}
                                            </option>
                                            <option <?php if ($tahun_ini == date('Y', strtotime('-4 year', strtotime(date('Y'))))) {
                                                echo 'selected';
                                            } ?>
                                                value="{{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}">
                                                {{ date('Y', strtotime('-4 year', strtotime(date('Y')))) }}
                                            </option>
                                        </select>

                                        <button style="float: right;margin-left:5px;margin-top:1px;"
                                            id="btn-submit-profit-loss" class="btn btn-primary">Submit 1</button>
                                    </div>
                                </div>
                            </form>
                            <div class="mtop20"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table" id="table-trial-balance">
                                            <tr>

                                                <th style="border-bottom: 2px solid black;">Keterangan</th>
                                                <th style="border-bottom: 2px solid black;">Debit</th>
                                                <th style="border-bottom: 2px solid black;">Kredit</th>
                                            </tr>
                                            @php
                                                $total_debet = 0;
                                                $total_credit = 0;
                                            @endphp
                                            @foreach ($dt['current_asset'] as $key)
                                                @php
                                                    $ca = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 1)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($ca > 0) {
                                                        $debit = abs($ca);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($ca);
                                                    }

                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format($kredit) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @foreach ($dt['income'] as $key)
                                                @php
                                                    $inc = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 7)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit - debet'));

                                                    if ($inc > 0) {
                                                        $debit = 0;
                                                        $kredit = abs($inc);
                                                    } else {
                                                        $debit = abs($inc);
                                                        $kredit = 0;
                                                    }

                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            @foreach ($dt['nb_income'] as $key)
                                                @php
                                                    $inc = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 11)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit - debet'));

                                                    if ($inc > 0) {
                                                        $debit = 0;
                                                        $kredit = abs($inc);
                                                    } else {
                                                        $debit = abs($inc);
                                                        $kredit = 0;
                                                    }

                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            @foreach ($dt['fixed_asset'] as $key)
                                                @php
                                                    $fa = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 2)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($fa > 0) {
                                                        $debit = abs($fa);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($fa);
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            @foreach ($dt['cost_good'] as $key)
                                                @php
                                                    $fa = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 8)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($fa > 0) {
                                                        $debit = abs($fa);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($fa);
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;
                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach


                                            @foreach ($dt['selling_cost'] as $key)
                                                @php
                                                    $fa = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 9)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($fa > 0) {
                                                        $debit = abs($fa);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($fa);
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;
                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach

                                            @foreach ($dt['admin_cost'] as $key)
                                                @php
                                                    $fa = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 10)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($fa > 0) {
                                                        $debit = abs($fa);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($fa);
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;
                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach


                                            @foreach ($dt['nb_cost'] as $key)
                                                @php
                                                    $fa = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 12)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));

                                                    if ($fa > 0) {
                                                        $debit = abs($fa);
                                                        $kredit = 0;
                                                    } else {
                                                        $debit = 0;
                                                        $kredit = abs($fa);
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;
                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach


                                            @foreach ($dt['short_debt'] as $key)
                                                @php
                                                    $sd = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 4)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit - debet'));

                                                    if ($sd > 0) {
                                                        $debit = 0;
                                                        $kredit = abs($sd);
                                                    } else {
                                                        $debit = abs($sd);
                                                        $kredit = 0;
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @foreach ($dt['long_debt'] as $key)
                                                @php
                                                    $ld = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 5)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit - debet'));

                                                    if ($ld > 0) {
                                                        $debit = 0;
                                                        $kredit = abs($ld);
                                                    } else {
                                                        $debit = abs($ld);
                                                        $kredit = 0;
                                                    }
                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @foreach ($dt['capital'] as $key)
                                                @php

                                                    $nd = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $key->id)
                                                        ->where('account_code_id', 6)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir);

                                                    if ($key->code == 'prive') {
                                                        $ld = $nd->sum(\DB::raw('debet - credit'));
                                                        if ($ld > 0) {
                                                            $debit = abs($ld);
                                                            $kredit = 0;
                                                        } else {
                                                            $debit = 0;
                                                            $kredit = abs($ld);
                                                        }
                                                    } else {
                                                        $ld = $nd->sum(\DB::raw('credit - debet'));
                                                        if ($ld > 0) {
                                                            $debit = 0;
                                                            $kredit = abs($ld);
                                                        } else {
                                                            $debit = abs($ld);
                                                            $kredit = 0;
                                                        }
                                                    }

                                                    $total_debet = $total_debet + $debit;
                                                    $total_credit = $total_credit + $kredit;

                                                @endphp
                                                @if ($debit + $kredit != 0)
                                                    <tr>
                                                        <td>{{ $key->name }}</td>
                                                        <td>{{ number_format($debit) }}</td>
                                                        <td>{{ number_format(abs($kredit)) }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>

                                                <td style="border-top:2px solid black;"></td>
                                                <td style="border-top:2px solid black;">
                                                    <strong>{{ number_format($total_debet) }}</strong>
                                                </td>
                                                <td style="border-top:2px solid black;">
                                                    <strong>{{ number_format(abs($total_credit)) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>

                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mtop50"></div>
                    </div>

                </div>
            </div>
        </div>
    @endif
@endsection
