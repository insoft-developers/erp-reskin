@php

    use App\Http\Controllers\Main\ReportController;

@endphp

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
                            <li class="breadcrumb-item">Jurnal</li>
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
                                    <h5 class="card-title">Jurnal</h5>
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
                                        <form id="form-journal-report-submit" method="POST">
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
                                                    <table class="table">
                                                        <tr>
                                                            <th style="border-bottom: 2px solid black;">Tanggal</th>
                                                            <th style="border-bottom: 2px solid black;">Keterangan</th>
                                                            <th style="border-bottom: 2px solid black;">Debit</th>
                                                            <th style="border-bottom: 2px solid black;">Kredit</th>
                                                        </tr>
                                                        @php
                                                            $total_debet = 0;
                                                            $total_credit = 0;
                                                        @endphp
                                                        @foreach ($data as $key)
                                                            @php

                                                                if (
                                                                    $key->transaction_name == 'Saldo Awal' &&
                                                                    $key->is_opening_balance == 1
                                                                ) {
                                                                    $detail = \App\Models\JournalList::where(
                                                                        'journal_id',
                                                                        $key->id,
                                                                    )
                                                                        ->groupBy(['asset_data_id', 'account_code_id'])
                                                                        ->orderBy('id', 'asc')
                                                                        ->get();
                                                                } else {
                                                                    $detail = \App\Models\JournalList::where(
                                                                        'journal_id',
                                                                        $key->id,
                                                                    )
                                                                        ->orderBy('id', 'asc')
                                                                        ->get();
                                                                }

                                                            @endphp
                                                            <tr>
                                                                <td style="background-color:whitesmoke;"></td>
                                                                <td style="background-color:whitesmoke;">
                                                                    <strong>{{ $key->transaction_name }}</strong>
                                                                </td>
                                                                <td style="background-color:whitesmoke;"></td>
                                                                <td style="background-color:whitesmoke;"></td>
                                                            </tr>

                                                            @foreach ($detail as $item)
                                                                @php
                                                                    if (
                                                                        $key->transaction_name == 'Saldo Awal' &&
                                                                        $key->is_opening_balance == 1
                                                                    ) {
                                                                        $total_debet =
                                                                            $total_debet +
                                                                            ReportController::get_view_data_controller(
                                                                                $key->id,
                                                                                $item->asset_data_id,
                                                                                $item->account_code_id,
                                                                            )['debet'];
                                                                        $total_credit =
                                                                            $total_credit +
                                                                            ReportController::get_view_data_controller(
                                                                                $key->id,
                                                                                $item->asset_data_id,
                                                                                $item->account_code_id,
                                                                            )['credit'];
                                                                    } else {
                                                                        $total_debet = $total_debet + $item->debet;
                                                                        $total_credit = $total_credit + $item->credit;
                                                                    }

                                                                @endphp
                                                                @if ($key->transaction_name == 'Saldo Awal' && $key->is_opening_balance == 1)
                                                                    <tr>
                                                                        <td>{{ date('d-m-Y', $item->created) }}</td>
                                                                        <td>{{ $item->asset_data_name }}</td>
                                                                        <td>{{ number_format(ReportController::get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['debet']) }}
                                                                        </td>
                                                                        <td>{{ number_format(ReportController::get_view_data_controller($key->id, $item->asset_data_id, $item->account_code_id)['credit']) }}
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td>{{ date('d-m-Y', $item->created) }}</td>
                                                                        <td>{{ $item->asset_data_name }}</td>
                                                                        <td>{{ number_format($item->debet) }}</td>
                                                                        <td>{{ number_format($item->credit) }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                        @endforeach
                                                        <tr>
                                                            <th style="border-top:2px solid black;">Total</th>
                                                            <th style="border-top:2px solid black;"></th>
                                                            <th style="border-top:2px solid black;">
                                                                {{ number_format($total_debet) }}</th>
                                                            <th style="border-top:2px solid black;">
                                                                {{ number_format($total_credit) }}</th>
                                                        </tr>
                                                        <tr>
                                                            <th></th>
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
                            <h5 class="card-title">Jurnal</h5>
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
                            <form id="form-journal-report-submit" method="POST">
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
                                            id="btn-submit-profit-loss" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                            <div class="mtop20"></div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tr>
                                                <th style="border-bottom: 2px solid black;">Tanggal</th>
                                                <th style="border-bottom: 2px solid black;">Keterangan</th>
                                                <th style="border-bottom: 2px solid black;">Debit</th>
                                                <th style="border-bottom: 2px solid black;">Kredit</th>
                                            </tr>
                                            @php
                                                $total_debet = 0;
                                                $total_credit = 0;
                                            @endphp
                                            @foreach ($data as $key)
                                                @php
                                                    $detail = \App\Models\JournalList::where(
                                                        'journal_id',
                                                        $key->id,
                                                    )->get();

                                                @endphp
                                                <tr>
                                                    <td style="background-color:whitesmoke;"></td>
                                                    <td style="background-color:whitesmoke;">
                                                        <strong>{{ $key->transaction_name }}</strong>
                                                    </td>
                                                    <td style="background-color:whitesmoke;"></td>
                                                    <td style="background-color:whitesmoke;"></td>
                                                </tr>

                                                @foreach ($detail as $item)
                                                    @php
                                                        $total_debet = $total_debet + $item->debet;
                                                        $total_credit = $total_credit + $item->credit;
                                                    @endphp
                                                    <tr>
                                                        <td>{{ date('d-m-Y', $item->created) }}</td>
                                                        <td>{{ $item->asset_data_name }}</td>
                                                        <td>{{ number_format($item->debet) }}</td>
                                                        <td>{{ number_format($item->credit) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                            <tr>
                                                <th style="border-top:2px solid black;">Total</th>
                                                <th style="border-top:2px solid black;"></th>
                                                <th style="border-top:2px solid black;">
                                                    {{ number_format($total_debet) }}</th>
                                                <th style="border-top:2px solid black;">
                                                    {{ number_format($total_credit) }}</th>
                                            </tr>
                                            <tr>
                                                <th></th>
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


            <!-- [Recent Orders] end -->
            <!-- [] start -->
        </div>
    @endif
@endsection
