<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('template/main') }}/css/theme.min.css" />

    <style>
        body {
            line-height: 1.6;
            color: unset;
            overflow-x: hidden;
            font-size: .84rem;
            scroll-behavior: smooth;
            font-family: Inter, sans-serif;
            transition: all .3s ease;
            -webkit-font-smoothing: antialiased;
            background-color: unset
        }

        table {
            border-collapse: collapse; /* Agar border tidak double */
            width: 100%; /* Optional: supaya tabel mengambil lebar penuh */
        }

        table, th, td {
            border: 1px solid black; /* Border 1px pada tabel, th, dan td */
        }

        th, td {
            padding: 8px; /* Memberikan sedikit padding untuk spasi dalam sel */
            text-align: left; /* Mengatur teks agar rata kiri */
        }

        .noborder, th, td{
            border: 0px;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">LAPORAN PENJUALAN {{ strtoupper($dateName) }}</h2>
    <table class="noborder" style="text-align: left; width: 100%; margin-bottom: 50px;" >
        <tr>
            <th>Omset Penjualan</th>
            <td>{{ $cart['omset_penjualan']}}</td>

            <th>Total Ongkir (+)</th>
            <td>{{ $cart['total_ongkir']}}</td>

            <th>Total Diskon (-)</th>
            <td>{{ $cart['total_diskon']}}</td>
        </tr>
        <tr>
            <th>Jumlah Terjual</th>
            <td>{{ $cart['jumlah_terjual']}}</td>

            {{-- <th>Total Harga Produk Terjual</th>
            <td>{{ $cart['total_harga_produk_terjual']}}</td> --}}
            <th>Total Pajak</th>
            <td>{{ $cart['total_pajak']}}</td>

            <th>Harga Pokok Penjualan</th>
            <td>{{ $cart['hpp']}}</td>
        </tr>
        <tr>
            <th>Laba Rugi Bersih</th>
            <td>{{ $cart['laba_rugi_bersih']}}</td>

            <th>Biaya Biaya</th>
            <td>{{ $cart['biaya']}}</td>

            <th>ROAS</th>
            <td>{{ $cart['roas']}}</td>
        </tr>
        
        <tr>
            {{-- <td colspan="6" style="text-align: center;"><strong>Total Pajak</strong> {{ $cart['total_pajak']}}</td> --}}
        </tr>
    </table>
    
    <table class="table table-striped" style="width: 100%;" id="data-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jumlah Terjual</th>
                <th>Harga Jual Produk</th>
                <th>Omset Penjualan</th>
                <th>HPP Produk</th>
                <th>HPP Total</th>
                <th>Margin Kotor</th>
                <th>Persentase Margin</th>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['jumlah_penjualan'], 0, ',', '.') }}</td>
                    <td>{{ $item['harga_jual'] }}</td>
                    <td>{{ number_format($item['total_harga_produk_terjual'], 0, ',', '.') }}</td>
                    <td>{{ $item['hpp_produk'] }}</td>
                    <td>{{ number_format($item['hpp'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['margin_kotor'], 0, ',', '.') }}</td>
                    <td>{{ $item['persentase_margin'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>