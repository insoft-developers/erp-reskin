@if ($view == 'product-add')
    <script>
        $(document).ready(function() {
            $("#category_id").select2();
            $("#unit").select2();
            $(".select-item").select2();
        });


        let n = 1;
        let i = 0;
        let varian_item_index = 1;

        com_index = 1;

        CKEDITOR.replace("description");



        function add_varian_group() {
            n++;
            varian_item_index++
            var html = '';
            html += '<tr class="baris_' + n + '">';
            html += '<td colspan="5"><input onkeyup="onVarianChange(' + n + ')" id="vg_' + n +
                '" type="text" class="form-control cust-control" placeholder="Varian Group"></td>';
            html +=
                '<td><center><a href="javascript:void(0);"onclick="add_varian_item(' + n +
                ')" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="feather-plus"></i></a></center></td>';
            html += '</tr>';
            html += '<tr class="row_middle_' + n + ' baris_' + n + '">';
            html += '<td width="*"><input id="varian_name_' + varian_item_index +
                '" name="varian_name[]" type="text" class="form-control cust-control" placeholder="Varian Name"></td>';
            html += '<td width="20%"><input id="sku_' + varian_item_index +
                '" name="varian_sku[]" type="text" class="form-control cust-control" placeholder="SKU"></td>';
            html += '<td width="20%"><input onkeyup="varian_price_keyup(' + varian_item_index +
                ')" id="varian_price_text_' + varian_item_index +
                '"  type="text" class="form-control cust-control" placeholder="Harga"><input type="hidden" class="varian_group_' +
                n + '" name="varian_group[]"><input type="hidden" id="varian_price_' + varian_item_index +
                '" name="varian_price[]"></td>';
            html +=
                '<td width="1%"><input onclick="on_change_check(' + varian_item_index + ')" title="Single Pick" id="sp_' +
                varian_item_index + '" class="chk-item" type="checkbox"><label for="sp_1">Single Pick</label><input type="hidden" id="single_pick_' +
                varian_item_index + '" name="single_pick[]" value="0"></td>';
            html +=
                '<td width="15%"><input value="10" type="number" id="max_quantity_' + varian_item_index +
                '" name="max_quantity[]" placeholder="max qty" class="form-control cust-control"></td>';
            html +=
                '<td width="2%"> <center><a href="javascript:void(0);" id="btn_delete_item_' + n +
                '" onclick="delete_varian_item(' + n +
                ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center></td>';
            html += '</tr>';
            html += '<tr class="baris_' + n +
                '"><td colspan="6" style="border-bottom:1px solid orange"><a href="javascript:void(0);" onclick="delete_varian_group(' +
                n +
                ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-remove"></i></td></tr>';

            $("#varian-table").append(html);
            console.log(n + '|' + i);

        }

        function add_varian_item(id) {
            i++;
            varian_item_index++;
            var html = '';
            html += '<tr class="row_middle_' + id + ' baris_' + id + '">';
            html +=
                '<td width="*"><input id="varian_name_' + varian_item_index +
                '" name="varian_name[]" type="text" class="form-control cust-control" placeholder="Varian Name"></td>';
            html += '<td width="20%"><input id="sku_' + varian_item_index +
                '" name="varian_sku[]" type="text" class="form-control cust-control" placeholder="SKU"></td>';
            html += '<td width="20%"><input onkeyup="varian_price_keyup(' + varian_item_index +
                ')" id="varian_price_text_' + varian_item_index +
                '" type="text" class="form-control cust-control" placeholder="Harga"><input type="hidden" class="varian_group_' +
                id + '" name="varian_group[]"><input type="hidden" id="varian_price_' + varian_item_index +
                '" name="varian_price[]"></td>';
            html +=
                '<td width="1%"><input onclick="on_change_check(' + varian_item_index + ')" title="Single Pick" id="sp_' +
                varian_item_index + '" class="chk-item" type="checkbox"><label for="sp_1">Single Pick</label><input type="hidden" id="single_pick_' +
                varian_item_index + '" name="single_pick[]" value="0"></td>';
            html +=
                '<td width="15%"><input value="10" type="number" id="max_quantity_' + varian_item_index +
                '" name="max_quantity[]" placeholder="max qty" class="form-control cust-control"></td>';
            html +=
                '<td width="2%"> <center><a href="javascript:void(0);" id="btn_delete_item_' + i +
                '" onclick="delete_varian_item(' + i +
                ')" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center></td>';
            html += '</tr>';
            var y = i - 1;
            $(".row_middle_" + id).last().after(html);
            var vg = $("#vg_" + id).val();
            $(".varian_group_" + id).val(vg);
        }

        function delete_varian_item(id) {
            $("#btn_delete_item_" + id).closest('tr').remove();
        }

        function delete_varian_group(id) {
            Swal.fire({
                title: "Hapus Varian Group?",
                text: "Apakah anda yakin ingin menghapus Varian Group ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(".baris_" + id).remove();
                }
            });
        }

        $("#is_variant").change(function() {
            var varian = $(this).val();

            if (varian == 2) {
                var html = '';
                html += '<table id="varian-table" class="table table-bordered table-striped mtop20">';
                html += '<thead>';
                html += '<tr>';
                html += '<th colspan="5">';
                html += '<h5 style="text-align: center;">Varian Product List</h5>';
                html += '</th>';

                html +=
                    '<th><center><a href="javascript:void(0);" onclick="add_varian_group()" class="avatar-text avatar-md bg-success text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="feather-plus"></i></a></center></a></th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                html += '<tr class="baris_1">';
                html +=
                    '<td colspan="5"><input type="text" id="vg_1" onkeyup="onVarianChange(1)"  class="form-control cust-control" placeholder="Varian Group"></td>';

                html +=
                    '<td><center><a href="javascript:void(0);" onclick="add_varian_item(1)" class="avatar-text avatar-md bg-info text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="feather-plus"></i></a></center></td>';
                html += '</tr>';
                html += '<tr class="row_middle_1 baris_1">';
                html +=
                    '<td width="*"><input id="varian_name_1" name="varian_name[]" type="text" class="form-control cust-control" placeholder="Varian Name"></td>';
                html +=
                    '<td width="20%"><input id="sku_1" name="varian_sku[]" type="text" class="form-control cust-control" placeholder="SKU"></td>';
                html +=
                    '<td width="20%"><input id="varian_price_text_1" onkeyup="varian_price_keyup(1)" type="text" class="form-control cust-control" placeholder="Harga"><input type="hidden" class="varian_group_1" name="varian_group[]"><input type="hidden" id="varian_price_1" name="varian_price[]"></td>';

                html +=
                    '<td width="1%"><input onclick="on_change_check(1)" title="Single Pick" id="sp_1" class="chk-item" type="checkbox"><label for="sp_1">Single Pick</label><input type="hidden" id="single_pick_1" name="single_pick[]" value="0"></td>';
                html +=
                    '<td width="15%"><input value="10" type="number" id="max_quantity_1" name="max_quantity[]" placeholder="max qty" class="form-control cust-control"></td>';
                html +=
                    '<td width="2%"><center><a href="javascript:void(0);" id="btn_delete_item_1" onclick="delete_varian_item(1)" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center></td>';
                html += '</tr>';
                html += '<tr class="baris_1">';
                html +=
                    '<td colspan="6" style="border-bottom:1px solid orange;"><a href="javascript:void(0);" onclick="delete_varian_group(1)" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-remove"></i></a></td>';
                html += '</tr>';
                html += '</tbody>';
                html += '</table>';
                $("#varian-table-container").html(html);

            } else {
                $("#varian-table-container").html("");
            }



        });

        function on_change_check(id) {
            if ($('#sp_' + id).is(':checked')) {
                $("#single_pick_" + id).val(1)
            } else {
                $("#single_pick_" + id).val(0)
            }

        }

        function onVarianChange(id) {
            var nilai = $("#vg_" + id).val();
            $(".varian_group_" + id).val(nilai);
        }

        $("#is_manufactured").change(function() {
            var nilai = $(this).val();
            if (nilai == 1) {
                $("#composition-container").html("");
                $("#tambah-bahan-text").hide();
                $("#manual_hpp").show();
                $("#createdBy").hide();
                $("#costtext").removeAttr("readonly");
                $('#buffered_stock').html('<option value="0"> No - Jangan Gunakan Stok</option><option value="1">Yes - Gunakan Stok</option>');
            } else {
                $("#manual_hpp").hide();
                $("#createdBy").show();

                bufferedStockChangeCreatedBy();

                $.ajax({
                    url: "{{ url('get_bahan_product') }}",
                    type: "GET",
                    success: function(data) {
                        $("#composition-container").html(data);
                        $("#tambah-bahan-text").show();

                        $(".select-item").select2();
                    }
                });
            }
        });

        function bufferedStockChangeCreatedBy() {
            var created_by = $('input[name="created_by"]:checked').val();
            if (created_by == 1) {
                $('#buffered_stock').html('<option value="0"> No - Jangan Gunakan Stok</option>');
                $('#stock_alert').attr('readonly', true);
                $('#stock_alert').val('');
            } else {
                $('#buffered_stock').html('<option value="1">Yes - Gunakan Stok</option>');
                $('#stock_alert').attr('readonly', false);
                $('#stock_alert').val(0);
            }
        }

        function tambah_bahan() {
            $.ajax({
                url: "{{ url('get_data_non_product') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {
                    com_index++;

                    var html = '';
                    html += '<div class="row baris mtop10 baris-tambahan" id="baris_' + com_index + '">';
                    html += '<div class="col-md-8">';
                    html += '<select class="form-control cust-control select-item" id="composition_' +
                        com_index +
                        '" name="composition[]">';
                    html += '<option value="">Pilih komposisi bahan</option>';
                    html += '<optgroup label="Bahan Baku">';
                    for (var i = 0; i < data.material.length; i++) {
                        html += '<option value="' + data.material[i].id + '_' + 1 + '">' + data.material[i]
                            .material_name + ' - ' + data.material[i].unit + '</option>';
                    }
                    html += '</optgroup>';
                    html += '<optgroup label="Barang Setengah Jadi">';
                    for (var i = 0; i < data.inter.length; i++) {
                        html += '<option value="' + data.inter[i].id + '_' + 2 + '">' + data.inter[i]
                            .product_name + ' - ' + data.inter[i].unit + '</option>';
                    }
                    html += '</optgroup>';
                    html += '</select>';
                    html += '</div>';
                    html += '<div class="col-md-3">';
                    html +=
                        '<input type="number" class="form-control cust-control" id="quantity_' + com_index +
                        '" name="quantity[]" placeholder="quantitiy">';
                    html += '</div>';
                    html += '<div class="col-md-1">';
                    html +=
                        '<center><a onclick="delete_composition_item(' + com_index +
                        ')" href="javascript:void(0);" class="avatar-text avatar-md bg-danger text-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="fa fa-trash"></i></a></center>';
                    html += '</div>';
                    html += '</div>';

                    $(".baris").last().after(html);
                    $(".select-item").select2();
                }
            })
        }

        function delete_composition_item(id) {
            $("#baris_" + id).remove();
        }

        $('#upload-product-image').click(function() {
            $('#image').trigger('click');
        });

        $("#image").change(function() {

            var numFiles = $("#image")[0].files.length;

            var html = '';
            for (var i = 0; i < numFiles; i++) {
                html += '<img id="image_' + i +
                    '" class="img-preview" src="{{ asset('template/main/images/upload-icon.png') }}">';
            }

            $("#preview-container").html(html);
            for (var i = 0; i < numFiles; i++) {
                document.getElementById('image_' + i).src = window.URL.createObjectURL(this.files[i]);
            }

        });

$("#form-products-add").submit(function(e) {
    e.preventDefault();
    var id = $('#id').val();
    var desc = CKEDITOR.instances.description.getData();
    $("#desc_assist").val(desc);
    $.ajax({
        url: "{{ url('product') }}",
        type: "POST",
        data: new FormData($('form')[0]),
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.success) {
                Swal.fire({
                    title: "Berhasil Tambah Produk..!",
                    text: "Produk akan tampil di tabel Daftar Produk",
                    icon: "success",
                    showCancelButton: true,
                    cancelButtonColor: "#3085d6",
                    cancelButtonText: "Lihat Daftar Produk", // Atau teks lain yang kamu inginkan
                    showConfirmButton: false
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location = "{{ url('product') }}";
                    }
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Notice!...",
                    html: data.message,
                    footer: ''
                });
            }
        }
    });
});


        $(document).ready(function() {
            $("#price_text").keyup(function() {
                pemisah_ribuan("#price_text", "#price");

                var value = $("#price_text").val();
                $("#price_ta").val(value)
                $("#price_mp").val(value)
                $("#price_cus").val(value)
            });

            $(document).on('input', "#price_ta", function() {
                var value = $("#price_ta").val();
                var nilai = formatCurrency(value)
                $("#price_ta").val(nilai)
            });

            $(document).on('input', "#price_mp", function() {
                var value = $("#price_mp").val();
                var nilai = formatCurrency(value)
                $("#price_mp").val(nilai)
            });

            $(document).on('input', "#price_cus", function() {
                var value = $("#price_cus").val();
                var nilai = formatCurrency(value)
                $("#price_cus").val(nilai)
            });
        })

        function formatCurrency(angka, prefix) {
            if (!angka) {
                return (prefix || '') + '-';
            }

            angka = angka.toString();
            const splitDecimal = angka.split('.');
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            // tambahkan titik jika yang di input sudah menjadi angka ribuan
            if (ribuan) {
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix === undefined ? rupiah : rupiah ? (prefix || '') + rupiah : '';
        }

        function varian_price_keyup(id) {
            pemisah_ribuan("#varian_price_text_" + id, "#varian_price_" + id);
        }

        function onChangeCost() {
            pemisah_ribuan("#costtext", "#cost");
        }


        $("#buffered_stock").change(function() {
            var nilai = $(this).val();
            if (nilai == 1) {
                $("#stock_alert").removeAttr("readonly");
                $("#stock_alert").val(0);
                $("#costtext").attr('readonly', true);
            } else {
                $("#stock_alert").attr("readonly", true);
                $("#stock_alert").val("");
                $("#costtext").removeAttr('readonly');
            }
        });

        function product_more() {
            $("#name").val("");
            $("#category_id").val("");
            $("#sku").val("");
            $("#price").val("");
            $("#price_text").val("");
            $("#code").val("");
            $("#is_variant").val("1");
            $("#unit").val("");
            $("#barcode").val("");
            $("#buffered_stock").val("0");
            $("#is_manufactured").val("1");
            CKEDITOR.instances.description.setData("");
            $("#desc_assist").val("");
            $("#image").val(null);
            $("#composition-container").html("");
            $("#varian-table-container").html("");
            $("#preview-container").html("");
            $("#tambah-bahan-text").hide();
            $("#cost").val("");
            $("#costtext").val("");
            $("#weight").val("");
        }

        $(document).on('change', '#detailProductInput', function() {
            const detailProduct = document.getElementById('detailProduct');
            if (this.checked) {
                detailProduct.style.display = 'block';
            } else {
                detailProduct.style.display = 'none';
            }
        });
    </script>
@endif
