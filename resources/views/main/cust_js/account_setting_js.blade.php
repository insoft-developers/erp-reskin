@if ($view == 'account-setting')
    <script>
        function on_account_item_click(id) {
            var akun = '';
            if (id == 0) {
                akun = "current_assets"
            } else if (id == 1) {
                akun = "fixed_assets";
            } else if (id == 2) {
                akun = "accumulated_depreciation";
            } else if (id == 3) {
                akun = "short_term_debt";
            } else if (id == 4) {
                akun = "long_term_debt";
            } else if (id == 5) {
                akun = "capital";
            } else if (id == 6) {
                akun = "income";
            } else if (id == 7) {
                akun = "cost_good_sold";
            } else if (id == 8) {
                akun = "selling_cost";
            } else if (id == 9) {
                akun = "admin_general_fees";
            } else if (id == 10) {
                akun = "non_business_income";
            } else if (id == 11) {
                akun = "non_business_expenses";
            }
            window.location = "{{ url('account_setting') }}" + "/" + akun;
        }
    </script>
@endif
