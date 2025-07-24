@php

    use App\Http\Controllers\Main\ReportController;

@endphp

<!DOCTYPE html>
<html>

<head>
    <title>Export Laporan Jurnal</title>


</head>

<body>

    <div class="container">
        <center>
            <h4><span style="font-size: 28px;">Laporan Jurnal</span><br>
                <center>{{ date('F Y', $awal) }}</center>
            </h4>

        </center>


        <table class="table" width="100%">
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

                    if ($key->transaction_name == 'Saldo Awal' && $key->is_opening_balance == 1) {
                        $detail = \App\Models\JournalList::where('journal_id', $key->id)
                            ->groupBy(['asset_data_id', 'account_code_id'])
                            ->get();
                    } else {
                        $detail = \App\Models\JournalList::where('journal_id', $key->id)
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
                        if ($key->transaction_name == 'Saldo Awal' && $key->is_opening_balance == 1) {
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

</body>

</html>
