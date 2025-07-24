@if ($view == 'opname')
    <script>
        var table = $('#table-purchase').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('adjustment.opname.table') }}",
            order: [
                [1, "desc"]
            ],
            columns: [{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'sync_status',
                    name: 'sync_status'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },

                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'total_value',
                    name: 'total_value'
                },
                {
                    data: 'physical_quantity',
                    name: 'physical_quantity'
                },
                {
                    data: 'physical_total_value',
                    name: 'physical_total_value'
                },
                {
                    data: 'selisih_quantity',
                    name: 'selisih_quantity'
                },
                {
                    data: 'selisih_total_value',
                    name: 'selisih_total_value'
                },
                {
                    data: 'total_adjust_quantity',
                    name: 'total_adjust_quantity'
                },
                {
                    data: 'total_adjust_value',
                    name: 'total_adjust_value'
                },

            ]
        });


        function add_data() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Stock Opname");
            resetData();
        }


        $(document).ready(function() {

            $("#form-upload-opname").submit(function(e) {
                e.preventDefault();
                $("#loading-pro").show();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                var id = $("#soid").val();
                const data = new FormData();

                data.append('_method', 'POST');
                data.append('id', id);
                data.append('file', $('#file')[0].files[0]);

                data.append('_token', csrf_token);



                $.ajax({
                    url: "{{ route('adjustment.opname.upload') }}",
                    type: "POST",
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function(data) {
                        if (data.success) {
                            $("#loading-pro").hide();
                            Swal.fire({
                                icon: "success",
                                title: "Success...",
                                html: data.message,
                                footer: ''
                            });
                            table.ajax.reload(null, false);
                            $("#modal-upload").modal("hide");
                        } else {
                            $("#loading-pro").hide();
                            Swal.fire({
                                icon: "warning",
                                title: "Failed...",
                                html: data.message,
                                footer: ''
                            });
                            table.ajax.reload(null, false);
                        }
                    }
                })
            });



            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image-opname").show();
                $.ajax({
                    url: "{{ route('adjustment.opname.store') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $("#modal-tambah").modal("hide");
                            table.ajax.reload(null, false);
                            $("#loading-image-opname").hide();

                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Notice!...",
                                html: data.message,
                                footer: ''
                            });
                            $("#loading-image-opname").hide();
                        }
                    }
                })

            });
        });

        function listData(id) {
            $.ajax({
                url: "{{ url('adjustment/opname_product_detail') }}" + "/" + id,
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    var html = '';
                    html += '<div class="table-responsive">';
                    html += '<table id="table-purchase" class="table table-bordered table-hover nowrap">';

                    html += '<tr>';
                    html += '<th>No</th>';
                    html += '<th>Nama Produk</th>';
                    html += '<th>Stok</th>';
                    html += '<th>HPP</th>';
                    html += '<th>Nilai Stok</th>';
                    html += '<th>Stok Fisik</th>';
                    html += '<th>Nilai Stok Fisik</th>';
                    html += '<th>selisih Stok</th>';
                    html += '<th>Stok Penyesuaian</th>';
                    html += '<th>Type Penyesuaian</th>';
                    html += '<th>Stok Stlh Penyesuaian</th>';
                    html += '<th>Nilai Stok Stlh Penyesuaian</th>';
                    html += '</tr>';



                    var nomor = 0;
                    for (var i = 0; i < data.data.length; i++) {
                        nomor++;

                        if (data.data[i].product_type == 1) {
                            if (i == 0 || data.data[i - 1].product_type != 1) {
                                html +=
                                    '<tr><td style="background-color:green;color:white;" colspan="12">PRODUK BARANG JADI</td></tr>';
                            }
                        } else if (data.data[i].product_type == 2) {
                            if (i == 0 || data.data[i - 1].product_type != 2) {
                                html +=
                                    '<tr><td style="background-color:green;color:white;" colspan="12">PRODUK MANUFAKTUR</td></tr>';
                            }
                        } else if (data.data[i].product_type == 3) {
                            if (i == 0 || data.data[i - 1].product_type != 3) {
                                html +=
                                    '<tr><td style="background-color:green;color:white;" colspan="12">PRODUK BAHAN BAKU</td></tr>';
                            }
                        } else if (data.data[i].product_type == 4) {
                            if (i == 0 || data.data[i - 1].product_type != 4) {
                                html +=
                                    '<tr><td style="background-color:green;color:white;" colspan="12">PRODUK SETENGAH JADI</td></tr>';
                            }
                        }


                        html += '<tr>';
                        html += '<td>' + nomor + '</td>';
                        html += '<td>' + data.data[i].product_name + '</td>';
                        html += '<td style="background:whitesmoke;">' + formatAngka(data.data[i].quantity) +
                            '</td>';
                        html += '<td>' + formatAngka(data.data[i].cost) + '</td>';
                        html += '<td>' + formatAngka(data.data[i].total_value) + '</td>';
                        html += '<td style="background:whitesmoke;color:green;font-weight:bold;">' +
                            formatAngka(data.data[i].physical_quantity) + '</td>';
                        html += '<td>' + formatAngka(data.data[i].physical_total_value) + '</td>';

                        if (data.data[i].selisih != 0) {
                            html += '<td style="background:whitesmoke;color:red;font-weight:bold;">' +
                                formatAngka(data.data[i].selisih) + '</td>';
                        } else {
                            html += '<td style="background:whitesmoke;">' + formatAngka(data.data[i].selisih) +
                                '</td>';
                        }

                        html += '<td>' + formatAngka(data.data[i].adjust_quantity) + '</td>';
                        html += '<td>' + data.data[i].adjust_mode + '</td>';
                        html += '<td>' + formatAngka(data.data[i].quantity_after_adjust) + '</td>';
                        html += '<td>' + formatAngka(data.data[i].total_value_after_adjust) + '</td>';
                        html += '</tr>';
                    }
                    html += '</table>';
                    html += '</div>';
                    $("#content-opname-detail").html(html);

                    $("#modal-detail").appendTo("body").modal("show");
                    $(".modal-title").text("Daftar Produk Stock Opname");
                    $("#sid-detail").val(id);
                    if (data.master.is_download == 1) {
                        $("#btn-sesuaikan-stock").hide();
                    } else {
                        $("#btn-sesuaikan-stock").show();
                    }
                }
            });
        }


        function uploadData(id) {
            $("#modal-upload").appendTo("body").modal("show");
            $(".modal-title").text("Upload Stock Opname");
            $("#file").val(null);
            $("#soid").val(id);
        }

        function resetData() {
            $("#description").val("");
        }

        function reloadTable() {
            table.ajax.reload(null, false);
        }



        function sesuaikan_stock() {
            var id = $("#sid-detail").val();
            Swal.fire({
                title: "Sesuikan Data Stok?",
                text: "Apakah anda yakin ingin menyesuaikan Data Stok Dengan Fisik?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Sesuaikan!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_sesuaikan_stock(id);
                }
            });

        }

        function confirm_sesuaikan_stock(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('adjustment.sesuaikan.opname') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        // $("#loading-pro").hide();
                        Swal.fire({
                            icon: "success",
                            title: "Success...",
                            html: data.message,
                            footer: ''
                        });
                        table.ajax.reload(null, false);
                        $("#modal-detail").modal("hide");
                    } else {
                        // $("#loading-pro").hide();
                        Swal.fire({
                            icon: "warning",
                            title: "Failed...",
                            html: data.message,
                            footer: ''
                        });
                        table.ajax.reload(null, false);
                    }
                }
            });

        }


        function download_template_opname() {
            var id = $("#soid").val();
            window.location = "{{ url('adjustment/download_template_opname') }}" + "/" + id;
        }



        function unsync(id) {
            Swal.fire({
                title: 'Ubah Sync Menjadi UnSync?',
                text: 'Unsync Akan Menghapus Singkronisasi Jurnal. ',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Singkronisasi!',
                cancelButtonText: 'Tidak, Batalkan!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    unsync_process(id);
                }
            });
        }


        function unsync_process(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('adjustment.opname.unsync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses',
                            text: data.message
                        })
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        })
                    }


                }
            });
        }


        function syncData(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will syncronize this transaction into journal account ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Synchronize it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    sync(id);
                }
            });
        }


        function sync(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('adjustment.opname.sync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                    } else {
                        Swal.fire({
                            icon: 'danger',
                            title: 'Failed',
                            text: data.message
                        })
                    }

                }
            });
        }


        function deleteData(id) {
            Swal.fire({
                title: "Delete Data?",
                text: "Are you sure, do you want to delete this transaction..?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    confirm_delete(id);

                }
            });
        }

        function confirm_delete(id) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{ route('adjustment.opname.delete') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "_token": csrf_token,
                    "id": id
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Notice!...",
                            html: data.message,
                            footer: ''
                        });
                    }
                }
            })
        }
    </script>
@endif
