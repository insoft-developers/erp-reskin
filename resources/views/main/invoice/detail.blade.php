<div class="modal-content">
    <div class="modal-header" style="background-color: #2f467a;">
        <h5 class="modal-title" style="color:white;">Termin {{ $data->is_quotation == 1 ? 'Quotation' : 'Invoice' }} {{ $data->name }}</h5>
    </div>
    <div class="modal-body table-responsive">
        <table id="data-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Copy Link</th>
                    <th>Termin</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data->termin as $item)
                    <tr>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('invoice.export', [$data->id, $item->id]) }}" target="_blank" class="btn-success btn-sm me-2" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Download Invoice">
                                    <img src="{{ asset('template/main/images/icon-download-pdf.png') }}" width="20px">
                                </a>
                                <a title="Bayar" style="margin-right:2px;" href="javascript:void(0);" onclick="confirmationChangePaid({{ $item->id }})" class="avatar-text avatar-md bg-warning text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-dollar"></i></a>
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('invoice.preview', [$data->id, $item->id]) }}" target="_blank" style="background-color: #385a9c !important;" class="btn-primary btn-sm me-2 copyLinkButtonTermin" data-url="{{ route('invoice.preview', [$data->id, $item->id]) }}" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Copy Invoice">
                                <i class="fa fa-copy"></i> Invoice Link
                            </a>
                            @if ($item->payment_url != null)
                                <a href="{{ $item->payment_url }}" target="_blank" style="background-color: #385a9c !important;" class="btn-primary btn-sm me-2 copyLinkButtonTermin" data-url="{{ $item->payment_url }}" style="padding: 8px; font-size: 9px; border-radius: 4px;" title="Copy Payment Link">
                                    <i class="fa fa-copy"></i> Payment Link
                                </a>
                            @endif
                        </td>
                        <td>Termin {{ $item->number }}</td>
                        <td>{{ $item->payment_method }}</td>
                        <td>
                            @if ($item->status == 1) 
                                <span class="badge bg-success">Paid</span>
                            @elseif($item->status == 2) 
                                <span class="badge bg-danger">Canceled</span>
                            @else
                                <span class="badge bg-warning">Unpaid</span>
                            @endif
                        </td>
                        <td>{{ $item->date }}</td>
                        <td>Rp. {{ number_format($item->nominal) }}</td>
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

<script>
    function confirmationChangePaid(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        })
        .then((result) => {
            if (result.isConfirmed) {
                changePaidTermin(id);
            }
        });
    }

    function changePaidTermin(id) {
        var postForm = {
            '_token': '{{ csrf_token() }}',
        };

        $.ajax({
            url: "{{ route('invoice.changePaidTermin', ':id') }}".replace(':id', id),
            type: 'POST', 
            data : postForm,
            dataType  : 'json',
        })
        .done(function(data) {
            Swal.fire({
                title: 'Success',
                text: data.message,
                icon: 'success',
            });
        })
        .fail(function() {
            Swal.fire('Error!', 'An error occurred while deleting the record.', 'error');
        });
    }

    $(document).on('click', '.copyLinkButtonTermin', function() {
        var paymentLink = $(this).data('url'); // Ambil URL dari data attribute

        if (navigator.clipboard && window.isSecureContext) {
            // Menggunakan Clipboard API jika tersedia dan aman
            navigator.clipboard.writeText(paymentLink).then(function() {
                toastr.success('Link copied to clipboard!');
            }, function(err) {
                toastr.error('Failed to copy link: ', err);
            });
        } else {
            // Fallback untuk browser yang tidak mendukung navigator.clipboard
            var tempInput = document.createElement('textarea');
            tempInput.value = paymentLink;
            document.body.appendChild(tempInput);
            tempInput.select();
            try {
                document.execCommand('copy');
                toastr.success('Link copied to clipboard!');
            } catch (err) {
                toastr.error('Failed to copy link: ', err);
            }
            document.body.removeChild(tempInput);
        }

        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "toastClass": "toast toast-custom"
        };
    });

</script>