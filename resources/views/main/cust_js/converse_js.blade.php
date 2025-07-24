@if ($view == 'konversi')
    <script>
        let n = 1;
        var products = [];
        <?php foreach ($products as $product) : ?>
        products.push(JSON.parse('<?php echo $product; ?>'));
        <?php endforeach; ?>

        var inters = [];
        <?php foreach ($inters as $inter) : ?>
        inters.push(JSON.parse('<?php echo $inter; ?>'));
        <?php endforeach; ?>



        function tambah_item() {
            n++;
            var html = '';
            html += '<div style="margin-top:20px;" class="row bariss tambahan" id="bariss_' + n + '">';
            html += '<div class="col-md-4">';
            html += '<div class="form-group">';

            html += '<select class="form-control cust-control select-item" data-id="' + n + '" id="item_' + n +
                '"name="item[]">';
            html += '<option value="">Pilih Produk</option>';
            html += '<optgroup label="Produk Jadi">';
            for (var i = 0; i < products.length; i++) {
                html += '<option value="' + products[i].id + '_1">' + products[i].name + '</option>';
            }
            html += '</optgroup>';

            html += '<optgroup label="Produk Setengah Jadi">';
            for (var i = 0; i < inters.length; i++) {
                html += '<option value="' + inters[i].id + '_2">' + inters[i].product_name + '</option>';
            }
            html += '</optgroup>';


            html += '</select>';
            html += '</div>';
            html += '</div>';

            html += '<div class="col-md-2">';
            html += '<div class="form-group">';

            html += '<input onkeyup="onchange_jumlah(' + n +
                ')" type="text" class="form-control cust-control" id="jumlah_text_' + n + '" placeholder="Quantity">';
            html += '<input class="jumlah" type="hidden" id="jumlah_' + n + '" name="jumlah[]">';
            html += '</div>';
            html += '</div>';


            html += '<div class="col-md-2">';
            html += '<div class="form-group">';

            html +=
                '<input readonly type="text" class="form-control cust-control" id="item_price_text_' + n +
                '" placeholder="Harga">';
            html += '<input class="purchase-amount" type="hidden" id="item_price_' + n + '" name="item_price[]">';
            html += '</div>';
            html += '</div>';


            html += '<div class="col-md-3">';
            html += '<div class="form-group">';

            html +=
                '<input readonly type="text" class="form-control cust-control" id="item_total_text_' + n +
                '"  placeholder="Total Harga">';
            html += '<input class="total-amount" type="hidden" id="item_total_' + n + '" name="item_total[]">';
            html += '</div>';
            html += '</div>';


            html += '<div class="col-md-1 button-product-action">';
            html +=
                '<center><a title="Tambah Produk" href="javascript:void(0);" onclick="tambah_item()" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-plus"></i></a></center>';
            html +=
                '<center><a style="margin-left:5px;" title="Hapus Produk" href="javascript:void(0);" onclick ="hapus_item(' +
                n +
                ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle = "dropdown" data-bs-auto-close = "outside"> <i class="fa fa-trash"></i></a></center>';
            html += '</div>';

            html += '</div>';
            $(".bariss").last().after(html);
            $(".select-item").select2();
        }



        function count_total_conversion() {
            var product_quantity = $("#product_quantity").val();
            var total_conversion = 0;
            $(".total-amount").each(function(index) {
                total_conversion = +total_conversion + +$(this).val();
            });

            var total_quantity = 0;
            $(".jumlah").each(function(index) {
                total_quantity = +total_quantity + +$(this).val();
            });


            var total_product_jadi = 0;
            $(".total-jadi").each(function(index) {
                total_product_jadi = +total_product_jadi + +$(this).val();
            });

            var total_setengah_jadi = 0;
            $(".total-setengah").each(function(index) {
                total_setengah_jadi = +total_setengah_jadi + +$(this).val();
            });



            $("#total_product_text").val(formatAngka(total_conversion));
            $("#total_product").val(total_conversion);
            $("#total_product_jadi").val(total_product_jadi);
            $("#total_product_setengah_jadi").val(total_setengah_jadi);

            var total_material = $("#total_material").val();
            var total_sisa = total_material - total_conversion;
            var total_sisa2 = product_quantity - total_quantity;
            if (total_sisa >= 0) {
                $("#total_sisa_text").val(formatAngka(total_sisa));
                $("#total_sisa2_text").val(formatAngka(total_sisa2));
            } else {
                $("#total_sisa_text").val('-' + formatAngka(total_sisa));
                $("#total_sisa2_text").val('-' + formatAngka(total_sisa2));
            }

            $("#total_sisa").val(total_sisa);
            $("#total_sisa2").val(total_sisa2);

            console.log(total_conversion);
        }


        function onchange_jumlah(id) {
            pemisah_ribuan("#jumlah_text_" + id, "#jumlah_" + id);
            onchange_item(id);
        }


        function onchange_item(id) {
            var price = $("#product_price").val();
            $("#item_price_text_" + id).val(formatAngka(price));
            $("#item_price_" + id).val(price);

            var jumlah = $("#jumlah_" + id).val();
            var total = jumlah * price;
            $("#item_total_" + id).val(total);
            $("#item_total_text_" + id).val(formatAngka(total));
            count_total_conversion();

        }


        function hapus_item(id) {
            $("#bariss_" + id).remove();
            count_total_conversion();
        }

        function qty_onchange() {
            pemisah_ribuan("#product_quantity_text", "#product_quantity");
            calculate_total();
        }

        function calculate_total() {

            var qty = $("#product_quantity").val();
            var harga = $("#product_price").val();
            var total_harga = qty * harga;

            $("#total_price_text").val(formatAngka(total_harga));
            $("#total_price").val(total_harga);
            $("#total_material_text").val(formatAngka(total_harga));
            $("#total_material").val(total_harga);
        }

        function reset_material() {
            $("#unit").val("");
            $("#stock_text").val("");
            $("#stock").val("");
            $("#product_quantity_text").val("");
            $("#product_quantity").val("");
            $("#product_price_text").val("");
            $("#product_price").val("");
            $("#total_price_text").val("");
            $("#total_price").val("");
        }

        $(document).ready(function() {
            $("#product_id").select2();
            $(".select-item").select2();
            $("#cost_account").select2();

            $.fn.modal.Constructor.prototype._enforceFocus = function () {};

            $("#product_id").change(function() {
                var selected = $(this).val();

                var csrf_token = $('meta[name="csrf-token"]').attr('content');

                if (selected == '') {
                    $("#product_quantity_text").attr("readonly", true);
                    reset_material();
                    $("#product-container").slideUp();
                } else {
                    $.ajax({
                        url: "{{ url('conversion_selected_item') }}",
                        type: "POST",
                        dataType: "JSON",
                        data: {
                            "selected": selected,
                            "_token": csrf_token
                        },
                        success: function(data) {
                            console.log(data);
                            $("#unit").val(data.data.unit);
                            $("#stock_text").val(formatAngka(data.data.stock));
                            $("#product_price_text").val(formatAngka(data.data.cost));
                            $("#stock").val(data.data.stock);
                            $("#product_price").val(data.data.cost);

                            $("#product_quantity_text").val("");
                            $("#product_quantity").val("");
                            calculate_total();

                            $("#product_quantity_text").removeAttr("readonly");
                            $(".tambahan").remove();
                            $("#item_1").val("").trigger('change');
                            $("#jumlah_text_1").val("");
                            $("#jumlah_1").val("");
                            $("#item_price_text_1").val("");
                            $("#item_price_1").val("");
                            $("#item_total_text_1").val("");
                            $("#item_total_1").val("");
                            count_total_conversion();
                        }
                    })
                }

            });


            $("#modal-tambah form").submit(function(e) {
                e.preventDefault();
                $("#loading-image").show();

                $.ajax({
                    url: "{{ url('converse') }}",
                    type: "POST",
                    data: new FormData($('#modal-tambah form')[0]),
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.success) {
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




            $("#product_quantity_text").blur(function() {
                var stock = $("#stock").val();
                var qty = $("#product_quantity").val();

                if (parseInt(stock) < parseInt(qty)) {
                    alert("Stok Material / Bahan 1/2 Jadi tidak cukup untuk melakukan transaksi ini....!");
                    $("#product_quantity").val(0);
                    $("#product_quantity_text").val(0);
                    calculate_total();
                } else {
                    if (qty > 0) {
                        $("#product-container").slideDown();
                    } else {
                        $("#product-container").slideUp();
                    }

                    $("#product_quantity_text").attr("readonly", true);
                }

            });

        });


        function add_data() {
            $("#modal-tambah").appendTo("body").modal("show");
            save_method = 'add';
            $('input[name=_method]').val('POST');
            $(".modal-title").text("Tambah Konversi Stock");
            resetData();
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

            ajax: "{{ route('converse.table') }}",
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
                    data: 'transaction_date',
                    name: 'transaction_date'
                },

                {
                    data: 'reference',
                    name: 'reference'
                },
                {
                    data: 'product_type',
                    name: 'product_type'
                },
                {
                    data: 'product_id',
                    name: 'product_id'
                },
                {
                    data: 'product_quantity',
                    name: 'product_quantity'
                },
                {
                    data: 'item',
                    name: 'item'
                },

                {
                    data: 'total_material',
                    name: 'total_material'
                },
                {
                    data: 'total_product',
                    name: 'total_product'
                },
                {
                    data: 'total_sisa',
                    name: 'total_sisa'
                },

            ]
        });



        function resetData() {
            $("#id").val("");
            var tanggal = "{{ date('Y-m-d') }}";
            $("#transaction_date").val(tanggal);
            $("#product_id").val("").trigger('change');
            $("#reference").val("");
            $("#unit").val("");
            $("#stock_text").val("");
            $("#stock").val("");
            $("#product_quantity").val("");
            $("#product_quantity_text").val("");
            $("#product_price").val("");
            $("#product_price_text").val("");
            $("#total_price").val("");
            $("#total_price_text").val("");
            $("#item_1").val("").trigger('change');
            $("#jumlah_text_1").val("");
            $("#jumlah_1").val("");
            $("#item_price_text_1").val("");
            $("#item_price_1").val("");
            $("#item_total_text_1").val("");
            $("#item_total_1").val("");
            $("#total_material_text").val("");
            $("#total_material").val("")
            $("#total_product_text").val("");
            $("#total_product").val("");
            $("#total_product_jadi").val("");
            $("#total_product_setengah_jadi").val("");
            $("#total_sisa_text").val("");
            $("#total_sisa").val("");
            $("#total_sisa2_text").val("");
            $("#total_sisa2").val("");
            $("#cost_account").val("").trigger('change');
            $("#nama_biaya_1").val("");
            $("#jumlah_biaya_text_1").val("");
            $("#jumlah_biaya_1").val("");
            $("#total_biaya_text").val("");
            $("#total_biaya").val("");
            $(".tambahan").remove();
            $(".biaya-tambahan").remove();
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
                url: "{{ url('converse') }}" + "/" + id,
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
                url: "{{ route('converse.sync') }}",
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
                url: "{{ route('converse.unsync') }}",
                type: "POST",
                dataType: "JSON",
                data: {
                    "id": id,
                    "_token": csrf_token
                },
                success: function(data) {
                    if (data.status) {
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


        $(document).ready(function() {
            $("#product-container").on('change', ".select-item", function() {
                var nilai = $(this).val();
                var a = nilai.split("_");
                var account_code = a[1];

                var id = $(this).attr("data-id");
                if (account_code == 1) {
                    $("#item_total_" + id).addClass("total-jadi");
                    $("#item_total_" + id).removeClass("total-setengah");

                    $("#jumlah_" + id).addClass("jumlah-jadi");
                    $("#jumlah_" + id).removeClass("jumlah-setengah");


                } else if (account_code == 2) {
                    $("#item_total_" + id).removeClass("total-jadi");
                    $("#item_total_" + id).addClass("total-setengah");

                    $("#jumlah_" + id).addClass("jumlah-setengah");
                    $("#jumlah_" + id).removeClass("jumlah-jadi");
                }

            })
        });


        let nb = 1;
        function tambah_biaya() {
            nb++;
            var html = '';
            html += '<div style="margin-top:10px;" class="row baris-biaya biaya-tambahan" id="baris_biaya_'+nb+'">';
            html += '<div class="col-md-7">';
            html += '<div class="form-group">';
            html +=
                '<input type="text" class="form-control cust-control" id="nama_biaya_'+nb+'" placeholder="Nama Biaya" name="nama_biaya[]">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-3">';
            html += '<div class="form-group">';
            html +=
                '<input onkeyup="biaya_onchange('+nb+')" type="text" class="form-control cust-control" id="jumlah_biaya_text_'+nb+'" placeholder="Jumlah Biaya">';
            html += '<input class="jumlah-biaya" type="hidden" id="jumlah_biaya_'+nb+'" name="jumlah_biaya[]">';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-md-1 button-product-action">';
            html +=
                '<center><a title="Tambah Biaya" href="javascript:void(0);" onclick="tambah_biaya()" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-plus"></i></a></center>';
                html +=
                '<center><a style="margin-left:5px;" title="Hapus Biaya" href="javascript:void(0);" onclick ="hapus_biaya(' +
                nb +
                ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle = "dropdown" data-bs-auto-close = "outside"> <i class="fa fa-trash"></i></a></center>';

            html += '</div>';
            html += '</div>';
            $(".baris-biaya").last().after(html);
        }


        function hapus_biaya(id) {
            $("#baris_biaya_"+id).remove();
            count_total_biaya();
        }

        function biaya_onchange(id) {
            pemisah_ribuan("#jumlah_biaya_text_"+id, "#jumlah_biaya_"+id);
            count_total_biaya();
        }

        function count_total_biaya() {
            var total_biaya = 0;
            $(".jumlah-biaya").each(function(index) {
                total_biaya = +total_biaya + +$(this).val();
            });

            $("#total_biaya_text").val(formatAngka(total_biaya));
            $("#total_biaya").val(total_biaya);
        }
    </script>
@endif
