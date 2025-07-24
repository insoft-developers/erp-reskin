<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: 900; font-size: 15px;">Laporan Stok Barang Jadi</th>
            <th style="text-align: center; font-weight: 900; font-size: 15px;">Total Nilai Stok Akhir: Rp {{ number_format(collect($data[0])->sum('stock_value'), 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Awal Bulan (Unit)</th>
            <th>Pemasukan (Unit)</th>
            <th>Pengeluaran (Unit)</th>
            <th>Jumlah Akhir Bulan (Unit)</th>
            <th>Harga Satuan (Rp)</th>
            <th>Nilai Stok Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data[0] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['initial_stock']) }}</td>
                <td>{{ number_format($item['total_in']) }}</td>
                <td>{{ number_format($item['total_out']) }}</td>
                <td>{{ number_format($item['final_stock']) }}</td>
                <td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['stock_value'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: 900; font-size: 15px;">Laporan Stok Setengah Jadi</th>
            <th style="text-align: center; font-weight: 900; font-size: 15px;">Total Nilai Stok Akhir: Rp {{ number_format(collect($data[1])->sum('stock_value'), 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Awal Bulan (Unit)</th>
            <th>Pemasukan (Unit)</th>
            <th>Pengeluaran (Unit)</th>
            <th>Jumlah Akhir Bulan (Unit)</th>
            <th>Harga Satuan (Rp)</th>
            <th>Nilai Stok Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data[1] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['initial_stock']) }}</td>
                <td>{{ number_format($item['total_in']) }}</td>
                <td>{{ number_format($item['total_out']) }}</td>
                <td>{{ number_format($item['final_stock']) }}</td>
                <td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['stock_value'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: 900; font-size: 15px;">Laporan Stok Produk Manufaktur</th>
            <th style="text-align: center; font-weight: 900; font-size: 15px;">Total Nilai Stok Akhir: Rp {{ number_format(collect($data[2])->sum('stock_value'), 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Awal Bulan (Unit)</th>
            <th>Pemasukan (Unit)</th>
            <th>Pengeluaran (Unit)</th>
            <th>Jumlah Akhir Bulan (Unit)</th>
            <th>Harga Satuan (Rp)</th>
            <th>Nilai Stok Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data[2] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['initial_stock']) }}</td>
                <td>{{ number_format($item['total_in']) }}</td>
                <td>{{ number_format($item['total_out']) }}</td>
                <td>{{ number_format($item['final_stock']) }}</td>
                <td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['stock_value'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="table table-striped">
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-weight: 900; font-size: 15px;">Laporan Stok Bahan Baku</th>
            <th style="text-align: center; font-weight: 900; font-size: 15px;">Total Nilai Stok Akhir: Rp {{ number_format(collect($data[3])->sum('stock_value'), 0, ',', '.') }}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Produk</th>
            <th>Jumlah Awal Bulan (Unit)</th>
            <th>Pemasukan (Unit)</th>
            <th>Pengeluaran (Unit)</th>
            <th>Jumlah Akhir Bulan (Unit)</th>
            <th>Harga Satuan (Rp)</th>
            <th>Nilai Stok Akhir (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data[3] as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ number_format($item['initial_stock']) }}</td>
                <td>{{ number_format($item['total_in']) }}</td>
                <td>{{ number_format($item['total_out']) }}</td>
                <td>{{ number_format($item['final_stock']) }}</td>
                <td>Rp {{ number_format($item['unit_price'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item['stock_value'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>