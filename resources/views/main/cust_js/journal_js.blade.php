@if ($view == 'journal-list')
    <script>
      

        $("#inbalance_checkbox").click(function() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var cari = $("#cari").val();

            var isChecked = $("#inbalance_checkbox").is(":checked");
            if (isChecked) {

                init_journal_table(bulan, tahun, cari, 'inbalance');
            } else {
                init_journal_table(bulan, tahun, cari, '');
            }
        });

        function tambah_jurnal_reguler() {
            window.location = "{{ url('journal_add') }}";
        }

        function preview_journal(journal_id) {

            $.ajax({
                url: "{{ url('lihat_saldo_awal') }}" + "/" + journal_id,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    if (data.success) {
                        $("#content-lihat-saldo-awal").html(data.data);
                        $("#modal-lihat").modal("show");
                    }
                }
            })
        }


        var bulan = $("#bulan").val();
        var tahun = $("#tahun").val();
        var cari = $("#cari").val();

        init_journal_table(bulan, tahun, cari, '');

        function init_journal_table(bulan, tahun, cari, inbalance) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            var table = new DataTable('#table-jurnal');
            table.destroy();


            var table = $('#table-jurnal').DataTable({

                processing: true,
                serverSide: true,
                dom: 'Blfrtip',
                ajax: {
                    type: "POST",
                    url: "{{ route('journal.table') }}",
                    data: {
                        'bulan': bulan,
                        'tahun': tahun,
                        'cari': cari,
                        'inbalance': inbalance,
                        '_token': csrf_token
                    }
                },
                order: [
                    [0, "desc"]
                ],
                columns: [{
                        data: 'id',
                        name: 'id',
                       
                        searchable: false,
                    },
                    {
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'transaction_name',
                        name: 'transaction_name'
                    },
                    {
                        data: 'total_balance',
                        name: 'total_balance'
                    },
                    {
                        data: 'dibuat',
                        name: 'dibuat'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        }

        $(document).ready(function() {
            $(".s2").select2();
            $("#nominal_text").keyup(function() {
                pemisah_ribuan("#nominal_text", "#nominal");
            });
        })



        $("#bulan").change(function() {
            refresh_table();
        });

        $("#tahun").change(function() {
            refresh_table();
        });

        $("#cari").keyup(function() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var cari = $(this).val();
            init_journal_table(bulan, tahun, cari, '');
        })


        function refresh_table() {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var cari = $("#cari").val();
            init_journal_table(bulan, tahun, cari, '');
        }

        function refresh_after_save(bulan, tahun) {
            init_journal_table(bulan, tahun, "", '');
        }


        function add_jurnal() {
            $("#modal-tambah").modal("show");
            reset_form();
        }

        function reset_form() {
            var sekarang = "{{ date('Y-m-d') }}";
            $("#tanggal_transaksi").val(sekarang);
            $("#jenis_transaksi").val("").trigger('change');
            $("#receive_from").val("").trigger('change');
            $("#save_to").val("").trigger('change');
            $("#keterangan").val("");
            $("#nominal").val("");
            $("#nominal_text").val("");
        }


        $(document).ready(function() {
            $("#jenis_transaksi").change(function() {
                var jenis = $(this).val();
                $.ajax({
                    url: "{{ url('get_account_receive') }}" + "/" + jenis,
                    type: "GET",
                    dataType: "JSON",
                    success: function(data) {

                        var HTML = '';
                        HTML += '<option value="">Pilih diterima dari</option>';
                        for (var i = 0; i < data.group.length; i++) {

                            HTML += '<optgroup style="margin-top:-10px;" label="' + data.group[
                                i] + '">';
                            for (var a = 0; a < data.data.length; a++) {
                                if (data.group[i] == data.data[a].group) {
                                    HTML += '<option style="margin-top:-10px;" value="' + data
                                        .data[a].id + '_' + data.data[a].account_code_id +
                                        '">' + data.data[a].name + '</option>';
                                }
                            }
                            HTML += '</optgroup>';
                        }
                        $("#receive_from").html(HTML);


                        var HTM = '';
                        HTM += '<option value="">Pilih disimpan ke</option>';
                        for (var i = 0; i < data.kelompok.length; i++) {

                            HTM += '<optgroup style="margin-top:-10px;" label="' + data
                                .kelompok[i] + '">';
                            for (var a = 0; a < data.simpan.length; a++) {
                                if (data.kelompok[i] == data.simpan[a].group) {
                                    HTM += '<option style="margin-top:-10px;" value="' + data
                                        .simpan[a].id + '_' + data.simpan[a].account_code_id +
                                        '">' + data.simpan[a].name + '</option>';
                                }
                            }
                            HTM += '</optgroup>';
                        }
                        $("#save_to").html(HTM);

                    }
                })


            })

            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ url('save_jurnal') }}",
                    type: "POST",
                    data: new FormData($('#modal-tambah form')[0]),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        if (data.success) {
                            $("#modal-tambah").modal("hide");
                            refresh_after_save(data.periode.bulan, data.periode.tahun);
                            $("#bulan").val(data.periode.bulan);
                            $("#tahun").val(data.periode.tahun);
                        } else {
                            show_error(data.message);
                        }
                    }
                })
            })
            // $("#receive_from").select2();



        })

        function journal_delete(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this item?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_delete_data(id);

                }
            });
        }

        function confirm_delete_data(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ url('confirm_journal_delete') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    'id': id,
                    '_token': csrf_token
                },
                success: function(data) {

                    if (data.success) {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your file has been deleted.",
                            icon: "success"
                        });
                        refresh_table();
                    } else {
                        Swal.fire({
                            title: "Failed!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                }
            })
        }
    </script>
@endif
