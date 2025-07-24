@if ($view == 'initial-capital')
    <script>
        let index_item = "{{ $total_item }}" + +2;
        let total_debit = 0;
        let total_kredit = 0;




        // addition_item();

        count_grandtotal();

        function count_grandtotal() {
            count_total_debit();
            count_total_kredit();
        }

        function addition_item() {
            var n = "{{ $total_item }}";

            for (var i = index_item; i < n; i++) {
                add_item_init(i);

            }
            count_total_debit();
            count_total_kredit();
        }


        $("#form-update-jurnal").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ url('save_initial_capital') }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {

                    if (data.success) {
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success"
                        });
                    } else {
                        show_error(data.message);
                    }
                }
            })
        })


        function set_debit(id) {
            pemisah_ribuan("#debittext_" + id, "#debit_" + id);
            var debit = $("#debit_" + id).val();
            if (debit != '') {
                $("#kredit_" + id).attr('readonly', true);
            } else {
                $("#kredit_" + id).removeAttr('readonly');
            }


            count_total_debit();

        }

        function count_total_debit() {
            total_debit = 0;
            for (var i = 1; i <= index_item; i++) {
                var debit = $("#debit_" + i).val() != '' ? $("#debit_" + i).val() : 0;
                if (debit === undefined) {
                    debit = 0;
                }

                total_debit = +total_debit + +debit;

            }
            count_total();
        }

        function count_total_kredit() {
            total_kredit = 0;
            for (var i = 1; i <= index_item; i++) {
                var kredit = $("#kredit_" + i).val() != '' ? $("#kredit_" + i).val() : 0;
                if (kredit === undefined) {
                    kredit = 0;
                }
                total_kredit = +total_kredit + +kredit;
            }
            count_total();
        }

        function count_total() {
            $(".label-debit").text(formatAngka(total_debit, "Rp."));
            $(".label-kredit").text(formatAngka(total_kredit, "Rp."));
        }

        function set_kredit(id) {
            pemisah_ribuan("#kredittext_" + id, "#kredit_" + id);
            var kredit = $("#kredit_" + id).val();
            if (kredit != '') {
                $("#debit_" + id).attr('readonly', true);
            } else {
                $("#debit_" + id).removeAttr('readonly');
            }
            count_total_kredit();
        }


        function delete_item(id) {
            $("#row_" + id).remove();
            count_total_debit();
            count_total_kredit();

        }

        function add_item() {
            index_item++;
            $.ajax({
                url: "{{ url('journal_multiple_form') }}",
                type: "GET",
                dataType: "JSON",
                success: function(data) {

                    var HTML = '';
                    HTML += '<div class="row row-item" id="row_' + index_item + '">';
                    HTML += '<div class="col-md-4">';

                    HTML += '<select class="form-control cust-control takun" id="akun_' + index_item +
                        '" name="akun[]">';
                    HTML += '<option value="">Pilih</option>';
                    for (var i = 0; i < data.data.group.length; i++) {
                        HTML += '<optgroup label="' + data.data.group[i] + '">';
                        for (var n = 0; n < data.data.data.length; n++) {
                            if (data.data.group[i] == data.data.data[n]['group']) {
                                HTML += '<option value="' + data.data.data[n]['id'] + '_' + data.data.data[n][
                                    'account_code_id'
                                ] + '">' + data.data.data[n]['name'] + '</option>';
                            }
                        }
                        HTML += '</optgroup>';
                    }


                    HTML += '</select>';

                    HTML += '</div>';

                    HTML += '<div class="col-md-4">';

                    HTML += '<input type="text" onkeyup="set_debit(' + index_item +
                        ')" class="form-control cust-control tdebit" placeholder="0" id="debittext_' +
                        index_item +
                        '">';
                    HTML += '<input type="hidden" id="debit_' + index_item + '" name="debit[]">';
                    HTML += '</div>';

                    HTML += '<div class="col-md-4">';

                    HTML += '<input type="text" onkeyup="set_kredit(' + index_item +
                        ')" class="form-control cust-control tkredit" placeholder="0" id="kredittext_' +
                        index_item +
                        '">';
                    HTML += '<input type="hidden" id="kredit_' + index_item + '" name="kredit[]">';
                    HTML += '<a href="javascript:void(0);" onclick="delete_item(' + index_item +
                        ')" type="button" class="btn btn-sm del-item tdelete"><i class="fa fa-remove"></i></a>';
                    HTML += '</div>';

                    $("#input_add_container").append(HTML);
                }
            })


        }


        function add_item_init(n) {

            var index_detail = +n + +1;

            index_item++;
            $.ajax({
                url: "{{ url('journal_multiple_form') }}",
                type: "GET",
                dataType: "JSON",
                async: true,
                success: function(data) {

                    var HTML = '';
                    HTML += '<div class="row row-item" id="row_' + index_item + '">';
                    HTML += '<div class="col-md-4">';

                    HTML += '<select class="form-control cust-control takun" id="akun_' + index_item +
                        '" name="akun[]">';
                    HTML += '<option value="">Pilih</option>';
                    for (var i = 0; i < data.data.group.length; i++) {
                        HTML += '<optgroup label="' + data.data.group[i] + '">';
                        for (var n = 0; n < data.data.data.length; n++) {
                            if (data.data.group[i] == data.data.data[n]['group']) {
                                HTML += '<option value="' + data.data.data[n]['id'] + '_' + data.data.data[n][
                                    'account_code_id'
                                ] + '">' + data.data.data[n]['name'] + '</option>';
                            }
                        }
                        HTML += '</optgroup>';
                    }


                    HTML += '</select>';

                    HTML += '</div>';

                    HTML += '<div class="col-md-4">';

                    HTML += '<input type="text" onkeyup="set_debit(' + index_item +
                        ')" class="form-control cust-control tdebit" placeholder="0" id="debit_' + index_item +
                        '" name="debit[]">';
                    HTML += '</div>';

                    HTML += '<div class="col-md-4">';

                    HTML += '<input type="text" onkeyup="set_kredit(' + index_item +
                        ')" class="form-control cust-control tkredit" placeholder="0" id="kredit_' +
                        index_item +
                        '" name="kredit[]">';
                    HTML += '<a href="javascript:void(0);" onclick="delete_item(' + index_item +
                        ')" type="button" class="btn btn-sm del-item tdelete"><i class="fa fa-remove"></i></a>';
                    HTML += '</div>';

                    $("#input_add_container").append(HTML);
                },

            })


        }
    </script>
@endif
