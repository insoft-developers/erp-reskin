@if ($view == 'initial-delete')
    <script>
        $("#btn-submit-initial-delete").click(function() {
            Swal.fire({
                title: "Hapus Saldo Awal?",
                text: "Apakah anda yakin ingin menghapus saldo awal..?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_hapus_saldo();

                }
            });
        })


        function confirm_hapus_saldo() {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var bulan = $("#month").val();
            var tahun = $("#year").val();
            $.ajax({
                url: "{{ url('confirm_hapus_saldo') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "bulan": bulan,
                    "tahun": tahun,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            html: data.message,
                            icon: "success"
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed, Terjadi Kesalahan!",
                            icon: "error"
                        });
                    }
                }
            })
        }
    </script>
@endif
