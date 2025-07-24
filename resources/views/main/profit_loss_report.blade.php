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
                            <li class="breadcrumb-item">Laba Rugi</li>
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
                                    <h5 class="card-title">Laba Rugi</h5>
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
                                        <form id="form-profit-loss-submit" method="POST">
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

                                                    <select
                                                        class="form-control cust-control select-year2" name="year_to" id="year_too">
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
                                        <div class="mtop30"></div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="table-profit-loss">
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>Keterangan</center>
                                                            </th>
                                                            <th colspan="2">
                                                                <center>{{ date('F Y') }}</center>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th>*</th>
                                                            <th>*</th>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" style="border-top:2px solid black;">
                                                                <strong>Pendapatan</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $total_income = 0;
                                                        @endphp
                                                        @foreach ($data['income'] as $i)
                                                            @php
                                                                $income = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $i->id)
                                                                    ->where('account_code_id', 7)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit - debet'));
                                                                $total_income = $total_income + $income;
                                                            @endphp
                                                            @if ($income > 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $i->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($income) }}
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Pendapatan Bersih</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_income) }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Harga Pokok Penjualan</strong></td>
                                                        </tr>
                                                        @php
                                                            $total_hpp = 0;
                                                        @endphp
                                                        @foreach ($data['hpp'] as $a)
                                                            @php
                                                                $hpp = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 8)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet-credit'));
                                                                $total_hpp = $total_hpp + $hpp;
                                                            @endphp
                                                            @if ($hpp != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        ({{ number_format($hpp) }})
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Harga Pokok Penjualan</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                ({{ number_format($total_hpp) }})
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $laba_rugi_kotor = $total_income - $total_hpp;
                                                        @endphp
                                                        <tr>
                                                            <td><strong>LABA/RUGI KOTOR</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                <strong>{{ number_format($laba_rugi_kotor) }}</strong>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Biaya Penjualan</strong></td>
                                                        </tr>
                                                        @php
                                                            $total_selling_cost = 0;
                                                        @endphp
                                                        @foreach ($data['selling_cost'] as $a)
                                                            @php
                                                                $selling_cost = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 9)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet-credit'));
                                                                $total_selling_cost =
                                                                    $total_selling_cost + $selling_cost;
                                                            @endphp
                                                            @if ($selling_cost != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        ({{ number_format($selling_cost) }})
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Biaya Penjualan</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                ({{ number_format($total_selling_cost) }})</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"><strong>Biaya Umum Admin</strong></td>
                                                        </tr>
                                                        @php
                                                            $total_general_fees = 0;
                                                        @endphp
                                                        @foreach ($data['general_fees'] as $a)
                                                            @php
                                                                $general_fees = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 10)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet-credit'));
                                                                $total_general_fees =
                                                                    $total_general_fees + $general_fees;
                                                            @endphp
                                                            @if ($general_fees != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        ({{ number_format($general_fees) }})
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Biaya Admin dan Umum</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                ({{ number_format($total_general_fees) }})</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="3"><strong>Pendapatan Diluar Usaha</strong>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $total_nb_income = 0;
                                                        @endphp
                                                        @foreach ($data['non_business_income'] as $a)
                                                            @php
                                                                $nb_income = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 11)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('credit-debet'));
                                                                $total_nb_income = $total_nb_income + $nb_income;
                                                            @endphp
                                                            @if ($nb_income != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        {{ number_format($nb_income) }}</td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Pendapatan Diluar Usaha</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                {{ number_format($total_nb_income) }}</td>
                                                        </tr>


                                                        <tr>
                                                            <td colspan="3"><strong>Biaya Diluar Usaha</strong></td>
                                                        </tr>
                                                        @php
                                                            $total_nb_cost = 0;
                                                        @endphp
                                                        @foreach ($data['non_business_cost'] as $a)
                                                            @php
                                                                $nb_cost = DB::table('ml_journal_list')
                                                                    ->where('asset_data_id', $a->id)
                                                                    ->where('account_code_id', 12)
                                                                    ->where('created', '>=', $awal)
                                                                    ->where('created', '<=', $akhir)
                                                                    ->sum(\DB::raw('debet-credit'));
                                                                $total_nb_cost = $total_nb_cost + $nb_cost;
                                                            @endphp
                                                            @if ($nb_cost != 0)
                                                                <tr>
                                                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                                    <td style="text-align:right;">
                                                                        ({{ number_format($nb_cost) }})
                                                                    </td>
                                                                    <td></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                        <tr>
                                                            <td><strong>Total Biaya Diluar Usaha</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                ({{ number_format($total_nb_cost) }})</td>
                                                        </tr>
                                                        @php
                                                            $laba_bersih =
                                                                $laba_rugi_kotor -
                                                                $total_selling_cost -
                                                                $total_general_fees +
                                                                $total_nb_income -
                                                                $total_nb_cost;
                                                        @endphp
                                                        <tr>
                                                            <td><strong>LABA/RUGI BERSIH</strong></td>
                                                            <td></td>
                                                            <td style="text-align:right;">
                                                                <strong>{{ number_format($laba_bersih) }}</strong>
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
                            <h5 class="card-title">Laba Rugi</h5>
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
                            <form id="form-profit-loss-submit" method="POST">
                                @csrf

                                <div class="row">
                                    @if ($from !== 'desktop')
                                        <div class="col-12 mb-3">
                                            <div class="button-export" style="display: flex;">
                                                <button onclick="export_excel()" style="margin-right:8px";
                                                    class="btn btn-sm btn-success">Export XLS</button>
                                                <button onclick="export_pdf()" class="btn btn-sm btn-danger">Export
                                                    PDF</button>
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


                                        <select style="width:150px;margin-left:5px;" class="form-control cust-control"
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

                                        <p class="sampai-dengan">s/d</p>

                                        <div class="form-group">
                                            <select style="width:200px;" class="form-control cust-control"
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

                                        <select style="width:200px;margin-left:5px;" class="form-control cust-control"
                                            name="year_to" id="year_too">
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
                            <div class="mtop30"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table-profit-loss">
                                            <tr>
                                                <th rowspan="2">
                                                    <center>Keterangan</center>
                                                </th>
                                                <th colspan="2">
                                                    <center>{{ date('F Y') }}</center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>*</th>
                                                <th>*</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3" style="border-top:2px solid black;">
                                                    <strong>Pendapatan</strong>
                                                </td>
                                            </tr>
                                            @php
                                                $total_income = 0;
                                            @endphp
                                            @foreach ($data['income'] as $i)
                                                @php
                                                    $income = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $i->id)
                                                        ->where('account_code_id', 7)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit - debet'));
                                                    $total_income = $total_income + $income;
                                                @endphp
                                                @if ($income > 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $i->name }}</td>
                                                        <td style="text-align:right;">{{ number_format($income) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Pendapatan Bersih</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">{{ number_format($total_income) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Harga Pokok Penjualan</strong></td>
                                            </tr>
                                            @php
                                                $total_hpp = 0;
                                            @endphp
                                            @foreach ($data['hpp'] as $a)
                                                @php
                                                    $hpp = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 8)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet-credit'));
                                                    $total_hpp = $total_hpp + $hpp;
                                                @endphp
                                                @if ($hpp != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                        <td style="text-align:right;">({{ number_format($hpp) }})
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Harga Pokok Penjualan</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">({{ number_format($total_hpp) }})
                                                </td>
                                            </tr>
                                            @php
                                                $laba_rugi_kotor = $total_income - $total_hpp;
                                            @endphp
                                            <tr>
                                                <td><strong>LABA/RUGI KOTOR</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    <strong>{{ number_format($laba_rugi_kotor) }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Biaya Penjualan</strong></td>
                                            </tr>
                                            @php
                                                $total_selling_cost = 0;
                                            @endphp
                                            @foreach ($data['selling_cost'] as $a)
                                                @php
                                                    $selling_cost = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 9)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet-credit'));
                                                    $total_selling_cost = $total_selling_cost + $selling_cost;
                                                @endphp
                                                @if ($selling_cost != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                        <td style="text-align:right;">
                                                            ({{ number_format($selling_cost) }})
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Biaya Penjualan</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    ({{ number_format($total_selling_cost) }})</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><strong>Biaya Umum Admin</strong></td>
                                            </tr>
                                            @php
                                                $total_general_fees = 0;
                                            @endphp
                                            @foreach ($data['general_fees'] as $a)
                                                @php
                                                    $general_fees = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 10)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet-credit'));
                                                    $total_general_fees = $total_general_fees + $general_fees;
                                                @endphp
                                                @if ($general_fees != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                        <td style="text-align:right;">
                                                            ({{ number_format($general_fees) }})
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Biaya Admin dan Umum</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    ({{ number_format($total_general_fees) }})</td>
                                            </tr>

                                            <tr>
                                                <td colspan="3"><strong>Pendapatan Diluar Usaha</strong></td>
                                            </tr>
                                            @php
                                                $total_nb_income = 0;
                                            @endphp
                                            @foreach ($data['non_business_income'] as $a)
                                                @php
                                                    $nb_income = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 11)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('credit-debet'));
                                                    $total_nb_income = $total_nb_income + $nb_income;
                                                @endphp
                                                @if ($nb_income != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                        <td style="text-align:right;">
                                                            {{ number_format($nb_income) }}</td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Pendapatan Diluar Usaha</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    {{ number_format($total_nb_income) }}</td>
                                            </tr>


                                            <tr>
                                                <td colspan="3"><strong>Biaya Diluar Usaha</strong></td>
                                            </tr>
                                            @php
                                                $total_nb_cost = 0;
                                            @endphp
                                            @foreach ($data['non_business_cost'] as $a)
                                                @php
                                                    $nb_cost = DB::table('ml_journal_list')
                                                        ->where('asset_data_id', $a->id)
                                                        ->where('account_code_id', 12)
                                                        ->where('created', '>=', $awal)
                                                        ->where('created', '<=', $akhir)
                                                        ->sum(\DB::raw('debet-credit'));
                                                    $total_nb_cost = $total_nb_cost + $nb_cost;
                                                @endphp
                                                @if ($nb_cost != 0)
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;{{ $a->name }}</td>
                                                        <td style="text-align:right;">
                                                            ({{ number_format($nb_cost) }})
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            <tr>
                                                <td><strong>Total Biaya Diluar Usaha</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    ({{ number_format($total_nb_cost) }})</td>
                                            </tr>
                                            @php
                                                $laba_bersih =
                                                    $laba_rugi_kotor -
                                                    $total_selling_cost -
                                                    $total_general_fees +
                                                    $total_nb_income -
                                                    $total_nb_cost;
                                            @endphp
                                            <tr>
                                                <td><strong>LABA/RUGI BERSIH</strong></td>
                                                <td></td>
                                                <td style="text-align:right;">
                                                    <strong>{{ number_format($laba_bersih) }}</strong>
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
