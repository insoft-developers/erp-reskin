@if ($view == 'product-purchase')
    <script>
        var n = 1;



        $(document).ready(function() {

            $(".rb-input").click(function() {


                var nilai = $('input[name="type"]:checked').val();
                if (nilai == 1) {

                    var html = '';
                    html += '<div class="form-group">';
                    html +=
                        '<input onkeyup="onchange_unit_price(1)" type="text" class="form-control cust-control" id="unit_price_text_1" placeholder="Harga Satuan">';
                    html += '<input type="hidden" id="unit_price_1" name="unit_price[]">';
                    html += '</div>';

                    $("#bagian_unit_1").html(html);

                    var pp = '';
                    pp += '<div class="form-group">';
                    pp +=
                        '<input readonly type="text" class="form-control cust-control" id="purchase_amount_text_1" onkeyup="onchange_purchase_amount(1)" placeholder="Harga Total Pembelian">';
                    pp +=
                        '<input class="purchase-amount" type="hidden" id="purchase_amount_1" name="purchase_amount[]">';
                    pp += '</div>';

                    $("#bagian_total_1").html(pp);

                    var lk = '';
                    lk += '<div class="col-md-4"><strong>Nama Produk</strong></div>';
                    lk += '<div class="col-md-2"><strong>Harga Satuan</strong></div>';
                    lk += '<div class="col-md-2"><strong>Quantity Pembelian</strong></div>';
                    lk += '<div class="col-md-3"><strong>Harga Total Pembelian</strong></div>';

                    $("#label_komposisi").html(lk);

                } else if (nilai == 2) {
                    var html = '';
                    html += '<div class="form-group">';
                    html +=
                        '<input readonly onkeyup="onchange_unit_price(1)" type="text" class="form-control cust-control" id="unit_price_text_1" placeholder="Harga Satuan">';
                    html += '<input type="hidden" id="unit_price_1" name="unit_price[]">';
                    html += '</div>';

                    $("#bagian_total_1").html(html);

                    var pp = '';
                    pp += '<div class="form-group">';
                    pp +=
                        '<input type="text" class="form-control cust-control" id="purchase_amount_text_1" onkeyup="onchange_purchase_amount(1)" placeholder="Harga Total Pembelian">';
                    pp +=
                        '<input class="purchase-amount" type="hidden" id="purchase_amount_1" name="purchase_amount[]">';
                    pp += '</div>';

                    $("#bagian_unit_1").html(pp);

                    var lk = '';
                    lk += '<div class="col-md-4"><strong>Nama Produk</strong></div>';
                    lk += '<div class="col-md-2"><strong>Harga Total Pembelian</strong></div>';
                    lk += '<div class="col-md-2"><strong>Quantity Pembelian</strong></div>';
                    lk += '<div class="col-md-3"><strong>Harga Satuan</strong></div>';

                    $("#label_komposisi").html(lk);
                }
            })
            count_product_item();
            
        });


        $(document).ready(function() {
            $(".select-item").select2();
             $.fn.modal.Constructor.prototype._enforceFocus = function () {};

            $("#payment_type").change(function() {
                var nilai = $(this).val();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: "{{ route('product.purchase.type') }}",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        "payment_type": nilai,
                        "_token": csrf_token
                    },
                    success: function(data) {
                        var HTML = '';
                        HTML += '<option value="">Pilih</option>';
                        for (var i = 0; i < data.data.length; i++) {
                            HTML += '<option value="' + data.data[i].id + '_' + data.data[i]
                                .account_code_id + '">' + data.data[i].name + '</option>';
                        }

                        $("#account_id").html(HTML);
                        console.log(HTML);


                    }
                });
            });
        });


        function tambah_item() {
            $.ajax({
                url: "{{ url('tambah_item') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    n++;
                    var html = '';
                    html += '<div class="row mtop10 bariss tambahan" id="bariss_' + n + '">';
                    html += '<div class="col-md-4">';
                    html += '<div class="form-group">';
                    html += '<select class="form-control cust-control select-item" id="product_id_' + n +
                        '" name="product_id[]">';
                    html += '<option value="">Pilih Produk</option>';
                    for (var i = 0; i < data.data.length; i++) {
                        html += '<option value="' + data.data[i].id + '">' + data.data[i].name + '-' + data
                            .data[i].unit + '</option>';
                    }
                    html += '</select>';
                    html += '</div>';
                    html += '</div>';

                    var pilihan = $('input[name="type"]:checked').val();
                    if (pilihan == 1) {
                        html += '<div class="col-md-2">';
                        html += '<div class="form-group">';
                        html += '<input onkeyup="onchange_unit_price(' + n +
                            ')" type="text" class = "form-control cust-control" id = "unit_price_text_' + n +
                            '" placeholder = "Harga Satuan"> ';
                        html += '<input type="hidden" id="unit_price_' + n + '" name="unit_price[]">';
                        html += '</div>';
                        html += '</div>';
                    } else {
                        html += '<div class="col-md-2">';
                        html += '<div class="form-group">';
                        html +=
                            '<input type="text" class="form-control cust-control" id="purchase_amount_text_' +
                            n + '" onkeyup="onchange_purchase_amount(' + n +
                            ')"placeholder = "Harga total pembelian" > ';
                        html += '<input class="purchase-amount" type="hidden" id="purchase_amount_' + n +
                            '" name="purchase_amount[]">';
                        html += '</div>';
                        html += '</div>';

                    }



                    html += '<div class="col-md-2">';
                    html += '<div class="form-group">';
                    html += '<input onkeyup="onchange_quantity(' + n +
                        ')" type="text" class = "form-control cust-control" id = "quantity_text_' + n +
                        '" placeholder = "Quantity Pembelian" > ';
                    html += '<input type="hidden" id="quantity_' + n + '" name="quantity[]">';
                    html += '</div>';
                    html += '</div>';

                    if (pilihan == 1) {
                        html += '<div class="col-md-3">';
                        html += '<div class="form-group">';
                        html +=
                            '<input readonly type="text" class="form-control cust-control" id="purchase_amount_text_' +
                            n + '" onkeyup="onchange_purchase_amount(' + n +
                            ')"placeholder = "Harga total pembelian" > ';
                        html += '<input class="purchase-amount" type="hidden" id="purchase_amount_' + n +
                            '" name="purchase_amount[]">';
                        html += '</div>';
                        html += '</div>';
                    } else {
                        html += '<div class="col-md-3">';
                        html += '<div class="form-group">';
                        html += '<input readonly onkeyup="onchange_unit_price(' + n +
                            ')" type="text" class = "form-control cust-control" id = "unit_price_text_' + n +
                            '" placeholder = "Harga Satuan"> ';
                        html += '<input type="hidden" id="unit_price_' + n + '" name="unit_price[]">';
                        html += '</div>';
                        html += '</div>';
                    }





                    html += '<div class="col-md-1 button-product-action">';
                    html +=
                        '<center><a title="Tambah Produk" href="javascript:void(0);" onclick="tambah_item()" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown"data-bs-auto-close="outside"><i class="fa fa-plus"></i></a></center>';
                    html +=
                        '<center><a style="margin-left:5px;" title="Hapus Produk" href="javascript:void(0);" onclick ="hapus_item(' +
                        n +
                        ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle = "dropdown" data-bs-auto-close = "outside"> <i class="fa fa-trash"></i></a></center>';
                    html += '</div>';

                    html += '</div>';
                    $(".bariss").last().after(html);
                    $(".select-item").select2({
                        dropdownParent: $("#modal-tambah .modal-content")
                    });
                    $.fn.modal.Constructor.prototype._enforceFocus = function () {};
                    count_product_item();

                    $(".rb-input").attr("disabled", true);
                }
            })
        }

        function count_total_transaction() {
            var total_transaksi = 0;
            $(".purchase-amount").each(function(index) {
                total_transaksi = +total_transaksi + +$(this).val();
            });

            var discount = $("#discount").val();
            var tax = $("#tax").val();
            var oe = $("#other_expense").val();
            var final_amount = total_transaksi + +tax - discount + +oe;

            $("#total_transaksi").val(final_amount);
            $("#total_transaksi_text").val(formatAngka(final_amount));
        }

        function count_product_item() {
            var count = $(".select-item").length;
            $("#product_count").val(count);
        }

        function hapus_item(id) {
            $("#bariss_" + id).remove();
            count_product_item();
            count_total_transaction();

            var panjang = $(".purchase-amount").length;
            if (panjang == 1) {
                $(".rb-input").removeAttr("disabled");
            }
        }

        function onchange_purchase_amount(id) {
            pemisah_ribuan("#purchase_amount_text_" + id, "#purchase_amount_" + id);
            var nilai = $('input[name="type"]:checked').val();
            if (nilai == 1) {

            } else {
                count_harga_satuan(id);
                count_total_transaction();
            }

        }


        function onchange_unit_price(id) {
            pemisah_ribuan("#unit_price_text_" + id, "#unit_price_" + id);

            var nilai = $('input[name="type"]:checked').val();
            if (nilai == 1) {
                count_harga_satuan(id);
                count_total_transaction();
            } else {

            }

        }

        function onchange_quantity(id) {
            pemisah_ribuan("#quantity_text_" + id, "#quantity_" + id);
            count_harga_satuan(id);
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

        function count_harga_satuan(id) {
            var pilihan = $('input[name="type"]:checked').val();
            if (pilihan == 1) {
                var nilai = $("#unit_price_" + id).val();
                var qty = $("#quantity_" + id).val();
                var harga = nilai * qty;

                $("#purchase_amount_" + id).val(harga);


                display_unit_price(id);
            } else {
                var nilai = $("#purchase_amount_" + id).val();
                var qty = $("#quantity_" + id).val();
                var harga = nilai / qty;
                var rounded_price = Math.round(harga);
                if (qty > 0) {
                    $("#unit_price_" + id).val(rounded_price);
                } else {
                    $("#unit_price_" + id).val(0);
                }

                display_unit_price(id);
            }



        }

        function display_unit_price(id) {
            var nilai = $('input[name="type"]:checked').val();

            if (nilai == 1) {
                var unitprice = $("#purchase_amount_" + id).val();
                $("#purchase_amount_text_" + id).val(formatAngka(unitprice));
            } else {
                var unitprice = $("#unit_price_" + id).val();
                $("#unit_price_text_" + id).val(formatAngka(unitprice));
            }


        }

        $(document).ready(function() {
            

            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();
                var id = $("#id").val();
                if (save_method == 'add') url = "{{ url('product_purchase') }}";
                else url = "{{ url('product_purchase') }}" + "/" + id;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: new FormData($('#modal-tambah form')[0]),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.success) {
                            $(".rb-input").removeAttr("disabled");
                            $("#modal-tambah").modal("hide");
                            table.ajax.reload(null, false);
                            $("#loading-image").hide();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Notice!...",
                                html: data.message,
                                footer: ''
                            });
                            $("#loading-image").hide();
                        }
                    }
                })

            });


            $("#form-upload-product-purchase").submit(function(e) {
                e.preventDefault();
                $("#loading-image-upload").show();
                var csrf_token = $('meta[name="csrf-token"]').attr('content');
                const data = new FormData();

                data.append('_method', 'POST');
                data.append('file', $('#file')[0].files[0]);
                data.append('_token', csrf_token);



                $.ajax({
                    url: "{{ url('product_purchase_upload') }}",
                    type: "POST",
                    contentType: false,
                    processData: false,
                    data: data,
                    success: function(data) {
                        if (data.success) {
                            $("#loading-image-upload").hide();
                            Swal.fire({
                                icon: "success",
                                title: "Success...",
                                html: data.message,
                                footer: ''
                            });
                            table.ajax.reload(null, false);
                            $("#modal-upload").modal("hide");
                        } else {
                            $("#loading-image-upload").hide();
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



        });


        function add_data() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Transaksi Beli Produk");
            resetData();
            $(".select-item").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });
             $.fn.modal.Constructor.prototype._enforceFocus = function () {};

            $("#supplier_id").select2({
                dropdownParent: $("#modal-tambah .modal-content")
            });
        }



        var table = $('#table-purchase').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Blfrtip',
            columnDefs: [{
                target: 1,
                visible: false,
                searchable: false
            }, ],

            ajax: "{{ route('product.purchase.table') }}",
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
                    data: 'reference',
                    name: 'reference'
                },
                {
                    data: 'supplier_id',
                    name: 'supplier_id'
                },
                {
                    data: 'image',
                    name: 'image'
                },
                {
                    data: 'product',
                    name: 'product'
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

        function editData(id) {
            save_method = "edit";
            $("input[name='_method']").val("PATCH");
            $(".modal-title").text("Edit Transaksi Beli Produk")

            $.ajax({
                url: "{{ url('product_purchase') }}" + "/" + id + "/edit",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    console.log(data);
                    $("#id").val(data.purchase.id);
                    $("#transaction_date").val(data.purchase.transaction_date);
                    $("#account_id").val(data.purchase.account_id);
                    $("#product_count").val(data.purchase.product_count);
                    $("#tax").val(data.purchase.tax);
                    $("#tax_text").val(formatAngka(data.purchase.tax));
                    $("#discount").val(data.purchase.discount);
                    $("#discount_text").val(formatAngka(data.purchase.discount));
                    $("#other_expense").val(data.purchase.other_expense);
                    $("#other_expense_text").val(formatAngka(data.purchase.other_expense));
                    $("#total_transaksi").val(data.purchase.total_purchase);
                    $("#total_transaksi_text").val(formatAngka(data.purchase.total_purchase));

                    $("#product_id_1").val(data.detail[0].product_id).trigger('change');
                    $("#purchase_amount_1").val(data.detail[0].purchase_amount);
                    $("#purchase_amount_text_1").val(formatAngka(data.detail[0].purchase_amount));
                    $("#quantity_1").val(data.detail[0].quantity);
                    $("#quantity_text_1").val(formatAngka(data.detail[0].quantity));
                    $("#unit_price_1").val(data.detail[0].unit_price);
                    $("#unit_price_text_1").val(formatAngka(data.detail[0].unit_price));

                    $(".tambahan").remove();
                    $(".bariss").last().after(data.html);
                    $(".select-item").select2({
                        dropdownParent: $("#modal-tambah .modal-content")
                    });
                    $("#modal-tambah").appendTo("body").modal("show");
                    n = data.detail.length;
                     $.fn.modal.Constructor.prototype._enforceFocus = function () {};
                }
            })
        }

        function resetData() {
            $("#id").val("");
            var tanggal = "{{ date('Y-m-d') }}";
            $("#transaction_date").val(tanggal);
            $("#account_id").val("");
            $("#product_count").val(1);
            $("#tax_text").val("");
            $("#tax").val("");
            $("#discount_text").val("");
            $("#discount").val("");
            $("#other_expense_text").val("");
            $("#other_expense").val("");
            $("#total_transaksi_text").val("");
            $("#total_transaksi").val("");
            $(".tambahan").remove();
            $("#product_id_1").val("").trigger('change');
            $("#purchase_amount_text_1").val("");
            $("#purchase_amount_1").val("");
            $("#quantity_text_1").val("");
            $("#quantity_1").val("");
            $("#unit_price_text_1").val("");
            $("#unit_price_1").val("");
            $("#payment_type").val("0");
            $("#supplier_id").val("").trigger('change');
            $("#reference").val("");
            $("#image").val(null);
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
                url: "{{ url('product_purchase') }}" + "/" + id,
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
                            icon: "error",
                            title: "Notice!...",
                            html: data.message,
                            footer: ''
                        });
                    }
                }
            })
        }

        function reloadTable() {
            var table = $("#table-purchase").DataTable();
            table.ajax.reload(null, false);
        }

        function update_category_data() {
            $.ajax({
                url: "{{ url('material_category_update') }}",
                type: "GET",
                success: function(data) {
                    $("#category_id").html(data);
                }
            })
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
                url: "{{ route('product.purchase.sync') }}",
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

        function upload_product_purchase() {
            $("#modal-upload").appendTo("body").modal("show");
        }


        function download_template_upload() {
            window.location = "{{ url('download_template_pembelian') }}";
        }
    </script>
@endif
