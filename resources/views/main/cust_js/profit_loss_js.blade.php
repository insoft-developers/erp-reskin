@if ($view == 'profit-loss')
    <script>
        function export_excel() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_too").val();
            var yearto = $("#year_too").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            window.open("{{ url('profit_loss_export') }}" + "/" + tanggal);
        }

        function export_pdf() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_too").val();
            var yearto = $("#year_too").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            window.open("{{ url('profit_loss_pdf') }}" + "/" + tanggal);
        }



        $("#form-profit-loss-submit").submit(function(e) {
            e.preventDefault();
            $("#l-image").show();
            $.ajax({
                url: "{{ !$userKey ? url('submit_profit_loss') : url('/api/submit_profit_loss') . '?user_key=' . $userKey }}",
                type: "POST",
                dataType: "JSON",
                data: $(this).serialize(),
                success: function(data) {
                    $("#l-image").hide();
                    console.log(data);
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
