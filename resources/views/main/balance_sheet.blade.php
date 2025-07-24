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
                            <li class="breadcrumb-item"><a href=" {{ url('report') }} ">Laporan</a></li>
                            <li class="breadcrumb-item">Neraca</li>
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
                                    <h5 class="card-title">Neraca</h5>
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
                                        <form id="form-balance-sheet-submit" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12" style="display: inline-flex">
                                                    <div class="form-group">
                                                        <select class="form-control cust-control select-month"
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


                                                    <select 
                                                        class="form-control cust-control select-year" name="year_from" id="year_from">
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

                                                    <p class="sampai-dengan">s/d</p>
                                                    <p class="sampai-dengan-mobile"> - </p>

                                                    <div class="form-group">
                                                        <select class="form-control cust-control select-month2"
                                                            name="month_to" id="month_too">
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

                                                    <select class="form-control cust-control select-year2" name="year_to" id="year_too">
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

                                                    <button id="btn-submit-profit-loss" class="btn btn-primary">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="mtop20"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="table-profit-loss">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>Keterangan</center>
                                                            </th>
                                                            <th colspan="2">
                                                                <center> {{ date('F Y') }}</center>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>*</th>
                                                            <th>*</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" style="border-top:2px solid black;">
                                                                <strong>Aktiva Lancar</strong>
                                                            </td>
                                                        </tr>
                                                        @php

                                                            $total_lancar = 0;
                                                        @endphp
                                                        @foreach ($dt['aktiva_lancar'] as $i)
                                                            @php
                                                                $lancar = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $i->id)
                                                                    ->where('account_code_id', 1)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet - credit'));
                                                                $total_lancar = $total_lancar + $lancar;
                                                            @endphp

                                                            @if ($lancar != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $i->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($lancar) }} </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Aktiva Lancar</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_lancar) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Aktiva Tetap</strong></td>
                                                        </tr>
                                                        @php

                                                            $total_tetap = 0;
                                                        @endphp
                                                        @foreach ($dt['aktiva_tetap'] as $a)
                                                            @php
                                                                $tetap = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 2)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet-credit'));
                                                                $total_tetap = $total_tetap + $tetap;
                                                            @endphp
                                                            @if ($tetap != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($tetap) }}
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach

                                                        @php

                                                            $total_akumulasi = 0;
                                                        @endphp
                                                        @foreach ($dt['akumulasi'] as $a)
                                                            @php
                                                                $akumulasi = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 3)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit-debet'));
                                                                $total_akumulasi = $total_akumulasi + $akumulasi;
                                                            @endphp
                                                            @if ($akumulasi != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        ({{ number_format($akumulasi) }})
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Akumulasi Penyusutaan</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                ({{ number_format($total_akumulasi) }})
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Total Aktiva Tetap</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_tetap - $total_akumulasi) }}
                                                            </td>
                                                        </tr>
                                                        @php

                                                            $total_aktiva =
                                                                $total_lancar + $total_tetap - $total_akumulasi;
                                                        @endphp
                                                        <tr>
                                                            <td><strong>TOTAL AKTIVA</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;"><strong>
                                                                    {{ number_format($total_aktiva) }} </strong></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Utang Jangka Pendek</strong></td>
                                                        </tr>
                                                        @php

                                                            $total_pendek = 0;
                                                        @endphp
                                                        @foreach ($dt['utang_pendek'] as $a)
                                                            @php
                                                                $pendek = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 4)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit-debet'));
                                                                $total_pendek = $total_pendek + $pendek;
                                                            @endphp
                                                            @if ($pendek != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($pendek) }} </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Utang Jangka Pendek</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_pendek) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Utang Jangka Panjang</strong></td>
                                                        </tr>
                                                        @php

                                                            $total_panjang = 0;
                                                        @endphp
                                                        @foreach ($dt['utang_panjang'] as $a)
                                                            @php

                                                                $panjang = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 5)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit-debet'));
                                                                $total_panjang = $total_panjang + $panjang;
                                                            @endphp
                                                            @if ($panjang != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($panjang) }} </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Utang Jangka Panjang</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_panjang) }}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="3"><strong>Modal</strong></td>
                                                        </tr>
                                                        @php

                                                            $total_modal = 0;
                                                        @endphp
                                                        @foreach ($dt['modal'] as $a)
                                                            @php

                                                                $modal = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 6)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit-debet'));
                                                                $total_modal = $total_modal + $modal;
                                                            @endphp
                                                            @if ($modal != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($modal) }}
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; LABA/RUGI BERSIH </td>
                                                        <td style="text-align:right;"> {{ number_format($laba_bersih) }}
                                                        </td>
                                                        <td></td>
                                                        <tr>
                                                            <td><strong>Total Modal</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_modal + $laba_bersih) }} </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>TOTAL UTANG DAN MODAL</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;"><strong>
                                                                    {{ number_format($total_pendek + $total_panjang + $total_modal + $laba_bersih) }}</strong>
                                                            </td>
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


                        <!-- [Recent Orders] end -->
                        <!-- [] start -->
                    </div>

                </div>
                <!-- [ Main Content ] end -->

            </div>
        </main>
    @else
        <div class="row">
            <!-- [Leads] start -->
            <div class="col-xxl-12">
                <div class="card stretch stretch-full">
                    @if ($from === 'desktop')
                        <div class="card-header">
                            <h5 class="card-title">Neraca</h5>
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
                            <form id="form-balance-sheet-submit" method="POST">
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
                                                        <select class="form-control cust-control select-month"
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


                                                    <select 
                                                        class="form-control cust-control select-year" name="year_from" id="year_from">
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

                                                    <p class="sampai-dengan">s/d</p>
                                                    <p class="sampai-dengan-mobile"> - </p>

                                                    <div class="form-group">
                                                        <select class="form-control cust-control select-month2"
                                                            name="month_to" id="month_too">
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

                                                    <select class="form-control cust-control select-year2" name="year_to" id="year_too">
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
                                        <table class="table table-bordered" id="table-profit-loss">
                                            <tr>
                                                <th rowspan="2">
                                                    <center>Keterangan</center>
                                                </th>
                                                <th colspan="2">
                                                    <center> {{ date('F Y') }}</center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>*</th>
                                                <th>*</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="border-top:2px solid black;">
                                                    <strong>Aktiva Lancar</strong>
                                                </td>
                                            </tr>
                                            @php

                                                $total_lancar = 0;
                                            @endphp
                                            @foreach ($dt['aktiva_lancar'] as $i)
                                                @php
                                                    $lancar = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $i->id)
                                                        ->where('account_code_id', 1)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet - credit'));
                                                    $total_lancar = $total_lancar + $lancar;
                                                @endphp

                                                @if ($lancar != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $i->name }} </td>
                                                        <td style="text-align:right;">
                                                            {{ number_format($lancar) }} </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Aktiva Lancar</strong></td>
                                                <td></td>
                                                <td style="text-align:right;"> {{ number_format($total_lancar) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Aktiva Tetap</strong></td>
                                            </tr>
                                            @php

                                                $total_tetap = 0;
                                            @endphp
                                            @foreach ($dt['aktiva_tetap'] as $a)
                                                @php
                                                    $tetap = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 2)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet-credit'));
                                                    $total_tetap = $total_tetap + $tetap;
                                                @endphp
                                                @if ($tetap != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                        <td style="text-align:right;"> {{ number_format($tetap) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Aktiva Tetap</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">{{ number_format($total_tetap) }}
                                                </td>
                                            </tr>
                                            @php

                                                $total_aktiva = $total_lancar + $total_tetap;
                                            @endphp
                                            <tr>
                                                <td><strong>TOTAL AKTIVA</strong></td>
                                                <td></td>
                                                <td style="text-align:right;"><strong>
                                                        {{ number_format($total_aktiva) }} </strong></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Utang Jangka Pendek</strong></td>
                                            </tr>
                                            @php

                                                $total_pendek = 0;
                                            @endphp
                                            @foreach ($dt['utang_pendek'] as $a)
                                                @php
                                                    $pendek = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 4)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit-debet'));
                                                    $total_pendek = $total_pendek + $pendek;
                                                @endphp
                                                @if ($pendek != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                        <td style="text-align:right;">
                                                            {{ number_format($pendek) }} </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Utang Jangka Pendek</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    {{ number_format($total_pendek) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Utang Jangka Panjang</strong></td>
                                            </tr>
                                            @php

                                                $total_panjang = 0;
                                            @endphp
                                            @foreach ($dt['utang_panjang'] as $a)
                                                @php

                                                    $panjang = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 5)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit-debet'));
                                                    $total_panjang = $total_panjang + $panjang;
                                                @endphp
                                                @if ($panjang != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                        <td style="text-align:right;">
                                                            {{ number_format($panjang) }} </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Utang Jangka Panjang</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">{{ number_format($total_panjang) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3"><strong>Modal</strong></td>
                                            </tr>
                                            @php

                                                $total_modal = 0;
                                            @endphp
                                            @foreach ($dt['modal'] as $a)
                                                @php

                                                    $modal = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 6)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit-debet'));
                                                    $total_modal = $total_modal + $modal;
                                                @endphp
                                                @if ($modal != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp; {{ $a->name }} </td>
                                                        <td style="text-align:right;"> {{ number_format($modal) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp; LABA/RUGI BERSIH </td>
                                            <td style="text-align:right;"> {{ number_format($laba_bersih) }} </td>
                                            <td></td>
                                            <tr>
                                                <td><strong>Total Modal</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    {{ number_format($total_modal + $laba_bersih) }} </td>
                                            </tr>
                                            <tr>
                                                <td><strong>TOTAL UTANG DAN MODAL</strong></td>
                                                <td></td>
                                                <td style="text-align:right;"><strong>
                                                        {{ number_format($total_pendek + $total_panjang + $total_modal + $laba_bersih) }}</strong>
                                                </td>
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
