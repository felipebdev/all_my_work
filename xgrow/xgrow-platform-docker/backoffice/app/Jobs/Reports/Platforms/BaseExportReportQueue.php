<?php

namespace App\Jobs\Reports\Platforms;

use App\DownloadsBackoffice;
use App\Jobs\Reports\Platforms\Models\BaseReport;
use App\Services\Objects\PlatformFilter;
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

    protected $filename;
    protected $user_id;

    protected $saveDirectory = 'xgrow_reports';
    protected $diskStorage; //linode //public_local
    protected $chunkSize = 10000;
    protected $report;
    protected $filters;

    //Filters
    protected $createdPeriodFilter;

    public function __construct($user, BaseReport $report, PlatformFilter $filters)
    {
        $this->user_id = $user->id;
        $this->diskStorage = env('STORAGE_DIR', 'images');
        $this->createdPeriodFilter = $filters->createdPeriod;
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
            $path = stream_get_meta_data($file)[ 'uri'];
            $download = DownloadsBackoffice::create([
                'status' => 'pending',
                'period' => $this->createdPeriodFilter ? dateBr($this->createdPeriodFilter->startDate).' - '.dateBr($this->createdPeriodFilter->endDate) : ' - ',
                'filters' => $this->filters->getData(),
                'filename' => $filename ?? ' - ',
                'user_id' => $this->user_id,
            ]);
            $downloadId = $download->id;


            $this->process($path);

            Storage::disk($this->diskStorage)->putFileAs($this->saveDirectory, $path, $filename);
            DownloadsBackoffice::where('id', $download->id)->update([
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
            DownloadsBackoffice::where('id', $downloadId)->update([
                'status' => 'failed',
            ]);
        }

        throw $exception;

    }
}
