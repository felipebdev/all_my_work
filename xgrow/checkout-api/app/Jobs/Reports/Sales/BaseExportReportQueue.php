<?php

namespace App\Jobs\Reports\Sales;

use App\Downloads;
use App\Jobs\Reports\Sales\Models\BaseReport;
use App\Services\Objects\SaleReportFilter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
abstract class BaseExportReportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $platform_id;
    protected $filename;
    protected $platform_user_id;

    protected $saveDirectory = 'xgrow_reports';
    protected $diskStorage;
    protected $chunkSize = 10000;
    protected $report;
    protected $filters;

    //Filters
    protected $plansFilter;
    protected $typePaymentFilter;
    protected $statusPaymentFilter;
    protected $statusPeriodFilter;
    protected $searchTermFilter;

    public function __construct($user, BaseReport $report, SaleReportFilter $filters)
    {
        $this->platform_id = $user->platform_id;
        $this->platform_user_id = $user->id;
        $this->diskStorage = env('STORAGE_DIR', 'linode');

        $this->plansFilter = $filters->plans;
        $this->typePaymentFilter = $filters->paymentType;
        $this->statusPaymentFilter = $filters->paymentStatus;
        $this->statusPeriodFilter = $filters->paymentPeriod;
        $this->searchTermFilter = $filters->search;
        $this->report = $report;
        $this->filters = $filters;
    }

    abstract protected function filename();
    abstract protected function process(string $path);

    public function handle()
    {
        try {
            ini_set('max_execution_time', 600);
            $downloadId = null;
            $filename = $this->filename();
            $file = tmpfile();
            $path = stream_get_meta_data($file)['uri'];
            $download = Downloads::create([
                'status' => 'pending',
                'period' => $this->statusPeriodFilter ? dateBr($this->statusPeriodFilter->startDate).' - '.dateBr($this->statusPeriodFilter->endDate) : ' - ',
                'filters' => $this->typePaymentFilter ? implode('; ', $this->typePaymentFilter) : ' - ',
                'filename' => $filename ?? ' - ',
                'platform_id' => $this->platform_id,
                'platforms_users_id' => $this->platform_user_id,
            ]);
            $downloadId = $download->id;

            $this->process($path);

            Storage::disk($this->diskStorage)->putFileAs($this->saveDirectory, $path, $filename);
            Downloads::where('id', $download->id)->update([
                'status' => 'completed',
                'filesize' => Storage::disk($this->diskStorage)->size("$this->saveDirectory/" . $filename),
                'url' => Storage::disk($this->diskStorage)->url("$this->saveDirectory/" . $filename),
            ]);
        }
        catch(Exception $exception) {
            $this->failedJob($downloadId, $exception);
        }
    }

    private function failedJob($downloadId, Exception $exception) {
        if (!empty($downloadId)) {
            Downloads::where('id', $downloadId)->update([
                'status' => 'failed',
            ]);
        }

        throw $exception;
    }
}
