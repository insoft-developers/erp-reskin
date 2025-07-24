@if ($view == 'setting')
    <script>
        function on_opening_balance_click() {
            window.location = "{{ url('generate_opening_balance') }}";
        }

        function on_company_setting_click() {
            window.location = "{{ url('company_setting') }}";
        }

        function on_initial_capital() {
            window.location = "{{ url('initial_capital') }}";
        }

        function on_account_setting_click() {
            window.location = "{{ url('account_setting') }}";
        }

        function on_petty_cash_click() {
            window.location = "{{ url('petty_cash') }}";
        }

        function on_delete_initial_click() {
            window.location = "{{ url('initial_delete') }}";
        }
        function on_payment_method_setting() {
            window.location = "{{ url('payment-method-setting') }}";
        }
        function on_printer_setting_setting() {
            window.location = "{{ route('printer-setting') }}";
        }
        function redirect_to_account_settings_page() {
            window.location = "{{ route('account.profile.settings') }}";
        }
        function redirect_to_storefront_settings() {
            window.location = "{{ route('storefront-setting') }}";
        }
    </script>
@endif
