<!DOCTYPE html>
<html>

<head>
    <title>Export Neraca Saldo</title>

</head>

<body>

    <div class="container">
        <center>
            <h4><span style="font-size: 28px;">Neraca Saldo</span><br>
                <center>{{ date('F Y', $awal) }}</center>
            </h4>
            
        </center>


        <table class="table" width="100%" id="table-trial-balance">
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
                        <td>{{ number_format(abs($kredit)) }}</td>
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
                            $kredit = $ld;
                        } else {
                            $debit = $ld;
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
                <td style="border-top:2px solid black;"><strong>{{ number_format($total_debet) }}</strong></td>
                <td style="border-top:2px solid black;"><strong>{{ number_format(abs($total_credit)) }}</strong></td>
            </tr>
            <tr>

                <th></th>
                <th></th>
                <th></th>
            </tr>
        </table>


    </div>

</body>

</html>
