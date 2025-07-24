@if ($view == 'account-setting-detail')
    <script>
        function account_delete(id, code) {
            Swal.fire({
                title: "Hapus Akun?",
                text: "Apakah anda yakin ingin menghapus akun ini..?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_hapus_akun(id, code);

                }
            });
        }



        function confirm_hapus_akun(id, code) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var link = $("#redirect-link").val();
            $.ajax({
                url: "{{ route('confirm.hapus.akun') }}",
                dataType: "JSON",
                type: "POST",
                data: {
                    "id": id,
                    "code": code,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        window.location = link;
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: data.message,
                            icon: "error"
                        });
                    }


                }
            })
        }


        let nomor = "{{ $data->count() }}";
        let account_code_id = "{{ $data[0]->account_code_id }}";

        function add_item() {

            var html = '';
            html += '<div class="row" id="baris_' + nomor + '">';
            html += '<div class="col-md-11">';
            html += '<div class="form-group mtop20">';
            html += '<input name="account_item[]" id="account_item_' + nomor +
                '" type="text" class="form-control cust-control">';
            html += '</div>';
            html += '<input type="hidden" name="id[]">';
            html += '<input type="hidden" name="account_code_id[]" value="' + account_code_id + '">';
            html += '</div>';
            html += '<div style="margin-top:20px;" class="col-md-1">';
            html += '<i onclick="account_hapus_item(' + nomor + ')" class="fa fa-remove btn-hapus-akun"></i>';
            html += '</div>';
            html += '</div>';

            $("#setting-input-container").append(html);
            nomor++;

        }

        function account_hapus_item(nomor) {
            $("#baris_" + nomor).remove();
        }

        $("#form-setting-account").submit(function(e) {
            e.preventDefault();

            var link = $("#redirect-link").val();
            $.ajax({
                url: "{{ url('save_setting_account') }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);
                    if (data.success) {
                        window.location = link;
                    } else {
                        Swal.fire({
                            title: "Error!",
                            html: data.message,
                            icon: "error"
                        });
                    }
                }
            })
        })
    </script>
@endif
