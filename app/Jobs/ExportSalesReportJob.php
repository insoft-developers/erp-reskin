<?php

namespace App\Jobs;

use App\Exports\LaporanPenjualanExport;
use App\Http\Controllers\Main\LaporanPenjualanAdvanceController;
use App\Http\Controllers\ManajemenPesananController;
use App\Mail\SalesReportExportMail;
use App\Models\JobsValidation;
use App\Models\MlAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ExportSalesReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $exportType;
    protected $filters;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 900;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $exportType, $filters)
    {
        $this->userId = $userId;
        $this->exportType = $exportType; // 'excel' or 'pdf'
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting export job for user: {$this->userId}, type: {$this->exportType}");

            // Get user information
            $user = MlAccount::find($this->userId);
            if (!$user || !$user->email) {
                Log::error("User not found or email not available for user ID: {$this->userId}");
                return;
            }

            // Create controller instance
            $controller = new LaporanPenjualanAdvanceController();

            // Create request object with filters
            $request = new Request($this->filters);

            // Generate date name for filename
            $date = $this->filters['date'] ?? 'isThisMonth';
            $dateName = $this->getDateName($date, $this->filters);

            // Get data
            $data = $controller->getData($request);
            Log::debug('pause for 20 seconds before fetching chart data');
            sleep(20); // Simulate delay for testing purposes
            Log::debug('Fetching chart data');
            $cart = $controller->chart($request);

            // Get additional data from ManajemenPesananController
            $newRequest = new Request([
                'selected_range' => $this->filters['date'] ?? 'isThisMonth',
                'startDate' => $this->filters['start_date'] ?? null,
                'endDate' => $this->filters['end_date'] ?? null,
                'price_type' => $this->filters['price_type'] ?? null,
                'user_id' => $this->userId,
            ]);

            $manajemenPesanan = new ManajemenPesananController();
            Log::debug('pause for 20 seconds before fetching cart data');
            sleep(20); // Simulate delay for testing purposes
            $penjualan = $manajemenPesanan->getDataCart($newRequest);
            sleep(10);
            $responseData = json_decode($penjualan->getContent(), true);

            $cart['omset_penjualan'] = $responseData['data']['omset_penjualan'];
            $cart['total_ongkir'] = $responseData['data']['total_ongkir'];
            $cart['total_diskon'] = $responseData['data']['total_diskon'];

            // Generate file
            $fileName = "Laporan Penjualan {$dateName}";
            $tempPath = storage_path('app/temp/');

            // Create temp directory if it doesn't exist
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            if ($this->exportType === 'excel') {
                $fileName .= '.xlsx';
                $filePath = $tempPath . $fileName;

                Excel::store(new LaporanPenjualanExport($data, $cart), 'temp/' . $fileName, 'local');
                $filePath = storage_path('app/temp/' . $fileName);
            } else { // PDF
                $fileName .= '.pdf';
                $filePath = $tempPath . $fileName;

                $pdf = PDF::loadView('main.report.sales.exportPdf', [
                    'data' => $data,
                    'cart' => $cart,
                    'dateName' => $dateName
                ]);

                $pdf->save($filePath);
            }

            $userEmail = $user->email;
            Mail::to($userEmail)->send(new SalesReportExportMail(
                $filePath,
                $fileName,
                $dateName,
                $this->exportType
            ));

            $secondEmail = 'randu_testmail@mailnesia.com';
            Mail::to($secondEmail)->send(new SalesReportExportMail(
                $filePath,
                $fileName,
                $dateName,
                $this->exportType
            ));

            Log::info("Export completed and email sent to: {$userEmail}");

            // Clean up temporary file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            JobsValidation::where('user_id', $this->userId)
                ->where('job_title', 'Export Sales Report')
                ->delete();
            // Log::info("Export job completed successfully for user: {$this->userId}, type: {$this->exportType}");
        } catch (\Exception $e) {

            JobsValidation::where('user_id', $this->userId)
                ->where('job_title', 'Export Sales Report')
                ->delete();
            Log::error("Export job failed for user {$this->userId}: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get formatted date name for the report
     */
    private function getDateName($date, $filters)
    {
        switch ($date) {
            case 'isToday':
                return "Hari ini";
            case 'isYesterday':
                return "Kemarin";
            case 'isThisMonth':
                return "Bulan ini";
            case 'isLastMonth':
                return "Bulan Kemarin";
            case 'isThisYear':
                return "Tahun ini";
            case 'isLastYear':
                return "Tahun Kemarin";
            case 'isRangeDate':
                return ($filters['start_date'] ?? '') . ' - ' . ($filters['end_date'] ?? '');
            default:
                return "Periode Tertentu";
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Export job failed permanently for user {$this->userId}: " . $exception->getMessage());

        // Optionally send an error notification email to the user
        try {
            $user = MlAccount::find($this->userId);
            if ($user && $user->email) {
                // You can create a separate mail class for error notifications
                // Mail::to($user->email)->send(new ExportFailedMail($this->exportType));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send error notification: " . $e->getMessage());
        }
    }
}
