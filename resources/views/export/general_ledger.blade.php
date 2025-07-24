@php

    use App\Http\Controllers\Main\ReportController;

@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Export Buku Besar</title>

</head>

<body>

    <div class="container">
        <center>
            <h4><span style="font-size: 28px;">Laporan Buku Besar</span><br>
                <center>{{ date('F Y', $awal) }}</center>
            </h4>

        </center>

        @php

            $estimations = explode('_', $estimasi);
            $account_id = $estimations[0];
            $account_asset_id = $estimations[1];

        @endphp


        <table class="table" width="100%" id="table-general-ledger">
            <tr>
                <th style="border-bottom: 2px solid black;">Tanggal</th>
                <th style="border-bottom: 2px solid black;">Estimasi</th>
                <th style="border-bottom: 2px solid black;">Keterangan</th>
                <th style="border-bottom: 2px solid black;">Debit</th>
                <th style="border-bottom: 2px solid black;">Kredit</th>
                <th style="border-bottom: 2px solid black;">Saldo</th>
            </tr>


            @php
                $saldo = 0;

                $total_debit = 0;
                $total_kredit = 0;

                $data = \App\Models\Journal::where('userid', ReportController::user_id_staff(session('id')))
                    ->where('created', '>=', $awal)
                    ->where('created', '<=', $akhir)
                    ->orderBy('created', 'asc')
                    ->orderBy('is_opening_balance', 'desc')
                    ->orderBy('id', 'asc')
                    ->get();
            @endphp

            @foreach ($data as $item)
                @if ($item->transaction_name == 'Saldo Awal' && $item->is_opening_balance == 1)
                    @php
                        $jlists = \App\Models\JournalList::where('journal_id', $item->id)
                            ->where('asset_data_id', $account_id)
                            ->where('account_code_id', $account_asset_id)
                            ->groupBy('asset_data_id')
                            ->get();
                    @endphp
                    @foreach ($jlists as $jlist)
                        @if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id)
                            @php
                                $saldo =
                                    $saldo +
                                    ReportController::get_view_data_controller(
                                        $item->id,
                                        $jlist->asset_data_id,
                                        $jlist->account_code_id,
                                    )['debet'] -
                                    ReportController::get_view_data_controller(
                                        $item->id,
                                        $jlist->asset_data_id,
                                        $jlist->account_code_id,
                                    )['credit'];
                                $total_debit =
                                    $total_debit +
                                    ReportController::get_view_data_controller(
                                        $item->id,
                                        $jlist->asset_data_id,
                                        $jlist->account_code_id,
                                    )['debet'];
                                $total_kredit =
                                    $total_kredit +
                                    ReportController::get_view_data_controller(
                                        $item->id,
                                        $jlist->asset_data_id,
                                        $jlist->account_code_id,
                                    )['credit'];
                            @endphp
                            <tr>
                                <td>{{ date('d-m-Y', $jlist->created) }}</td>
                                <td>{{ $jlist->asset_data_name }}</td>
                                <td>{{ $item->transaction_name }}</td>
                                <td>{{ number_format(ReportController::get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['debet']) }}
                                </td>
                                <td>{{ number_format(ReportController::get_view_data_controller($item->id, $jlist->asset_data_id, $jlist->account_code_id)['credit']) }}
                                </td>
                                @if (
                                    $jlist->account_code_id == 1 ||
                                        $jlist->account_code_id == 2 ||
                                        $jlist->account_code_id == 8 ||
                                        $jlist->account_code_id == 9 ||
                                        $jlist->account_code_id == 10 ||
                                        $jlist->account_code_id == 12)
                                    <td>{{ number_format($saldo) }}</td>
                                @else
                                    <td>{{ number_format(abs($saldo)) }}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                @else
                    @php
                        $jlists = \App\Models\JournalList::where('journal_id', $item->id)
                            ->where('asset_data_id', $account_id)
                            ->where('account_code_id', $account_asset_id)
                            ->orderBy('journal_id', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();
                    @endphp
                    @foreach ($jlists as $jlist)
                        @if ($jlist->asset_data_id == $account_id && $jlist->account_code_id == $account_asset_id)
                            @php
                                $saldo = $saldo + $jlist->debet - $jlist->credit;
                                $total_debit = $total_debit + $jlist->debet;
                                $total_kredit = $total_kredit + $jlist->credit;
                            @endphp
                            <tr>
                                <td>{{ date('d-m-Y', $jlist->created) }}</td>
                                <td>{{ $jlist->asset_data_name }}</td>
                                <td>{{ $item->transaction_name }}</td>
                                <td>{{ number_format($jlist->debet) }}</td>
                                <td>{{ number_format($jlist->credit) }}</td>
                                @if (
                                    $jlist->account_code_id == 1 ||
                                        $jlist->account_code_id == 2 ||
                                        $jlist->account_code_id == 8 ||
                                        $jlist->account_code_id == 9 ||
                                        $jlist->account_code_id == 10 ||
                                        $jlist->account_code_id == 12)
                                    <td>{{ number_format($saldo) }}</td>
                                @else
                                    <td>{{ number_format(abs($saldo)) }}</td>
                                @endif
                            </tr>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <tr>

                <td style="border-top:2px solid black;"></td>
                <td style="border-top:2px solid black;"></td>
                <td style="border-top:2px solid black;"><strong>Total</strong></td>

                <td style="border-top:2px solid black;"><strong>{{ number_format($total_debit) }}</strong></td>
                <td style="border-top:2px solid black;"><strong>{{ number_format($total_kredit) }}</strong></td>
                @if (
                    $account_asset_id == 1 ||
                        $account_asset_id == 2 ||
                        $account_asset_id == 8 ||
                        $account_asset_id == 9 ||
                        $account_asset_id == 10 ||
                        $account_asset_id == 12)
                    <td style="border-top:2px solid black;">
                        <strong>{{ number_format($total_debit - $total_kredit) }}</strong>
                    </td>
                @else
                    <td style="border-top:2px solid black;">
                        <strong>{{ number_format(abs($total_debit - $total_kredit)) }}</strong>
                    </td>
                @endif
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </table>


    </div>

</body>

</html>
