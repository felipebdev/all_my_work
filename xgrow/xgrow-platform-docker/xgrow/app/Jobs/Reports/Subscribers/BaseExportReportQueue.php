<?php

namespace App\Jobs\Reports\Subscribers;

use App\Downloads;
use App\Jobs\Reports\Subscribers\Models\BaseReport;
use App\Services\Objects\SubscriberReportFilter;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class BaseExportReportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $platform_id;
    protected $filename;
    protected $platform_user_id;

    protected $saveDirectory = 'xgrow_reports';
    protected $diskStorage; //linode //public_local
    protected $chunkSize = 10000;
    protected $report;
    protected $filters;

    //Filters
    protected $searchFilter;
    protected $plansFilter;
    protected $subscriberStatusFilter;
    protected $createdPeriodFilter;
    protected $lastAccessedPeriodFilter;
    protected $neverAccessedFilter;

    public function __construct($user, BaseReport $report, SubscriberReportFilter $filters)
    {
        $this->platform_id = $user->platform_id;
        $this->platform_user_id = $user->id;
        $this->diskStorage = env('STORAGE_DIR', 'images');

        $this->searchFilter = $filters->search;
        $this->plansFilter = $filters->plans;
        $this->subscriberStatusFilter = $filters->subscriberStatus;
        $this->createdPeriodFilter = $filters->createdPeriod;
        $this->lastAccessedPeriodFilter = $filters->lastAccessedPeriod;
        $this->neverAccessedFilter = $filters->neverAccessed;
        $this->report = $report;
        $this->filters = $filters;
    }

    abstract protected function filename();

    abstract protected function process(string $path);

    public function handle()
    {
        try {
            Log::info('Iniciando exportacao!', ['date' => date('d/m/Y H:i:s')]);
            ini_set('max_execution_time', 600);
            $downloadId = null;
            $filename = $this->filename();
            $file = tmpfile();
            $path = stream_get_meta_data($file)['uri'];
            $download = Downloads::create([
                'status' => 'pending',
                'period' => $this->createdPeriodFilter ? dateBr($this->createdPeriodFilter->startDate).' - '.dateBr($this->createdPeriodFilter->endDate) : ' - ',
                'filters' => $this->subscriberStatusFilter ? implode('; ', $this->subscriberStatusFilter) : ' - ',
                'filename' => $filename ?? ' - ',
                'platform_id' => $this->platform_id,
                'platforms_users_id' => $this->platform_user_id,
            ]);
            $downloadId = $download->id;
            Log::info('Gravando tabela de download!', ['download' => $download, 'date' => date('d/m/Y H:i:s')]);
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
