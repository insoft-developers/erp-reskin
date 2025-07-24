<table class="table table-striped" id="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Kode Transaksi</th>
            <th>Tanggal Waktu</th>
            <th>Nama Konsumen</th>
            <th>Sub Total Pesanan</th>
            <th>Nominal Pajak</th>
            <th>Metode Pembayaran</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->reference }}</td>
                <td>{{ $item->created }}</td>
                <td>{{ $item->customer }}</td>
                <td>{{ $item->paid }}</td>
                <td>{{ $item->tax }}</td>
                <td>{{ $item->payment_method }}</td>
            </tr>
        @endforeach
    </tbody>
</table>