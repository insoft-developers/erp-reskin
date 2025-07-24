<div class="modal-content">
    <div class="modal-header" style="background-color: #2f467a;">
        <h5 class="modal-title" style="color:white;">Penyusutan {{ $data->name }}</h5>
    </div>
    <div class="modal-body">
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Nilai Buku Awal (Rp)</th>
                    <th>Penyusutan (Rp)</th>
                    <th>Nilai Buku Akhir (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->shrinkageSimulate as $item)
                    <tr>
                        <td>{{ $item->month }}</td>
                        <td>Rp. {{ number_format($item->initial_book_value) }}</td>
                        <td>Rp. {{ number_format($item->shrinkage) }}</td>
                        <td>Rp. {{ number_format($item->final_book_value) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</div>