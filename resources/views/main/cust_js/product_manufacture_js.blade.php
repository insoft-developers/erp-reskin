@if ($view == 'product-manufacture')
    <script>
        var n = 1;


        $(document).ready(function() {
            $("#product_id").change(function() {
                var id = $(this).val();
                if (id == '') {
                    $("#product_count").attr("readonly", true);
                } else {
                    $("#product_count").removeAttr("readonly");
                }

                $("#product-container").html("");
            })
            $("#product_count").blur(function() {
                var product_count = $("#product_count").val();
                if (product_count == '' || product_count == '') {

                } else {
                    var id = $("#product_id").val();

                    var csrf_token = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        url: "{{ url('change_manufacture_select') }}",
                        type: "POST",
                        data: {
                            "id": id,
                            "product_count": product_count,
                            "_token": csrf_token
                        },
                        success: function(data) {
                             if(data.success) {
                                 $("#product-container").html(data.data);
                                 count_total_transaction();
                            } else {
                                 Swal.fire({
                                    icon: "warning",
                                    title: "Peringatan",
                                    html: data.message,
                                    footer: ''
                                });
                            }
                        }
                    });
                }

            });
        });

        function count_total_transaction() {
            var total_transaksi = 0;
            $(".total-harga").each(function(index) {
                total_transaksi = +total_transaksi + +$(this).val();
            });

            var discount = $("#discount").val();
            var tax = $("#tax").val();
            var oe = $("#other_expense").val();
            var final_amount = total_transaksi + +tax - discount + +oe;

            $("#total_transaksi").val(final_amount);
            $("#total_transaksi_text").val(formatAngka(final_amount));
        }



        function hapus_item(id) {
            $("#bariss_" + id).remove();
            count_product_item();
            count_total_transaction();
        }



        function onchange_tax() {
            pemisah_ribuan("#tax_text", "#tax");
            count_total_transaction();
        }

        function onchange_discount() {
            pemisah_ribuan("#discount_text", "#discount");
            count_total_transaction();
        }

        function onchange_other_expense() {
            pemisah_ribuan("#other_expense_text", "#other_expense");
            count_total_transaction();
        }



        $(document).ready(function() {
            $("#product_id").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });

            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                if (save_method == 'add') url = "{{ url('product_manufacture') }}";
                else url = "{{ url('product_manufacture') }}" + "/" + id;
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "JSON",
                    data: $(this).serialize(),
                    success: function(data) {
                        if (data.success) {
                            $("#modal-tambah").modal("hide");
                            table.ajax.reload(null, false);
                            $("#loading-image").hide();
                        } else {
                            Swal.fire({
                                icon: "warning",
                                title: "Peringatan",
                                html: data.message,
                                footer: ''
                            });
                            $("#loading-image").hide();
                        }
                    }
                })

            });
        });


        function add_data() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Transaksi Buat Produk");
            resetData();
        }



        var table = $('#table-inter-purchase').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('product.manufacture.table') }}",
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
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'quantity',
                    name: 'quantity'
                },
                {
                    data: 'total_price',
                    name: 'total_price'
                },
                {
                    data: 'tax_other',
                    name: 'tax_other'
                },
                {
                    data: 'final_price',
                    name: 'final_price'
                },



            ]
        });


        function resetData() {
            $("#id").val("");
            $("#product_id").val("").trigger('change');
            var tanggal = "{{ date('Y-m-d') }}";
            $("#transaction_date").val(tanggal);
            $("#account_id").val("");
            $("#product_count").val("");
            $("#product_count").attr("readonly", true);
            $("#tax_text").val("");
            $("#tax").val("");
            $("#discount_text").val("");
            $("#discount").val("");
            $("#other_expense_text").val("");
            $("#other_expense").val("");
            $("#total_transaksi_text").val("");
            $("#total_transaksi").val("");
            $("#product-container").html("");
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
                url: "{{ url('product_manufacture') }}" + "/" + id,
                type: "POST",
                dataType: "JSON",
                data: {
                    "_token": csrf_token,
                    "_method": "DELETE",
                    "id": id
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                    } else {
                        Swal.fire({
                            icon: "warning",
                            title: "Peringatan",
                            html: data.message,
                            footer: ''
                        });
                    }
                }
            })
        }

        function reloadTable() {
            var table = $("#table-inter-purchase").DataTable();
            table.ajax.reload(null, false);
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
                url: "{{ route('product.manufacture.sync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.success) {
                        reloadTable();
                    }
                }
            });
        }
    </script>
@endif
