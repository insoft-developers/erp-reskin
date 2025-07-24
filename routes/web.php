<?php

use App\Exports\ProductPurchaseExport;
use App\Http\Controllers\AdministrativeController;
use App\Http\Controllers\API\AuthController;
use App\Http\Middleware\mAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DuitkuController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Main\DashboardController;
use App\Http\Controllers\Main\AccountController;
use App\Http\Controllers\Main\ReportController;
use App\Http\Controllers\Main\SettingController;
use App\Http\Controllers\Main\ProductMainController;
use App\Http\Controllers\Main\SupplierMainController;
use App\Http\Controllers\Main\QrcodeController;
use App\Http\Controllers\Frontstore\HomeController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MailTestController;
use App\Http\Controllers\Main\AdjustmentController;
use App\Http\Controllers\Main\CategoryAdjustmentController;
use App\Http\Controllers\Main\CategoryExpenseController;
use App\Http\Controllers\Main\CustomerController;
use App\Http\Controllers\Main\DebtController;
use App\Http\Controllers\Main\DiscountController;
use App\Http\Controllers\Main\ExpenseController;
use App\Http\Controllers\Main\FollowUpController;
use App\Http\Controllers\Main\PosController;
use App\Http\Controllers\API\PosController as PosControllerAPI;
use App\Http\Controllers\Main\PremiumController;
use App\Http\Controllers\Main\NotificationController;
use App\Http\Controllers\Main\MaterialMainController;
use App\Http\Controllers\Main\PengeluaranController;
use App\Http\Controllers\Main\ReceivableController;
use App\Http\Controllers\Main\RekapitulasiHarianController;
use App\Http\Controllers\Main\ShrinkageController;
use App\Http\Controllers\Main\InterProductController;
use App\Http\Controllers\Main\InterPurchaseController;
use App\Http\Controllers\ManajemenPesananController;
use App\Http\Controllers\MlCityController;
use App\Http\Controllers\SeederController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Main\CRMController;
use App\Http\Controllers\AuthStaffController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\CustomerServiceTemplateController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\FeatureRequestController;
use App\Http\Controllers\StoreFront\StorefrontController;
use App\Http\Controllers\StoreFront\CartController;
use App\Http\Controllers\StoreFront\CheckoutController;
use App\Http\Controllers\StoreFront\OrderController;
use App\Http\Controllers\WalletLogsController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\IMessageController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\Main\ClientController;
use App\Http\Controllers\Main\ConverseController;
use App\Http\Controllers\Main\InvoiceController;
use App\Http\Controllers\Main\KatalogRanduController;
use App\Http\Controllers\Main\LaporanAbsensiController;
use App\Http\Controllers\Main\LaporanKunjunganController;
use App\Http\Controllers\Main\LaporanPajakController;
use App\Http\Controllers\Main\LaporanPenjualanAdvanceController;
use App\Http\Controllers\Main\LaporanPenjualanController;
use App\Http\Controllers\Main\LaporanStockController;
use App\Http\Controllers\Main\ProductCategoryController;
use App\Http\Controllers\Main\ProductPurchaseController;
use App\Http\Controllers\Main\MaterialPurchaseController;
use App\Http\Controllers\Main\PaymentMethodFlagController;
use App\Http\Controllers\Main\ProductManufactureController;
use App\Http\Controllers\Main\RekapitulasiHarianV2Controller;
use App\Http\Controllers\Main\TransactionProductController;
use App\Http\Controllers\Main\TransferStockMaterialController;
use App\Http\Controllers\Main\TransferStockProductController;
use App\Http\Controllers\Main\WebviewController;
use App\Http\Controllers\OpnameController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\WebsocketController;
use App\Http\Controllers\WhatsappCrmProviderController;
use App\Http\Controllers\WhatsappCrmTemplateController;
use App\Models\InterProduct;
use App\Models\Journal;
use App\Models\JournalList;
use App\Models\Material;
use App\Models\Penjualan;
use App\Models\PenjualanProduct;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('quick-invoice', [InvoiceController::class, 'quickInvoice'])->name('quick.invoice');

Route::get('/akhir', function () {
    $tanggal_akhir = cal_days_in_month(CAL_GREGORIAN, '02', '2025');
    return $tanggal_akhir;
});

Route::get('neraca-webview/{userid}/{fmonth}/{fyear}/{tmonth}/{tyear}', [WebviewController::class, 'neraca']);
Route::get('profit-loss-webview/{userid}/{fmonth}/{fyear}/{tmonth}/{tyear}', [WebviewController::class, 'profit_loss']);


Route::get('get_view/{id}/{asset_data_id}/{account_data_id}', [DashboardController::class, 'get_view_data']);

Route::get('/mobile_account_remove', [AccountController::class, 'mobileAccountRemove']);
Route::post('/mobile_account_post', [AccountController::class, 'mobileAccountPost']);

Route::get('/gwc', [InternalController::class, 'generateWaCode']);



// Route::get('test_jurnal/{id}', [DashboardController::class, 'automate_journal']);

Route::get('/frontend_register', [AccountController::class, 'register']);
Route::post('/signup', [AccountController::class, 'signup']);
Route::get('/frontend_login', [AccountController::class, 'login']);
Route::post('/login_action', [AccountController::class, 'login_action'])->name('login.action');
Route::get('/login_as_admin', [AccountController::class, 'loginAsUser'])->name('login_as_admin');
// Route::post('/login_action_with_admin', [AccountController::class, 'login_action_with_admin'])->name('login.action_with_admin');
Route::get('/frontend_logout', [AccountController::class, 'logout']);
Route::get('/terimakasih', [AccountController::class, 'terimakasih']);
Route::get('/account_activate', [AccountController::class, 'account_activate']);
Route::get('/activation_success', [AccountController::class, 'activation_success'])->name('activation_success');
// Route::get('/send-test-email', [MailTestController::class, 'sendTestEmail']);
Route::get('/send-test-wa', [MailTestController::class, 'sendTestWa']);
Route::get('/forget-password', [AccountController::class, 'forgotPassword']);

Route::get('/checkout/{id}/{slug}', [LandingPageController::class, 'show'])->name('landing-page.custom-show');
Route::post('/checkout/{id}', [LandingPageController::class, 'checkout'])->name('landing-page.checkout');
Route::get('/order/{landing_id}/{reference_id}', [LandingPageController::class, 'order'])->name('landing-page.order');

Route::post('/checkout/{landing_id}/wa', [LandingPageController::class, 'checkoutByWa'])->name('landing-page.checkout-wa');
Route::get('/order-wa/{landing_id}/{bump}', [LandingPageController::class, 'orderByWa'])->name('landing-page.order-wa');

Route::get('/forgot_password', [ForgotPasswordController::class, 'index']);
Route::post('/forgot_password/send_token', [ForgotPasswordController::class, 'send_token'])->name('forgot_password.send_token');
Route::get('/forgot_password/reset_password', [ForgotPasswordController::class, 'reset_password']);
Route::get('/informasi_forgot_password', [ForgotPasswordController::class, 'information']);
Route::post('/forgot_password/change_password', [ForgotPasswordController::class, 'change_password'])->name('forgot_password.change_password');

Route::get('/run-seeder/{seeder}', [SeederController::class, 'runSeeder']);
Route::get('/view-clear', [SeederController::class, 'runViewClear']);
Route::get('/link_storage', function () {
    Artisan::call('storage:link');
});


Route::get('/print-receipt', [ReceiptController::class, 'printReceipt']);
Route::get('invoice/api/{id}/{termin_id?}', [InvoiceController::class, 'preview'])->name('invoice.preview');
Route::get('/pos/print-receipt', [PosController::class, 'printReceipt']);

Route::group(['middleware' => 'mAuth'], function () {
    Route::get('websocket', [WebsocketController::class, 'index']);
    Route::get('/v1/check-status-cashier', [PosControllerAPI::class, 'checkStatusCashier']);
    Route::post('/v1/open-cashier', [PosControllerAPI::class, 'openCashier']);
    Route::post('/v1/close-cashier', [PosControllerAPI::class, 'closeCashier']);
    Route::get('/v1/product-categories', [PosControllerAPI::class, 'productCategory']);
    Route::get('/v1/tables', [PosControllerAPI::class, 'tables']);
    Route::get('/v1/qr-code-meja', [PosControllerAPI::class, 'qrCodeMeja']);
    Route::get('/v1/product', [PosControllerAPI::class, 'product']);
    Route::get('/v1/voucher', [PosControllerAPI::class, 'discount']);
    Route::get('/v1/voucher-web', [PosControllerAPI::class, 'discount']);
    Route::post('/v1/payment-validation', [PosControllerAPI::class, 'paymentValidation']);
    Route::get('/v1/show-order-detail', [PosControllerAPI::class, 'orderDetail']);
    Route::get('/v1/user', [AuthController::class, 'user']);
    Route::get('/v1/customer', [PosControllerAPI::class, 'customerList']);
    Route::post('/v1/customer', [PosControllerAPI::class, 'customerCreate']);
    Route::get('/v1/type-payment', [PosControllerAPI::class, 'typePayment']);
    Route::post('/v1/payment', [PosControllerAPI::class, 'payment']);
    Route::get('/v1/payment-check', [PosControllerAPI::class, 'paymentCheck']);
    Route::get('/v1/instant-qris', [PosControllerAPI::class, 'instantQris']);
    Route::get('/v1/administrative/provinces', [AdministrativeController::class, 'getProvince']);
    Route::get('/v1/administrative/cities', [AdministrativeController::class, 'getCity']);
    Route::get('/v1/administrative/districts', [AdministrativeController::class, 'getDistrict']);
    Route::get('/v1/get-list-flag', [PaymentMethodFlagController::class, 'getFlags']);

    Route::get('/v1/customer-service/template', [CustomerServiceTemplateController::class, 'index']);

    Route::post('/v1/chat-ai', [ChatController::class, 'sendMessage']);
    Route::get('/v1/chat-ai/conversation-list', [ChatController::class, 'listConversation']);
    Route::get('/v1/chat-ai/conversation-detail', [ChatController::class, 'detailConversation']);
    Route::get('/v1/chat-ai/new-conversation', [ChatController::class, 'newConversation']);
    Route::get('/v1/chat-ai/fav-conversation', [ChatController::class, 'favConversation']);
    Route::get('/v1/chat-ai/fav-conversation-ctg', [ChatController::class, 'favConversationCtg']);

    Route::get('/v1/account-reset', [AuthController::class, 'accountReset']);
    Route::get('/v1/account-reset-by-otp', [AuthController::class, 'accountResetStartAction']);

    Route::prefix('/v1/imessage')->group(function () {
        Route::get('status',                         [IMessageController::class, 'status']);
        Route::post('create-session/{id}',           [IMessageController::class, 'getQr']);
        Route::post('check-session/{id}',            [IMessageController::class, 'checkSession']);
        Route::post('logout-session/{id}',           [IMessageController::class, 'logoutSession']);

        Route::post('customer-service',              [IMessageController::class, 'addCustomerService']);
        Route::delete('customer-service',            [IMessageController::class, 'deleteCustomerService']);
        Route::put('customer-service',               [IMessageController::class, 'updateCustomerService']);
    });

    Route::get('/v1/feature-request-category', [FeatureRequestController::class, 'getData'])->name('feature-request.data');
    Route::resource('feature-request', FeatureRequestController::class);
    Route::get('feature-request/data/table', [FeatureRequestController::class, 'getDataTable'])->name('feature-request.datatable');

    Route::get('/', [DashboardController::class, 'index']);
    Route::resource('landing-page', LandingPageController::class);
    Route::get('/landing-page/{id}/destroy', [LandingPageController::class, 'destroy'])->name('landing-page.custom-destroy');
    Route::get('landing-pages/data', [LandingPageController::class, 'getData'])->name('landing-pages.data');
    Route::get('/landing-page/content-builder/{id}', [LandingPageController::class, 'contentBuilder'])->name('landing-page.content-builder');
    Route::patch('/landing-page/content-builder/{id}', [LandingPageController::class, 'storeContent'])->name('landing-page.content-builder.store');
    Route::post('/landing-page/content-builder/upload_file', [LandingPageController::class, 'uploadFile'])->name('landing-page.content-builder.upload_file');
    Route::post('/landing-page/content-builder/remove_file', [LandingPageController::class, 'removeFile'])->name('landing-page.content-builder.remove_file');

    Route::get('customer-service', [CustomerServiceController::class, 'index'])->name('customer-service.index');
    Route::get('customer-service/{id}/templates', [CustomerServiceController::class, 'show'])->name('customer-service.show');
    Route::get('customer-service/{id}/templates/data', [CustomerServiceController::class, 'showGetData'])->name('customer-service.show.getData');
    Route::post('customer-service/{id}/template', [CustomerServiceController::class, 'saveTemplate'])->name('customer-service.show.saveTemplate');
    Route::get('customer-service/data', [CustomerServiceController::class, 'getData'])->name('customer-service.getData');

    Route::get('whatsapp-provider/data', [WhatsappCrmProviderController::class, 'getData'])->name('whatsapp-provider.data');
    Route::resource('whatsapp-provider', WhatsappCrmProviderController::class);

    Route::resource('whatsapp-crm-template', WhatsappCrmTemplateController::class);

    Route::get('/manajemen-pesanan', [ManajemenPesananController::class, 'index']);
    Route::get('/manajemen-pesanan/data', [ManajemenPesananController::class, 'getData'])->name('manajemen-data.data');
    Route::get('/manajemen-pesanan/cart', [ManajemenPesananController::class, 'getDataCart'])->name('manajemen-data.cart');
    Route::post('/manajemen-pesanan/bulk/update-status', [ManajemenPesananController::class, 'bulk_update_status'])->name('manajemen-pesanan.bulk.update-status');
    Route::post('/manajemen-pesanan/bulk/payment-status', [ManajemenPesananController::class, 'bulk_payment_status'])->name('manajemen-pesanan.bulk.payment-status');
    Route::post('/manajemen-pesanan/bulk/payment-method', [ManajemenPesananController::class, 'bulk_payment_method'])->name('manajemen-pesanan.bulk.payment-method');
    Route::post('/manajemen-pesanan/bulk/sync-status', [ManajemenPesananController::class, 'bulk_sync_status'])->name('manajemen-pesanan.bulk.sync-status');
    Route::post('/manajemen-pesanan/single_sync', [ManajemenPesananController::class, 'single_sync'])->name('manajemen-pesanan.single.sync');
    Route::post('/manajemen-pesanan/single_paid', [ManajemenPesananController::class, 'single_paid'])->name('manajemen-pesanan.single.paid');
    Route::post('/manajemen-pesanan/single_refund', [ManajemenPesananController::class, 'single_refund'])->name('manajemen-pesanan.single.refund');
    Route::post('/manajemen-pesanan/single_unsync', [ManajemenPesananController::class, 'single_unsync'])->name('manajemen-pesanan.single.unsync');
    Route::post('/manajemen-pesanan/single_void', [ManajemenPesananController::class, 'single_void'])->name('manajemen-pesanan.single.void');

    Route::get('/api/products', [ProductController::class, 'api'])->name('api.get_products');
    Route::get('/api/products/{id}', [ProductController::class, 'apiByid']);
    // Route::get('/api/get_proty', [MlCityController::class, 'api'])->name('api.get_proties');
    Route::post('journal_table', [DashboardController::class, 'journal_table'])->name('journal.table');
    Route::get('/get_account_receive/{id}', [DashboardController::class, 'get_account_receive']);
    Route::post('/save_jurnal', [DashboardController::class, 'save_jurnal']);
    Route::get('journal_add', [DashboardController::class, 'journal_add']);
    Route::get('journal_edit/{id}', [DashboardController::class, 'journal_edit']);
    Route::get('journal_multiple_form', [DashboardController::class, 'journal_multiple_form']);
    Route::post('save_multiple_journal', [DashboardController::class, 'save_multiple_journal']);
    Route::post('confirm_journal_delete', [DashboardController::class, 'confirm_journal_delete']);
    Route::get('get_detail/{id}', [DashboardController::class, 'get_detail']);
    Route::post('journal_update', [DashboardController::class, 'journal_update']);
    Route::get('lihat_saldo_awal/{id}', [DashboardController::class, 'lihat_saldo_awal']);

    Route::get('report', [ReportController::class, 'index']);
    Route::get('profit_loss', [ReportController::class, 'profit_loss']);
    Route::post('submit_profit_loss', [ReportController::class, 'submit_profit_loss']);
    Route::get('balance', [ReportController::class, 'balance']);
    Route::post('submit_balance_sheet', [ReportController::class, 'submit_balance_sheet']);
    Route::get('journal_report', [ReportController::class, 'journal_report']);
    Route::post('journal_report_submit', [ReportController::class, 'journal_report_submit']);
    Route::get('trial_balance', [ReportController::class, 'trial_balance']);
    Route::post('trial_balance_submit', [ReportController::class, 'trial_balance_submit']);
    Route::get('general_ledger', [ReportController::class, 'general_ledger']);
    Route::post('general_ledger_submit', [ReportController::class, 'general_ledger_submit']);

    Route::get('profit_loss_export/{tanggal}', [ReportController::class, 'profit_loss_export']);
    Route::get('balance_sheet_export/{tanggal}', [ReportController::class, 'balance_sheet_export']);
    Route::get('trial_balance_export/{tanggal}', [ReportController::class, 'trial_balance_export']);
    Route::get('journal_report_export/{tanggal}', [ReportController::class, 'journal_report_export']);
    Route::get('general_ledger_export/{tanggal}', [ReportController::class, 'general_ledger_export']);


    Route::get('journal_report_pdf/{tanggal}', [ReportController::class, 'journal_report_pdf']);
    Route::get('general_ledger_pdf/{tanggal}', [ReportController::class, 'general_ledger_pdf']);
    Route::get('trial_balance_pdf/{tanggal}', [ReportController::class, 'trial_balance_pdf']);
    Route::get('profit_loss_pdf/{tanggal}', [ReportController::class, 'profit_loss_pdf']);
    Route::get('balance_sheet_pdf/{tanggal}', [ReportController::class, 'balance_sheet_pdf']);

    Route::get('setting', [SettingController::class, 'index']);
    Route::get('generate_opening_balance', [SettingController::class, 'generate_opening_balance']);
    Route::post('submit_opening_balance', [SettingController::class, 'submit_opening_balance']);
    Route::get('petty_cash', [SettingController::class, 'petty_cash']);
    Route::post('petycash_update', [SettingController::class, 'petycash_update']);
    Route::post('confirm_hapus_akun', [SettingController::class, 'confirmHapusAkun'])->name('confirm.hapus.akun');

    Route::get('company_setting', [SettingController::class, 'company_setting']);
    Route::post('company_setting_update', [SettingController::class, 'company_setting_update']);
    Route::get('initial_capital', [SettingController::class, 'initial_capital']);
    Route::post('save_initial_capital', [SettingController::class, 'save_initial_capital']);
    Route::get('account_setting', [SettingController::class, 'account_setting']);
    Route::get('account_setting/{akun}', [SettingController::class, 'account_detail']);
    Route::post('save_setting_account', [SettingController::class, 'save_setting_account']);
    Route::get('initial_delete', [SettingController::class, 'initial_delete']);
    Route::post('confirm_hapus_saldo', [SettingController::class, 'confirm_hapus_saldo']);
    Route::get('payment-method-setting', [SettingController::class, 'payment_method_setting'])->name('payment-method-setting.index');
    Route::get('payment-method-flag-data', [PaymentMethodFlagController::class, 'getData'])->name('payment-method-flag.data');
    Route::resource('payment-method-flag', PaymentMethodFlagController::class);
    Route::post('save-payment-method', [SettingController::class, 'store_payment_method']);
    Route::get('printer-setting', [SettingController::class, 'printer_setting'])->name('printer-setting');
    Route::post('save-printer', [SettingController::class, 'store_printer'])->name('save-printer');
    // QR Code Meja
    Route::get('qr-code', [QrcodeController::class, 'index']);
    Route::post('add-qrcode-meja', [QrcodeController::class, 'add'])->name('add-qrcode-meja');
    Route::post('edit-qrcode-meja', [QrcodeController::class, 'edit'])->name('edit-qrcode-meja');
    Route::post('delete-qrcode-meja', [QrcodeController::class, 'delete'])->name('delete-qrcode-meja');
    Route::post('set-qrcode-availaibility', [QrcodeController::class, 'set_availability'])->name('set-qrcode-availaibility');
    Route::get('ajax-get-data-qrcode', [QrcodeController::class, 'ajax_get_data'])->name('ajax-get-qrcode');
    Route::get('print-qr-code', [QrcodeController::class, 'print_qr_code']);
    // Wallet Logs
    Route::get('/wallet-logs', [WalletLogsController::class, 'index'])->name('wallet.index');
    Route::get('/wallet-logs/data', [WalletLogsController::class, 'getData'])->name('wallet-logs.data');
    Route::get('/wallet-logs/filter-data', [WalletLogsController::class, 'filterData'])->name('wallet-logs.data.filter');
    Route::post('/topup-duitku', [WalletLogsController::class, 'topupDuitku'])->name('topup.duitku');
    Route::post('/withdraw-duitku', [WalletLogsController::class, 'withdrawDuitku'])->name('withdraw.duitku');
    // Store Front Setting
    Route::get('/storefront/setting', [StorefrontController::class, 'setting'])->name('storefront-setting');
    Route::post('/storefront/save', [StorefrontController::class, 'store'])->name('storefront-save');
    Route::post('/storefront/check-username', [StorefrontController::class, 'usernameChecker'])->name('storefront-username-check');

    Route::resource('product', ProductMainController::class);
    Route::post('store_display_change', [ProductMainController::class, 'store_display_change']);
    Route::post('store_editable_change', [ProductMainController::class, 'store_editable_change']);
    Route::post('product_table', [ProductMainController::class, 'product_table'])->name('product.table');
    Route::post('delete_multiple_product', [ProductMainController::class, 'delete_multiple_product']);
    Route::get('product_export/{id}', [ProductMainController::class, 'product_export']);
    Route::get('get_bahan_product', [ProductMainController::class, 'get_bahan_product']);
    Route::get('get_composition_product/{id}', [ProductMainController::class, 'getComposisitionEdit']);
    Route::get('open_product_add', [ProductMainController::class, 'open_product_add']);
    Route::post('product_upload', [ProductMainController::class, 'product_upload']);
    Route::post('use_stock', [ProductMainController::class, 'use_stock']);

    Route::resource('main_supplier', SupplierMainController::class);
    Route::get('supplier_table', [SupplierMainController::class, 'supplier_table'])->name('supplier.table');

    Route::resource('main_material', MaterialMainController::class);
    Route::get('material_table', [MaterialMainController::class, 'material_table'])->name('material.table');
    Route::get('material_category_update', [MaterialMainController::class, 'material_category_update']);
    Route::post('material_upload', [MaterialMainController::class, 'material_upload']);

    Route::resource('inter_product', InterProductController::class);
    Route::get('inter_table', [InterProductController::class, 'inter_table'])->name('inter.table');
    Route::get('get_data_non_product', [InterProductController::class, 'get_data_non_product']);
    Route::get('inter_category_update', [InterProductController::class, 'inter_category_update']);


    Route::resource('product_category', ProductCategoryController::class);
    Route::get('product_category_table', [ProductCategoryController::class, 'product_category_table'])->name('product.category.table');


    Route::resource('product_purchase', ProductPurchaseController::class);
    Route::get('product_purchase_table', [ProductPurchaseController::class, 'product_purchase_table'])->name('product.purchase.table');
    Route::get('tambah_item', [ProductPurchaseController::class, 'tambah_item']);
    Route::post('product_purchase_sync', [ProductPurchaseController::class, 'sync'])->name('product.purchase.sync');
    Route::post('product_purchase_type', [ProductPurchaseController::class, 'productPurchaseType'])->name('product.purchase.type');

    Route::get('download_template_pembelian', [ProductPurchaseController::class, 'download_template_pembelian']);
    Route::post('product_purchase_upload', [ProductPurchaseController::class, 'product_purchase_upload']);


    Route::resource('converse', ConverseController::class);
    Route::get('converse_table', [ConverseController::class, 'converse_table'])->name('converse.table');
    Route::post('conversion_selected_item', [ConverseController::class, 'conversion_selected_item']);
    Route::post('converse_sync', [ConverseController::class, 'sync'])->name('converse.sync');
    Route::post('converse_unsync', [ConverseController::class, 'unsync'])->name('converse.unsync');




    Route::resource('material_purchase', MaterialPurchaseController::class);
    Route::get('material_purchase_table', [MaterialPurchaseController::class, 'material_purchase_table'])->name('material.purchase.table');
    Route::get('tambah_item_material', [MaterialPurchaseController::class, 'tambah_item']);
    Route::post('material_purchase_sync', [MaterialPurchaseController::class, 'sync'])->name('material.purchase.sync');
    Route::get('download_template_pembelian_material', [MaterialPurchaseController::class, 'download_template_pembelian_material']);
    Route::post('material_purchase_upload', [MaterialPurchaseController::class, 'material_purchase_upload']);

    Route::resource('inter_purchase', InterPurchaseController::class);
    Route::get('inter_purchase_table', [InterPurchaseController::class, 'inter_purchase_table'])->name('inter.purchase.table');
    Route::post('change_product_select', [InterPurchaseController::class, 'change_product_select']);
    Route::post('inter_purchase_sync', [InterPurchaseController::class, 'sync'])->name('inter.purchase.sync');

    Route::resource('product_manufacture', ProductManufactureController::class);
    Route::get('product_manufacture_table', [ProductManufactureController::class, 'product_manufacture_table'])->name('product.manufacture.table');
    Route::post('change_manufacture_select', [ProductManufactureController::class, 'change_manufacture_select']);
    Route::post('product_manufacture_sync', [ProductManufactureController::class, 'sync'])->name('product.manufacture.sync');


    Route::resource('branch', BranchController::class);
    Route::post('branch-table', [BranchController::class, 'getBranchTable'])->name('branch.table.api');

    Route::resource('branch', BranchController::class);
    Route::post('branch-table', [BranchController::class, 'getBranchTable'])->name('branch.table.api');

    Route::resource('branch', BranchController::class);
    Route::post('branch-table', [BranchController::class, 'getBranchTable'])->name('branch.table.api');

    Route::get('/pos/index', [PosController::class, 'index']);
    Route::get('/pos/metode-pembayaran', [PosController::class, 'metode_pembayaran']);
    Route::get('/pos/terima-kasih', [PosController::class, 'selesai_pembayaran']);
    Route::post('/pos/send-receipt', [PosController::class, 'sendReceipt'])->name('pos.send-receipt');

    // CRM
    Route::prefix('crm')
        ->name('crm.')
        ->group(function () {
            // CUSTOMER
            Route::get('/customer-data', [CustomerController::class, 'data'])->name('customer_data');
            Route::resource('customer', CustomerController::class);

            // FOLLOWUP
            Route::resource('followup', FollowUpController::class);
            Route::get('/followup-data', [FollowUpController::class, 'data'])->name('followup_data');

            // DISCOUNT
            Route::resource('discount', DiscountController::class);
            Route::get('/discount-data', [DiscountController::class, 'data'])->name('discount_data');
        });

    Route::prefix('premium')
        ->name('premium.')
        ->group(function () {
            Route::get('/', [PremiumController::class, 'index'])->name('index');
            Route::post('/{id}', [PremiumController::class, 'store'])->name('store');
        });

    Route::resource('staff', StaffController::class);

    // NOTIFICATION
    Route::prefix('notification')
        ->name('notification.')
        ->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/detail/{id}', [NotificationController::class, 'show'])->name('show');
            Route::get('markAllAsRead', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
            Route::get('show-popup-notification', [NotificationController::class, 'showPopupNotif'])->name('showPopupNotif');
            Route::get('hide-popup', [NotificationController::class, 'hidePopup'])->name('hidePopup');
        });

    // ADJUSTMENT
    Route::prefix('adjustment')
        ->name('adjustment.')
        ->group(function () {
            // CATEGORY
            Route::get('/category-data', [CategoryAdjustmentController::class, 'data'])->name('category_data');
            Route::delete('category/destroy-all', [CategoryAdjustmentController::class, 'destroyAll'])->name('category.destroyAll');
            Route::resource('category', CategoryAdjustmentController::class);

            // ADJUSTMENT
            Route::get('/data', [AdjustmentController::class, 'data'])->name('data');
            Route::get('/cost-good-sold', [AdjustmentController::class, 'costGoodSold'])->name('costGoodSold');
            Route::get('/inter-product', [AdjustmentController::class, 'interProduct'])->name('interProduct');
            Route::get('create/inter-product', [AdjustmentController::class, 'createInterProduct'])->name('createInterProduct');
            Route::get('create/material', [AdjustmentController::class, 'createMaterial'])->name('createMaterial');
            Route::get('/material', [AdjustmentController::class, 'material'])->name('material');
            Route::delete('/destroy-all', [AdjustmentController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [AdjustmentController::class, 'sync'])->name('sync');
            Route::post('/store/inter-product', [AdjustmentController::class, 'storeInterProduct'])->name('storeInterProduct');
            Route::post('/store/material', [AdjustmentController::class, 'storeMaterial'])->name('storeMaterial');
            Route::post('/single_sync', [AdjustmentController::class, 'single_sync'])->name('single.sync');
            Route::post('/single_unsync', [AdjustmentController::class, 'single_unsync'])->name('single.unsync');
            Route::get('/stock-opname', [OpnameController::class, 'index'])->name('stock.opname');
            Route::get('/opname-table', [OpnameController::class, 'opname_table'])->name('opname.table');
            Route::post('/opname-store', [OpnameController::class, 'opname_store'])->name('opname.store');
            Route::get('/opname_product_detail/{id}', [OpnameController::class, 'opname_product_detail']);
            Route::get('/download_template_opname/{id}', [OpnameController::class, 'download_template_opname']);
            Route::post('/opname-upload', [OpnameController::class, 'opname_upload'])->name('opname.upload');
            Route::post('/sesuaikan-opname', [OpnameController::class, 'sesuaikan_opname'])->name('sesuaikan.opname');

            Route::post('/opname_unsync', [OpnameController::class, 'opname_unsync'])->name('opname.unsync');
            Route::post('/opname_sync', [OpnameController::class, 'opname_sync'])->name('opname.sync');
            Route::post('/opname_delete', [OpnameController::class, 'opname_delete'])->name('opname.delete');
        });
    Route::resource('adjustment', AdjustmentController::class);

    // EXPENSE
    Route::prefix('expense')
        ->name('expense.')
        ->group(function () {
            // CATEGORY
            Route::get('/category-data', [CategoryExpenseController::class, 'data'])->name('category_data');
            Route::get('/category-get-data', [CategoryExpenseController::class, 'getData'])->name('category_getData');
            Route::get('/category/delete-product/{id}', [CategoryExpenseController::class, 'deleteProduct'])->name('category.deleteProduct');
            Route::delete('/category/destroy-all', [CategoryExpenseController::class, 'destroyAll'])->name('category.destroyAll');
            Route::resource('category', CategoryExpenseController::class);

            // EXPENSE
            Route::get('/data', [ExpenseController::class, 'data'])->name('data');
            Route::get('/from', [ExpenseController::class, 'from'])->name('from');
            Route::get('/to', [ExpenseController::class, 'to'])->name('to');
            Route::delete('/destroy-all', [ExpenseController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [ExpenseController::class, 'sync'])->name('sync');
            Route::post('/single_sync', [ExpenseController::class, 'single_sync'])->name('single.sync');
            Route::post('/single_unsync', [ExpenseController::class, 'single_unsync'])->name('single.unsync');
        });
    Route::resource('expense', ExpenseController::class);

    // PENYUSUTAN
    Route::prefix('penyusutan')
        ->name('penyusutan.')
        ->group(function () {
            // PENYUSUTAN
            Route::get('/data', [ShrinkageController::class, 'data'])->name('data');
            Route::get('/mlFixedAsset', [ShrinkageController::class, 'mlFixedAsset'])->name('mlFixedAsset');
            Route::get('/mlAccumulateDepreciation', [ShrinkageController::class, 'mlAccumulateDepreciation'])->name('mlAccumulateDepreciation');
            Route::get('/mlAdminGeneralFee', [ShrinkageController::class, 'mlAdminGeneralFee'])->name('mlAdminGeneralFee');
            Route::delete('/destroy-all', [ShrinkageController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [ShrinkageController::class, 'sync'])->name('sync');
            Route::post('/single_unsync', [ShrinkageController::class, 'single_unsync'])->name('single.unsync');
            Route::post('/penyusutan_lost', [ShrinkageController::class, 'lost'])->name('lost');
            Route::post('/penyusutan_lost_store', [ShrinkageController::class, 'lost_store'])->name('lost-store');
        });
    Route::resource('penyusutan', ShrinkageController::class);

    // UTANG
    Route::prefix('utang')
        ->name('utang.')
        ->group(function () {
            Route::get('/data', [DebtController::class, 'data'])->name('data');
            Route::get('/type', [DebtController::class, 'type'])->name('type');
            Route::get('/debtFrom', [DebtController::class, 'debtFrom'])->name('debtFrom');
            Route::get('/subType', [DebtController::class, 'subType'])->name('subType');
            Route::get('/saveTo', [DebtController::class, 'saveTo'])->name('saveTo');
            Route::get('/payment/{id}', [DebtController::class, 'todoPayment'])->name('todoPayment');
            Route::post('/payment/{id}', [DebtController::class, 'payment'])->name('payment');
            Route::delete('/destroy-all', [DebtController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [DebtController::class, 'sync'])->name('sync');
            Route::post('/sync_payment', [DebtController::class, 'sync_payment'])->name('sync_payment');
            Route::post('/unsync_payment', [DebtController::class, 'unsync_payment'])->name('unsync.payment');
            Route::post('/single_sync', [DebtController::class, 'single_sync'])->name('single.sync');
            Route::post('/single_unsync', [DebtController::class, 'single_unsync'])->name('single.unsync');
            Route::post('/utang_delete', [DebtController::class, 'delete_payment'])->name('delete.payment');
        });
    Route::resource('utang', DebtController::class);

    Route::prefix('piutang')
        ->name('piutang.')
        ->group(function () {
            Route::get('/data', [ReceivableController::class, 'data'])->name('data');
            Route::get('/type', [ReceivableController::class, 'type'])->name('type');
            Route::get('/from', [ReceivableController::class, 'from'])->name('from');
            Route::get('/saveTo', [ReceivableController::class, 'saveTo'])->name('saveTo');
            Route::get('/subType', [ReceivableController::class, 'subType'])->name('subType');
            Route::get('/payment/{id}', [ReceivableController::class, 'todoPayment'])->name('todoPayment');
            Route::post('/payment/{id}', [ReceivableController::class, 'payment'])->name('payment');
            Route::delete('/destroy-all', [ReceivableController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [ReceivableController::class, 'sync'])->name('sync');
            Route::post('/sync_payment', [ReceivableController::class, 'sync_payment'])->name('sync_payment');
            Route::post('/single_sync', [ReceivableController::class, 'single_sync'])->name('single.sync');
            Route::post('/single_unsync', [ReceivableController::class, 'single_unsync'])->name('single.unsync');
            Route::post('/unsync_payment', [ReceivableController::class, 'unsync_payment'])->name('unsync.payment');
            Route::post('/payment_delete', [ReceivableController::class, 'delete_payment'])->name('delete.payment');
        });
    Route::resource('piutang', ReceivableController::class);

    Route::get('pos', function () {
        $view = 'pos';
        return view('main.pos.index', compact('view'));
    })->name('pos.index');

    // PENGELUARAN OUTLET
    Route::prefix('pengeluaran')
        ->name('pengeluaran.')
        ->group(function () {
            Route::get('/data', [PengeluaranController::class, 'data'])->name('data');
            Route::delete('/destroy-all', [PengeluaranController::class, 'destroyAll'])->name('destroyAll');
            Route::post('/sync', [PengeluaranController::class, 'sync'])->name('sync');
            Route::post('/single_sync', [PengeluaranController::class, 'single_sync'])->name('single.sync');
            Route::post('/single_unsync', [PengeluaranController::class, 'single_unsync'])->name('single.unsync');
        });
    Route::resource('pengeluaran', PengeluaranController::class);

    // REKAPITULASI HARIAN
    Route::prefix('rekapitulasi-harian')
        ->name('rekapitulasi-harian.')
        ->group(function () {
            Route::delete('/destroy-all', [RekapitulasiHarianController::class, 'destroyAll'])->name('destroyAll');
        });
    Route::resource('rekapitulasi-harian', RekapitulasiHarianController::class);
    Route::resource('rekapitulasi-v2-harian', RekapitulasiHarianV2Controller::class);
    Route::get('/rekapitulasi-harian-data', [RekapitulasiHarianController::class, 'getData']);

    Route::get('account-profile-settings', [SettingController::class, 'showAccount'])->name('account.profile.settings');
    Route::prefix('laporan')
        ->name('laporan.')
        ->group(function () {
            Route::get('/penjualan/data', [LaporanPenjualanController::class, 'data'])->name('penjualan.data');
            Route::get('/penjualan/export-excel', [LaporanPenjualanController::class, 'exportExcel'])->name('penjualan.exportExcel');
            Route::get('/penjualan/export-pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('penjualan.exportPdf');
            Route::get('/penjualan/chart-regular', [LaporanPenjualanController::class, 'chartRegular'])->name('penjualan.chart-regular');
            Route::get('/penjualan/chart', [LaporanPenjualanController::class, 'chart'])->name('penjualan.chart');
            Route::get('/penjualan/chart-basic', [LaporanPenjualanController::class, 'chartBasic'])->name('penjualan.chart.basic');
            Route::get('/penjualan/chart-expenses', [LaporanPenjualanController::class, 'chartExpenses'])->name('penjualan.chart.expenses');
            Route::get('/penjualan/chart-sales', [LaporanPenjualanController::class, 'chartSales'])->name('penjualan.chart.sales');
            Route::get('/penjualan/category-expense', [LaporanPenjualanController::class, 'categoryExpense'])->name('penjualan.categoryExpense');
            Route::get('/penjualan', [LaporanPenjualanController::class, 'index'])->name('penjualan.index');

            Route::get('/penjualan-advance', [LaporanPenjualanAdvanceController::class, 'index'])->name('penjualan.advance.index');
            Route::get('/penjualan/export-excel-advance', [LaporanPenjualanAdvanceController::class, 'exportExcel'])->name('penjualan.exportExcel.advance');
            Route::get('/penjualan/export-pdf-advance', [LaporanPenjualanAdvanceController::class, 'exportPdf'])->name('penjualan.exportPdf.advance');
            Route::post('/penjualan/export-excel-advance-queue', [LaporanPenjualanAdvanceController::class, 'exportExcelQueue'])->name('penjualan.exportExcelQueue.advance');
            Route::post('/penjualan/export-pdf-advance-queue', [LaporanPenjualanAdvanceController::class, 'exportPdfQueue'])->name('penjualan.exportPdfQueue.advance');

            Route::get('/pajak/data', [LaporanPajakController::class, 'data'])->name('pajak.data');
            Route::get('/pajak/export', [LaporanPajakController::class, 'export'])->name('pajak.export');
            Route::get('/pajak/chart', [LaporanPajakController::class, 'chart'])->name('pajak.chart');
            Route::get('/pajak', [LaporanPajakController::class, 'index'])->name('pajak.index');

            Route::get('/absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
            Route::get('/absensi/data', [LaporanAbsensiController::class, 'data'])->name('absensi.data');
            Route::get('/absensi/export', [LaporanAbsensiController::class, 'export'])->name('absensi.export');

            Route::get('/absensi-by-date', [LaporanAbsensiController::class, 'indexByDate'])->name('absensi.indexByDate');
            Route::get('/absensi/data-by-date', [LaporanAbsensiController::class, 'dataByDate'])->name('absensi.dataByDate');
            Route::get('/absensi/export-by-date', [LaporanAbsensiController::class, 'exportByDate'])->name('absensi.exportByDate');

            Route::get('/stock/data', [LaporanStockController::class, 'data'])->name('stock.data');
            Route::get('/stock/export', [LaporanStockController::class, 'export'])->name('stock.export');
            Route::get('/stock', [LaporanStockController::class, 'index'])->name('stock.index');
            Route::get('/stock/sumDataBarangJadi', [LaporanStockController::class, 'getDataBarangJadi'])->name('stock.sumDataBarangJadi');
            Route::get('/stock/sumDataManufaktur', [LaporanStockController::class, 'getDataManufaktur'])->name('stock.sumDataManufaktur');
            Route::get('/stock/sumDataSetBarangJadi', [LaporanStockController::class, 'getDataSetBarangJadi'])->name('stock.sumDataSetBarangJadi');
            Route::get('/stock/sumDataMaterial', [LaporanStockController::class, 'getDataMaterial'])->name('stock.sumDataMaterial');
            Route::post('/sync-stock', [LaporanStockController::class, 'syncStock'])->name('stock.syncStock');
        });

    // Katalog Randu
    Route::post('/katalog-randu/add-to-cart/{id}', [KatalogRanduController::class, 'addToCart'])->name('katalog-randu.add-to-cart');
    Route::delete('/katalog-randu/remove-to-cart/{id}', [KatalogRanduController::class, 'removeToCart'])->name('katalog-randu.remove-to-cart');
    Route::put('/katalog-randu/update-cart/{id}', [KatalogRanduController::class, 'updateCart'])->name('katalog-randu.update-cart');
    Route::get('/katalog-randu/check-voucher', [KatalogRanduController::class, 'checkVoucher'])->name('katalog-randu.check-voucher');
    Route::get('/katalog-randu/transaction-data', [TransactionProductController::class, 'data'])->name('katalog-randu.transaction-data');
    Route::get('/katalog-randu/transaction', [TransactionProductController::class, 'index'])->name('katalog-randu.transaction.index');
    Route::post('/katalog-randu/cek-ongkir', [KatalogRanduController::class, 'cekOngkir'])->name('katalog-randu.cek-ongkir');
    Route::get('/category-katalog', [KatalogRanduController::class, 'categoryCatalog'])->name('katalog-randu.categoryCatalog');
    Route::resource('katalog-randu', KatalogRanduController::class);

    // INVOICE
    Route::prefix('invoice')
        ->name('invoice.')
        ->group(function () {
            // CLIENT
            Route::resource('client', ClientController::class);
            Route::get('/client-data', [ClientController::class, 'data'])->name('client_data');

            // INVOICE
            Route::resource('invoice', InvoiceController::class);
            Route::get('/data', [InvoiceController::class, 'data'])->name('invoice_data');
            Route::get('/invoiceFrom', [InvoiceController::class, 'invoiceFrom'])->name('invoiceFrom');
            Route::get('/export/{id}/{termin_id?}', [InvoiceController::class, 'export'])->name('export');
            Route::post('/change-bulk-paid', [InvoiceController::class, 'changeBulkPaid'])->name('changeBulkPaid');
            Route::get('/chart', [InvoiceController::class, 'chart'])->name('chart');
            Route::get('/payment-method', [InvoiceController::class, 'typePayment'])->name('typePayment');
            Route::post('/single_sync', [InvoiceController::class, 'single_sync'])->name('single.sync');
            Route::get('/termin/{id}', [InvoiceController::class, 'todoTermin'])->name('todoTermin');
            Route::post('/termin/{id}', [InvoiceController::class, 'terminStore'])->name('terminStore');
            Route::post('/termin-change-paid/{id}', [InvoiceController::class, 'changePaidTermin'])->name('changePaidTermin');
        });
    Route::get('currency', [InvoiceController::class, 'currency'])->name('currency');
    Route::get('check-exchange/{id}', [InvoiceController::class, 'checkExchange'])->name('checkExchange');

    Route::prefix('/report/visit')->name('visit.')->group(function () {
        // get list of visit for web
        Route::get('/', [LaporanKunjunganController::class, 'index'])->name('index');

        // update status from not approved to approved
        Route::put('/update/{id}', [LaporanKunjunganController::class, 'update'])->name('update');
        Route::get('show/{id}', [LaporanKunjunganController::class, 'show'])->name('show');

        // delete the visit
        Route::delete('/delete/{id}', [LaporanKunjunganController::class, 'destroy'])->name('destroy');

        Route::get('/show-photo', [LaporanKunjunganController::class, 'show_photo'])->name('show_photo');

        // list resistance
        Route::get('/resistance', [LaporanKunjunganController::class, 'resistance_index'])->name('resistance.index');
        Route::get('/resistance-data', [LaporanKunjunganController::class, 'resistance_data'])->name('resistance_data');
    });
    Route::get('/report/visit-data', [LaporanKunjunganController::class, 'data'])->name('visit_data');

    // Transfer Stock
    Route::prefix('transfer-stock')
        ->name('transfer-stock.')
        ->group(function () {
            // PRODUCT
            Route::get('/product-data', [TransferStockProductController::class, 'data'])->name('product_data');
            Route::resource('product', TransferStockProductController::class);

            // PRODUCT
            Route::get('/material-data', [TransferStockMaterialController::class, 'data'])->name('material_data');
            Route::resource('material', TransferStockMaterialController::class);
        });
});
// Storefront Public
Route::get('/storefront/halaman-tidak-ditemukan', [StorefrontController::class, 'page404'])->name('storefront.404');
Route::get('/storefront/getCity/{province}', [StorefrontController::class, 'getCity'])->name('storefront.getCity');
Route::get('/storefront/getSubdistrict/{city}', [StorefrontController::class, 'getSubdistrict'])->name('storefront.getSubdistrict');
Route::post('/storefront/getShippingCost', [StorefrontController::class, 'getShippingCost'])->name('storefront.getShippingCost');
Route::get('/{username}', [StorefrontController::class, 'index'])->name('storefront');
Route::get('/{username}/about', [StorefrontController::class, 'about'])->name('storefront.about');
Route::get('/{username}/categories', [StorefrontController::class, 'categories'])->name('product.categories');
Route::get('/{username}/c/{category}', [StorefrontController::class, 'productCategory'])->name('product.category');
Route::get('/{username}/p/{product}', [StorefrontController::class, 'detailProduct'])->name('product.detail');
Route::get('/{username}/search/{product}', [StorefrontController::class, 'search'])->name('product.search');
Route::get('/{username}/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/{username}/cart/data', [CartController::class, 'data'])->name('cart.data');
Route::post('/{username}/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/{username}/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/{username}/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('/{username}/cart/update-notes', [CartController::class, 'updateNotes'])->name('cart.updateNotes');
Route::post('/{username}/cart/apply-voucher', [CartController::class, 'applyVoucher'])->name('cart.applyVoucher');
Route::post('/{username}/cart/remove-voucher', [CartController::class, 'removeVoucher'])->name('cart.removeVoucher');
Route::get('/{username}/cart/check-voucher', [CartController::class, 'checkVoucher'])->name('cart.checkVoucher');
Route::get('/{username}/order', [OrderController::class, 'create'])->name('order.create');
Route::get('/{username}/order/getQrTable/{branch}/{userid}', [OrderController::class, 'getQrTable'])->name('order.getQrTable');
Route::post('/{username}/order/update-order-type', [OrderController::class, 'updateOrderType'])->name('order.updateOrderType');
Route::post('/{username}/order/update-customer-details', [OrderController::class, 'updateCustomerDetails'])->name('order.updateCustomerDetails');
Route::post('/{username}/order/update-payment-details', [OrderController::class, 'updatePaymentDetails'])->name('order.updatePaymentDetails');
Route::post('/{username}/order/calculate-final', [OrderController::class, 'calculateFinal'])->name('order.calculateFinal');
Route::get('/{username}/order/payment/{order}', [OrderController::class, 'payment'])->name('order.payment');
Route::get('/{username}/order/confirmation/{order}', [OrderController::class, 'confirmation'])->name('order.confirmation');
Route::get('/{username}/order/success/{order}', [OrderController::class, 'success'])->name('order.success');
Route::get('/{username}/checkout/delivery', [CheckoutController::class, 'delivery'])->name('checkout.delivery');
Route::get('/{username}/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/{username}/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/{username}/{qrcode}', [StorefrontController::class, 'reservationByQrCode'])->name('storefront-reservation-qr');

require __DIR__ . '/auth.php';
