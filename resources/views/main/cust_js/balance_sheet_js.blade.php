@if ($view == 'balance-sheet')
    <script>
        function export_excel() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_too").val();
            var yearto = $("#year_too").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            window.open("{{ !$userKey ? url('balance_sheet_export') : url('/api/balance_sheet_export') }}" + "/" + tanggal + "?user_key=" + "{{$userKey ?? 'null'}}");
        }

        function export_pdf() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_too").val();
            var yearto = $("#year_too").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            window.open("{{ !$userKey ? url('/balance_sheet_pdf') : url('/api/balance_sheet_pdf') }}" + "/" + tanggal + "?user_key=" + "{{$userKey ?? 'null'}}");
        }


        $("#form-balance-sheet-submit").submit(function(e) {
            e.preventDefault();
            $("#l-image").show();
            $.ajax({
                url: "{{ !$userKey ? url('submit_balance_sheet') : url('/api/submit_balance_sheet') . '?user_key=' . $userKey }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {
                    $("#l-image").hide();
                    if (data.success) {
                        $(".table-responsive").html(data.data);
                        $('.null-data').closest('tr').remove();
                       
                        
                    } else {
                        Swal.fire({
                            title: "Failed!",
                            text: data.message,
                            icon: "error"
                        });
                    }
                }
            })
        });
    </script>
@endif
