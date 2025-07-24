<div class="modal-content">
    <div class="modal-header" style="background-color: #2f467a;">
        <h5 class="modal-title" style="color:white;">Histori Utang {{ $data->name }}</h5>
    </div>
    <div class="modal-body table-responsive">
        <div class="alert alert-info">
            <i class="fa fa-info-circle mb-3"></i> Tabel ini menampilkan semua histori pembayaran
            <p>Keterangan : {{ $data->note }}</p>
        </div>
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Sync Jurnal</th>

                    <th>Tanggal</th>
                    <th>Bayar Dari</th>
                    <th>Bayar Untuk</th>
                    <th>Nominal Bayar</th>
                    <th>Sisa Utang</th>
                    <th>Keterangan</th>


                </tr>
            </thead>
            <tbody>
                @foreach ($data->debt_payment_history as $item)
                    <tr>
                        <td>
                            @if ($item->sync_status == 1)
                                <a href="javascript:void(0)" class="delete btn btn-success btn-sm m-1"><i
                                        class="fa fa-sync"></i>&nbsp;Sync</a>
                            @else
                                <a href="javascript:void(0)" class="delete btn btn-success btn-sm m-1"
                                    onclick="payment_sync({{ $item->id }})"><i class="fa fa-sync"></i>&nbsp;Sync</a>
                            @endif
                        </td>
                        @if ($item->sync_status == 1)
                            <td>
                                <div style="color:green;"><strong><i class="fa fa-check"></i>&nbsp;&nbsp;Sync</strong>
                                </div>
                            </td>
                        @else
                            <td>
                                <div style="color:red;">Not Sync</div>
                            </td>
                        @endif
                        <td>{{ $item->created_at->format('d-M-Y') }}</td>
                        <td>{{ $item->payment_from->name ?? null }}</td>
                        <td>{{ $item->payment_to($data->type)->name ?? null }}</td>
                        <td>Rp. {{ number_format($item->amount) }}</td>
                        <td>Rp. {{ number_format($item->balance) }}</td>
                        <td>{{ $item->note }}</td>




                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</div>
