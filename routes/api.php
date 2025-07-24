<?php

use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\DistrictController;
use App\Http\Controllers\API\PositionStaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\DuitkuController;
use App\Http\Controllers\FlipController;
use App\Http\Controllers\Main\SettingController;
use App\Http\Controllers\WebsocketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Events\MyEvent;
use App\Http\Controllers\DevController;
use App\Http\Controllers\IMessageController;
use App\Http\Controllers\Main\LaporanAbsensiController;
use App\Http\Controllers\Main\LaporanKunjunganController;
use App\Http\Controllers\Main\LaporanPajakController;
use App\Http\Controllers\Main\LaporanPenjualanAdvanceController;
use App\Http\Controllers\Main\LaporanPenjualanController;
use App\Http\Controllers\Main\LaporanStockController;
use App\Http\Controllers\Main\RekapitulasiHarianController;
use App\Http\Controllers\Main\ReportController;
use App\Http\Controllers\ManajemenPesananController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('dev', [DevController::class, 'index']);
Route::get('websocket/test-send-message', [WebsocketController::class, 'test']);
Route::get('get-branch-lists/{id}', [BranchController::class, 'getBranchLists'])->name('get.branch.lists.api');
Route::get('get-district-lists/{id}', [BranchController::class, 'getDistrictLists'])->name('get.district.lists.api');
Route::get('get-district-lists', [DistrictController::class, 'getDistrictLists'])->name('get.district.lists.api');
Route::post('staff-lists', [StaffController::class, 'getStaffData'])->name('staff.lists.api');
Route::get('get-position-staff', [PositionStaffController::class, 'index']);
Route::post('create-staff', [StaffController::class, 'store'])->name('store.staff.api');
Route::delete('remove-staff/{id}', [StaffController::class, 'destroy'])->name('destroy.staff.api');
Route::post('/callback-duitku', [DuitkuController::class, 'callback']);
Route::post('/callback-duitku-dev', [DuitkuController::class, 'callbackDev']);
Route::post('/callback-flip', [FlipController::class, 'callback']);
Route::post('/callback-flip/inquiry', [FlipController::class, 'callbackInquiry']);
Route::patch('account-update/{id}', [SettingController::class, 'updateAccount'])->name('account.profile.update.api');
Route::post('/imessage/device-connected', [IMessageController::class, 'deviceConnectedInfo']);
Route::post('/imessage/device-logout/{uuid}', [IMessageController::class, 'deviceLogout']);
Route::post('/imessage/device-store', [IMessageController::class, 'deviceStore']);
Route::put('/imessage/device-update/{uuid}', [IMessageController::class, 'deviceUpdate']);
Route::post('/imessage/account-status', [IMessageController::class, 'accountStatus']);
Route::delete('/imessage/device-remove/{uuid}', [IMessageController::class, 'deviceRemove']);
Route::get('/{username}/search', [ProductController::class, 'search'])->name('product.search.api');

Route::middleware(['preview'])->name('preview.')->group(function () {
    Route::get('/manajemen-pesanan/cart', [ManajemenPesananController::class, 'getDataCart'])->name('manajemen-data.cart');
    Route::get('journal_report', [ReportController::class, 'journal_report']);
    Route::post('journal_report_submit', [ReportController::class, 'journal_report_submit']);

    Route::get('general_ledger', [ReportController::class, 'general_ledger']);
    Route::post('general_ledger_submit', [ReportController::class, 'general_ledger_submit']);

    Route::get('trial_balance', [ReportController::class, 'trial_balance']);
    Route::post('trial_balance_submit', [ReportController::class, 'trial_balance_submit']);
    Route::get('trial_balance_export/{tanggal}', [ReportController::class, 'trial_balance_export']);

    Route::get('profit_loss', [ReportController::class, 'profit_loss']);
    Route::post('submit_profit_loss', [ReportController::class, 'submit_profit_loss']);
    Route::get('profit_loss_export/{tanggal}', [ReportController::class, 'profit_loss_export']);

    Route::get('balance', [ReportController::class, 'balance']);
    Route::post('submit_balance_sheet', [ReportController::class, 'submit_balance_sheet']);
    Route::get('balance_sheet_export/{tanggal}', [ReportController::class, 'balance_sheet_export']);


    Route::get('/rekapitulasi-harian', [RekapitulasiHarianController::class, 'index']);
    Route::get('/rekapitulasi-harian-data', [RekapitulasiHarianController::class, 'getData']);
    Route::prefix('laporan')
        ->name('laporan.')
        ->group(function () {
            Route::get('/penjualan/data', [LaporanPenjualanController::class, 'data'])->name('penjualan.data');
            Route::get('/penjualan/export-excel', [LaporanPenjualanController::class, 'exportExcel'])->name('penjualan.exportExcel');
            Route::get('/penjualan/export-pdf', [LaporanPenjualanController::class, 'exportPdf'])->name('penjualan.exportPdf');
            Route::get('/penjualan/chart', [LaporanPenjualanController::class, 'chart'])->name('penjualan.chart');
            Route::get('/penjualan/chart-regular', [LaporanPenjualanController::class, 'chartRegular'])->name('penjualan.chart-regular');
            Route::get('/penjualan/chart-basic', [LaporanPenjualanController::class, 'chartBasic'])->name('penjualan.chart.basic');
            Route::get('/penjualan/chart-expenses', [LaporanPenjualanController::class, 'chartExpenses'])->name('penjualan.chart.expenses');
            Route::get('/penjualan/chart-sales', [LaporanPenjualanController::class, 'chartSales'])->name('penjualan.chart.sales');
            Route::get('/penjualan/category-expense', [LaporanPenjualanController::class, 'categoryExpense'])->name('penjualan.categoryExpense');
            Route::get('/penjualan', [LaporanPenjualanController::class, 'index'])->name('penjualan.index');

            Route::get('/penjualan-advance', [LaporanPenjualanAdvanceController::class, 'index'])->name('penjualan.advance.index');
            Route::get('/penjualan/export-excel-advance', [LaporanPenjualanAdvanceController::class, 'exportExcel'])->name('penjualan.exportExcel.advance');
            Route::get('/penjualan/export-pdf-advance', [LaporanPenjualanAdvanceController::class, 'exportPdf'])->name('penjualan.exportPdf.advance');

            Route::get('/pajak/data', [LaporanPajakController::class, 'data'])->name('pajak.data');
            Route::get('/pajak/export', [LaporanPajakController::class, 'export'])->name('pajak.export');
            Route::get('/pajak/chart', [LaporanPajakController::class, 'chart'])->name('pajak.chart');
            Route::get('/pajak', [LaporanPajakController::class, 'index'])->name('pajak.index');

            Route::get('/absensi', [LaporanAbsensiController::class, 'index'])->name('absensi.index');
            Route::get('/absensi/data', [LaporanAbsensiController::class, 'data'])->name('absensi.data');
            Route::get('/absensi/export', [LaporanAbsensiController::class, 'export'])->name('absensi.export');

            Route::get('/stock/data', [LaporanStockController::class, 'data'])->name('stock.data');
            Route::get('/stock/export', [LaporanStockController::class, 'export'])->name('stock.export');
            Route::get('/stock', [LaporanStockController::class, 'index'])->name('stock.index');
            Route::get('/stock/sumDataBarangJadi', [LaporanStockController::class, 'getDataBarangJadi'])->name('stock.sumDataBarangJadi');
            Route::get('/stock/sumDataManufaktur', [LaporanStockController::class, 'getDataManufaktur'])->name('stock.sumDataManufaktur');
            Route::get('/stock/sumDataSetBarangJadi', [LaporanStockController::class, 'getDataSetBarangJadi'])->name('stock.sumDataSetBarangJadi');
            Route::get('/stock/sumDataMaterial', [LaporanStockController::class, 'getDataMaterial'])->name('stock.sumDataMaterial');
            Route::post('/sync-stock', [LaporanStockController::class, 'syncStock'])->name('stock.syncStock');
        });

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
});


// VERSIONING API
// Route::middleware('check.api.version')->prefix('v1')->group(base_path('routes/api/v1.php'));
Route::middleware('checkApiVersion')->prefix('v1')->group(function () {
    require base_path('routes/api/v1.php');
});

Route::prefix('v2')->group(base_path('routes/api/v2.php'));

Route::get('test-send-mail', [DevController::class, 'testMail']);

Route::get('calc-wallet-logs', [DevController::class, 'calcWalletLogs']);
// Route::get('test-send-message', [DevController::class, 'testSendMessage']);
