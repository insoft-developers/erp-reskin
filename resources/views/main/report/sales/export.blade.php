<table>
    <tr>
        <th colspan="2">Omset Penjualan</th>
        <td>{{ $cart['omset_penjualan']}}</td>
    </tr>
    <tr>
        <th colspan="2">Total Ongkir (+)</th>
        <td>{{ $cart['total_ongkir']}}</td>
    </tr>
    <tr>
        <th colspan="2">Total Diskon (-)</th>
        <td>{{ $cart['total_diskon']}}</td>
    </tr>
    <tr>
        <th colspan="2">Jumlah Terjual</th>
        <td>{{ $cart['jumlah_terjual']}}</td>
    </tr>
    {{-- <tr>
        <th colspan="2">Total Harga Produk Terjual</th>
        <td>{{ $cart['total_harga_produk_terjual']}}</td>
    </tr> --}}
    <tr>
        <th colspan="2">Total Pajak</th>
        <td>{{ $cart['total_pajak']}}</td>
    </tr>
    <tr>
        <th colspan="2">Biaya Biaya</th>
        <td>{{ $cart['biaya']}}</td>
    </tr>
    <tr>
        <th colspan="2">Harga Pokok Penjualan</th>
        <td>{{ $cart['hpp'] }}</td>
    </tr>
    <tr>
        <th colspan="2">Laba Rugi Bersih</th>
        <td>{{ $cart['laba_rugi_bersih'] }}</td>
    </tr>
    <tr>
        <th colspan="2">ROAS</th>
        <td>{{ $cart['roas']}}</td>
    </tr>
    
</table>

<table class="table table-striped" id="data-table">
    <thead>
        <tr>
            <th>#</th>
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
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['jumlah_penjualan'], 0, ',', '.') }}</td>
                <td>Rp {{ $item['harga_jual'] }}</td>
                <td>Rp {{ number_format($item['total_harga_produk_terjual'], 0, ',', '.') }}</td>
                <td>Rp {{ $item['hpp_produk'] }}</td>
                <td>Rp {{ number_format($item['hpp'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['margin_kotor'], 0, ',', '.') }}</td>
                <td>{{ $item['persentase_margin'] }}%</td>
            </tr>
        @endforeach
    </tbody>
</table>