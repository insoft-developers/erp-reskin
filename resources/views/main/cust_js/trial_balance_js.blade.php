@if ($view == 'trial-balance')
    <script>
        function export_excel() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_to").val();
            var yearto = $("#year_to").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            var url = "{{ !$userKey ? url('trial_balance_export') : url('/api/trial_balance_export') }}" + "/" +
                tanggal + "?user_key=" + "{{ $userKey ?? 'null' }}";

            window.open(url)
        }

        function export_pdf() {
            var monthfrom = $("#month_from").val();
            var yearfrom = $("#year_from").val();
            var monthto = $("#month_to").val();
            var yearto = $("#year_to").val();
            var tanggal = monthfrom + '_' + yearfrom + '_' + monthto + '_' + yearto;
            window.open("{{ !$userKey ? url('/trial_balance_pdf') : url('/api/trial_balance_pdf') }}" + "/" + tanggal +
                "?user_key=" + "{{ $userKey ?? 'null' }}");
        }

        $("#form-trial-balance-submit").submit(function(e) {
            e.preventDefault();

            $.ajax({
                url: "{{ !$userKey ? url('trial_balance_submit') : url('/api/trial_balance_submit') . '?user_key=' . $userKey }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(data) {
                    console.log(data);
                    $(".table-responsive").html(data.data);
                }
            })
        })
    </script>
@endif
