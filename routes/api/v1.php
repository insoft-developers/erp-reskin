<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PosController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserActivityLogController;
use App\Http\Controllers\Core\AdjustmentCategoryController;
use App\Http\Controllers\Core\AdjustmentController;
use App\Http\Controllers\Core\ExpenseCategoryController;
use App\Http\Controllers\Core\ExpenseController;
use App\Http\Controllers\Core\InterProductController;
use App\Http\Controllers\Core\InterPurchaseController;
use App\Http\Controllers\Core\ProductAPIController;
use App\Http\Controllers\Core\ProductCategoryAPIController;
use App\Http\Controllers\Core\MaterialController;
use App\Http\Controllers\Core\MaterialPurchaseController;
use App\Http\Controllers\Core\ProductManufactureController;
use App\Http\Controllers\Core\ProductPurchaseController;
use App\Http\Controllers\Core\SettingController;
use App\Http\Controllers\Journal\BlockingController as JournalBlockingController;
use App\Http\Controllers\Journal\HutangController;
use App\Http\Controllers\Journal\JournalController;
use App\Http\Controllers\Journal\LoginController;
use App\Http\Controllers\Journal\MobileSettingController;
use App\Http\Controllers\Journal\PengaturanController;
use App\Http\Controllers\Journal\PenyusutanController;
use App\Http\Controllers\Journal\PiutangController;
use App\Http\Controllers\Journal\ReportController;
use App\Http\Controllers\Journal\ExcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::get('check-version-mobile', [AuthController::class, 'checkVersionMobile']);
Route::post('own-register', [AuthController::class, 'ownRegister']);
Route::post('clone-branch', [AuthController::class, 'cloneBranch']);

Route::group(['prefix' => 'journal'], function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('lupa-password', [LoginController::class, 'lupaPassword']);
    Route::post('journal-upload', [JournalController::class, 'journalUpload']);
});

Route::group(['prefix' => 'core'], function () {
    Route::post('product-category-upload', [ProductCategoryAPIController::class, 'productCategoryUpload']);
    Route::post('product-image-upload', [ProductAPIController::class, 'productImageUpload']);
    Route::post('product-purchase-upload', [ProductPurchaseController::class, 'upload']);
    Route::post('material-purchase-upload', [MaterialPurchaseController::class, 'upload']);
    Route::post('company-setting-upload', [SettingController::class, 'upload']);
});

Route::middleware('api')->group(function () {
    Route::middleware('auth:api')->group(function () {

        Route::get('user', [AuthController::class, 'user']);
        Route::post('user-log-activity', [UserActivityLogController::class, 'store']);
        Route::get('get-users-for-owner', [AuthController::class, 'getUsers']);
        Route::get('product', [PosController::class, 'product']);
        Route::post('product', [ProductController::class, 'store']);
        Route::put('product/{id}', [ProductController::class, 'update']);
        Route::post('product-categories', [ProductController::class, 'storeCategory']);
        Route::put('product-categories/{id}', [ProductController::class, 'updateCategory']);
        Route::get('product-categories', [PosController::class, 'productCategory']);
        Route::get('tables', [PosController::class, 'tables']);

        Route::get('check-status-cashier', [PosController::class, 'checkStatusCashier']);
        Route::post('open-cashier', [PosController::class, 'openCashier']);
        Route::post('close-cashier', [PosController::class, 'closeCashier']);

        Route::get('discount', [PosController::class, 'discount']);
        Route::get('check-apply-discount', [PosController::class, 'checkApplyDiscount']);

        Route::get('type-payment', [PosController::class, 'typePayment']);
        Route::post('payment', [PosController::class, 'payment']);
        Route::get('payment-check', [PosController::class, 'paymentCheck']);
        Route::post('payment-validation', [PosController::class, 'paymentValidation']);

        Route::get('customer', [PosController::class, 'customerList']);
        Route::post('customer', [PosController::class, 'customerCreate']);

        Route::get('manage-pesanan', [PosController::class, 'managePesanan']);
        Route::put('update-status-pesanan/{id}', [PosController::class, 'updateStatusPenjualan']);
        Route::put('update-status-pembayaran/{id}', [PosController::class, 'updateStatusPembayaran']);

        Route::get('rekapitulasi-harian', [PosController::class, 'rekapitulasiHarian']);

        Route::get('pengeluaran', [PosController::class, 'listPengeluaran']);
        Route::post('pengeluaran', [PosController::class, 'createpengeluaran']);

        Route::get('qr-code-meja', [PosController::class, 'qrCodeMeja']);
        Route::put('update-statusqr-code-meja', [PosController::class, 'updateStatusQrCodeMeja']);

        Route::get('penjualan-detail', [PosController::class, 'detailPenjualan']);

        Route::group(['prefix' => 'journal'], function () {
            Route::post('branch-name', [MobileSettingController::class, 'branchName']);
            Route::post('list', [JournalController::class, 'list']);
            Route::get('transaction-type', [JournalController::class, 'transactionType']);
            Route::post('account-receive', [JournalController::class, 'getAccountReceive']);
            Route::post('save-quick-journal', [JournalController::class, 'saveQuickJournal']);
            Route::post('get-account-select', [JournalController::class, 'getAccountSelect']);
            Route::post('save-multiple-journal', [JournalController::class, 'saveMultipleJournal']);
            Route::post('delete-journal', [JournalController::class, 'journalDelete']);
            Route::post('journal-edit', [JournalController::class, 'journalEdit']);
            Route::post('update-multiple-journal', [JournalController::class, 'journalUpdate']);
            Route::post('journal-preview', [JournalController::class, 'journalPreview']);

            Route::post('journal-report', [ReportController::class, 'journalReport']);
            Route::post('account-by-user', [ReportController::class, 'AccountByUser']);
            Route::post('general-ledger', [ReportController::class, 'generalLedger']);
            Route::post('trial-balance', [ReportController::class, 'trialBalance']);
            Route::post('profit-loss', [ReportController::class, 'profitLoss']);
            Route::post('balance-sheet', [ReportController::class, 'balanceSheet']);


            Route::post('debt-list', [HutangController::class, 'debtList']);
            Route::post('debt-history', [HutangController::class, 'debtHistory']);
            Route::post('debt-sub-type', [HutangController::class, 'debtSubType']);
            Route::post('debt-from', [HutangController::class, 'debtFrom']);
            Route::post('debt-to', [HutangController::class, 'debtTo']);
            Route::post('debt-store', [HutangController::class, 'store']);
            Route::post('debt-payment', [HutangController::class, 'debtPayment']);
            Route::post('debt-destroy', [HutangController::class, 'debtDestroy']);
            Route::post('debt-sync', [HutangController::class, 'debtSync']);
            Route::post('payment-sync', [HutangController::class, 'paymentSync']);


            Route::post('piutang-list', [PiutangController::class, 'piutangList']);
            Route::post('piutang-history', [PiutangController::class, 'piutangHistory']);
            Route::post('piutang-sub-type', [PiutangController::class, 'piutangSubType']);
            Route::post('piutang-from', [PiutangController::class, 'piutangFrom']);
            Route::post('piutang-to', [PiutangController::class, 'piutangTo']);
            Route::post('piutang-store', [PiutangController::class, 'store']);
            Route::post('piutang-payment', [PiutangController::class, 'piutangPayment']);
            Route::post('piutang-destroy', [PiutangController::class, 'piutangDestroy']);
            Route::post('piutang-sync', [PiutangController::class, 'piutangSync']);
            Route::post('piutang-payment-sync', [PiutangController::class, 'piutangPaymentSync']);
            Route::post('piutang-bayar-ke', [PiutangController::class, 'piutangBayarKe']);


            Route::post('penyusutan-list', [PenyusutanController::class, 'list']);
            Route::post('akun-biaya-penyusutan', [PenyusutanController::class, 'akunBiayaPenyusutan']);
            Route::post('kategori-penyusutan', [PenyusutanController::class, 'kategoriPenyusutan']);
            Route::post('akun-akumulasi-penyusutan', [PenyusutanController::class, 'akunAkumulasiPenyusutan']);
            Route::post('penyusutan-store', [PenyusutanController::class, 'store']);
            Route::post('penyusutan-delete', [PenyusutanController::class, 'destroy']);
            Route::post('penyusutan-simulate', [PenyusutanController::class, 'simulate']);
            Route::post('penyusutan-sync', [PenyusutanController::class, 'syncProcess']);


            Route::post('pengaturan-modal-awal', [PengaturanController::class, 'pengaturanModalAwal']);
            Route::post('modal-awal-save', [PengaturanController::class, 'modalAwalSave']);
            Route::post('modal-awal-check', [PengaturanController::class, 'modalAwalCheck']);
            Route::post('pengaturan-rekening-detail', [PengaturanController::class, 'pengaturanRekeningDetail']);
            Route::post('pengaturan-rekening-save', [PengaturanController::class, 'pengaturanRekeningSave']);
            Route::post('generate-opening-balance', [PengaturanController::class, 'generateOpeningBalance']);
            Route::post('initial-delete', [PengaturanController::class, 'initialDelete']);
            Route::post('kode-rekening-delete', [PengaturanController::class, 'kodeRekeningDelete']);

            Route::post('journal-report-export', [ExcelController::class, 'journal_report_export']);
            Route::post('journal-report-pdf', [ExcelController::class, 'journal_report_pdf']);
            Route::post('general-ledger-export', [ExcelController::class, 'general_ledger_export']);
            Route::post('general-ledger-pdf', [ExcelController::class, 'general_ledger_pdf']);

            Route::post('trial-balance-export', [ExcelController::class, 'trial_balance_export']);
            Route::post('trial-balance-pdf', [ExcelController::class, 'trial_balance_pdf']);

            Route::post('profit-loss-export', [ExcelController::class, 'profit_loss_export']);
            Route::post('profit-loss-pdf', [ExcelController::class, 'profit_loss_pdf']);

            Route::post('balance-sheet-export', [ExcelController::class, 'balance_sheet_export']);
            Route::post('balance-sheet-pdf', [ExcelController::class, 'balance_sheet_pdf']);
            Route::post('hutang-current-asset', [HutangController::class, 'hutang_current_asset']);
            Route::post('check-omset', [JournalBlockingController::class, 'check_omset']);
        });


        Route::group(['prefix' => 'core'], function () {
            Route::post('product-category-list', [ProductCategoryAPIController::class, 'productCategoryList']);
            Route::post('product-category-store', [ProductCategoryAPIController::class, 'productCategoryStore']);
            Route::post('product-category-delete', [ProductCategoryAPIController::class, 'productCategoryDelete']);
            Route::post('product-category-update', [ProductCategoryAPIController::class, 'productCategoryUpdate']);

            Route::post('product-list', [ProductAPIController::class, 'productList']);
            Route::post('product-detail', [ProductAPIController::class, 'productDetail']);
            Route::post('product-unit', [ProductAPIController::class, 'productUnit']);
            Route::post('product-composition', [ProductAPIController::class, 'productComposition']);
            Route::post('product-store', [ProductAPIController::class, 'productStore']);
            Route::post('product-varian-store', [ProductAPIController::class, 'productVarianStore']);
            Route::post('product-composition-store', [ProductAPIController::class, 'productCompositionStore']);
            Route::post('product-update', [ProductAPIController::class, 'productUpdate']);
            Route::post('product-delete', [ProductAPIController::class, 'productDelete']);

            Route::post('material-list', [MaterialController::class, 'materialList']);
            Route::post('material-unit', [MaterialController::class, 'materialUnit']);
            Route::post('material-category', [MaterialController::class, 'materialCategory']);
            Route::post('material-store', [MaterialController::class, 'store']);
            Route::post('material-update', [MaterialController::class, 'update']);
            Route::post('material-delete', [MaterialController::class, 'destroy']);
            Route::post('material-supplier', [MaterialController::class, 'supplier']);

            Route::post('inter-product-list', [InterProductController::class, 'list']);
            Route::post('inter-product-category', [InterProductController::class, 'category']);
            Route::post('inter-product-unit', [InterProductController::class, 'unit']);
            Route::post('inter-product-material', [InterProductController::class, 'material']);
            Route::post('inter-product-store', [InterProductController::class, 'store']);
            Route::post('inter-product-update', [InterProductController::class, 'update']);
            Route::post('inter-product-delete', [InterProductController::class, 'destroy']);

            Route::post('product-purchase-list', [ProductPurchaseController::class, 'list']);
            Route::post('product-purchase-type', [ProductPurchaseController::class, 'type']);
            Route::post('product-purchase-product', [ProductPurchaseController::class, 'product']);
            Route::post('product-purchase-store', [ProductPurchaseController::class, 'store']);
            Route::post('product-purchase-delete', [ProductPurchaseController::class, 'destroy']);
            Route::post('product-purchase-sync', [ProductPurchaseController::class, 'sync']);
            Route::post('product-purchase-supplier', [ProductPurchaseController::class, 'supplier']);

            Route::post('product-manufacture-list', [ProductManufactureController::class, 'list']);
            Route::post('product-manufacture-product', [ProductManufactureController::class, 'product']);
            Route::post('product-manufacture-account', [ProductManufactureController::class, 'account']);
            Route::post('product-manufacture-change', [ProductManufactureController::class, 'change']);
            Route::post('product-manufacture-store', [ProductManufactureController::class, 'store']);
            Route::post('product-manufacture-sync', [ProductManufactureController::class, 'sync']);
            Route::post('product-manufacture-delete', [ProductManufactureController::class, 'destroy']);


            Route::post('inter-purchase-list', [InterPurchaseController::class, 'list']);
            Route::post('inter-purchase-product', [InterPurchaseController::class, 'product']);
            Route::post('inter-purchase-account', [InterPurchaseController::class, 'account']);
            Route::post('inter-purchase-change', [InterPurchaseController::class, 'change']);
            Route::post('inter-purchase-store', [InterPurchaseController::class, 'store']);
            Route::post('inter-purchase-sync', [InterPurchaseController::class, 'sync']);
            Route::post('inter-purchase-delete', [InterPurchaseController::class, 'destroy']);

            Route::post('material-purchase-list', [MaterialPurchaseController::class, 'list']);
            Route::post('material-purchase-type', [MaterialPurchaseController::class, 'type']);
            Route::post('material-purchase-product', [MaterialPurchaseController::class, 'product']);
            Route::post('material-purchase-store', [MaterialPurchaseController::class, 'store']);
            Route::post('material-purchase-delete', [MaterialPurchaseController::class, 'destroy']);
            Route::post('material-purchase-sync', [MaterialPurchaseController::class, 'sync']);


            Route::post('adjustment-category-list', [AdjustmentCategoryController::class, 'list']);
            Route::post('adjustment-category-store', [AdjustmentCategoryController::class, 'store']);
            Route::post('adjustment-category-update', [AdjustmentCategoryController::class, 'update']);
            Route::post('adjustment-category-delete', [AdjustmentCategoryController::class, 'destroy']);


            Route::post('adjustment-list', [AdjustmentController::class, 'list']);
            Route::post('adjustment-account', [AdjustmentController::class, 'account']);
            Route::post('adjustment-product', [AdjustmentController::class, 'product']);
            Route::post('adjustment-inter', [AdjustmentController::class, 'inter']);
            Route::post('adjustment-material', [AdjustmentController::class, 'material']);
            Route::post('adjustment-product-store', [AdjustmentController::class, 'product_store']);
            Route::post('adjustment-inter-store', [AdjustmentController::class, 'inter_store']);
            Route::post('adjustment-material-store', [AdjustmentController::class, 'material_store']);
            Route::post('adjustment-sync', [AdjustmentController::class, 'sync']);
            Route::post('adjustment-delete', [AdjustmentController::class, 'destroy']);

            Route::post('expense-category-list', [ExpenseCategoryController::class, 'list']);
            Route::post('expense-category-store', [ExpenseCategoryController::class, 'store']);
            Route::post('expense-category-product', [ExpenseCategoryController::class, 'product']);
            Route::post('expense-category-update', [ExpenseCategoryController::class, 'update']);
            Route::post('expense-category-delete', [ExpenseCategoryController::class, 'destroy']);

            Route::post('expense-list', [ExpenseController::class, 'list']);
            Route::post('expense-account-from', [ExpenseController::class, 'account_from']);
            Route::post('expense-account-to', [ExpenseController::class, 'account_to']);
            Route::post('expense-store', [ExpenseController::class, 'store']);
            Route::post('expense-sync', [ExpenseController::class, 'sync']);
            Route::post('expense-delete', [ExpenseController::class, 'destroy']);

            Route::post('company-setting', [SettingController::class, 'company_setting']);
            Route::post('company-city', [SettingController::class, 'city']);
            Route::post('company-district', [SettingController::class, 'district']);
            Route::post('company-setting-update', [SettingController::class, 'company_setting_update']);

            Route::post('petty-cash', [SettingController::class, 'petty_cash']);
            Route::post('petty-cash-update', [SettingController::class, 'petycash_update']);

            Route::post('payment-setting', [SettingController::class, 'payment_setting']);
            Route::post('payment-setting-update', [SettingController::class, 'payment_setting_update']);

            Route::post('printer-setting', [SettingController::class, 'printer_setting']);
            Route::post('printer-setting-update', [SettingController::class, 'printer_setting_update']);

            
           

        });


        Route::group(['prefix' => 'attendance'], function () {
            Route::get('/', [AttendanceController::class, 'list']);
            Route::post('/', [AttendanceController::class, 'store']);
        });

        Route::group(['prefix' => 'visit-record'], function () {
            Route::get('/', [AttendanceController::class, 'getDataVisitRecord']);
            Route::post('/', [AttendanceController::class, 'storeDataVisitRecord']);
            Route::get('/note/{id}', [AttendanceController::class, 'showNoteVisitRecord']);
            Route::post('/note/{id}', [AttendanceController::class, 'storeNoteVisitRecord']);
            Route::delete('/destroy/{id}', [AttendanceController::class, 'destroyVisitRecord']);
        });

        Route::post('/send-notification-fcm', [NotificationController::class, 'sendPushNotification']);
    });
});
