<?php

namespace App\Jobs\Reports\Producers;

use App\Downloads;
use App\Services\Objects\ProducerReportFilter;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\WriterInterface;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

abstract class BaseExportReportQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $diskName;
    protected string $saveDirectory = 'xgrow_reports';
    protected int $chunkSize = 10000;

    protected $platform_id;
    protected $platform_user_id;
    protected ProducerReportFilter $filters;
    private string $platformId;

    public function __construct(string $platformId, int $userId, ProducerReportFilter $filters)
    {
        $this->diskName = env('STORAGE_DIR', 'images');
        $this->platform_id = $platformId;
        $this->platform_user_id = $userId;
        $this->filters = $filters;
        $this->platformId = $platformId;
    }

    abstract protected function filename();

    public function disk(): FilesystemAdapter
    {
        return Storage::disk($this->diskName);
    }

    public function handle()
    {
        $downloadId = null;
        try {
            ini_set('max_execution_time', 600);

            $file = tmpfile();
            $path = stream_get_meta_data($file)['uri'];

            $downloadId = $this->createPendingDownload();

            $this->process($path);

            $this->disk()->putFileAs($this->saveDirectory, $path, $this->filename());

            $this->markDownloadCompleted($downloadId);
        } catch (Exception $exception) {
            $this->failedJob($downloadId, $exception);
        }
    }

    abstract protected function getWriter(): WriterInterface;

    protected function process(string $path)
    {
        $writer = $this->getWriter();
        $writer->openToFile($path);

        /* CREATE HEADER */
        $header = $this->report()->header();
        $writer->addRow(WriterEntityFactory::createRowFromArray($header));

        $query = $this->report()->query($this->platform_id, $this->filters);
        $normalizedRows = $this->report()->rows();
        $query->chunk($this->chunkSize, function ($rows) use ($writer, $normalizedRows) {
            foreach ($rows as $row) {
                $data = [];
                foreach ($normalizedRows as $key => $value) {
                    $data[] = (is_callable($value) ? $value($row->$key, $row) : $row->$value) ?? '-';
                }
                $writer->addRow(WriterEntityFactory::createRowFromArray($data));
            }
        });
        $writer->close();
    }

    protected function createPendingDownload(): ?int
    {
        $download = Downloads::create([
            'status' => 'pending',
            // 'period' => $this->filter ? dateBr($this->createdPeriodFilter->startDate).' - '.dateBr($this->createdPeriodFilter->endDate) : ' - ',
            //'filters' => $this->plansFilter ? implode('; ', $this->plansFilter) : ' - ',
            'filename' => $this->filename() ?? ' - ',
            'platform_id' => $this->platform_id,
            'platforms_users_id' => $this->platform_user_id,
        ]);

        $downloadId = $download->id;

        return $downloadId;
    }

    protected function markDownloadCompleted($downloadId)
    {
        Downloads::where('id', $downloadId)->update([
            'status' => 'completed',
            'filesize' => $this->disk()->size("{$this->saveDirectory}/{$this->filename()}"),
            'url' => $this->disk()->url("{$this->saveDirectory}/{$this->filename()}"),
        ]);
    }

    private function failedJob($downloadId, Exception $exception)
    {
        if (!empty($downloadId)) {
            Downloads::where('id', $downloadId)->update([
                'status' => 'failed',
            ]);
        }

        throw $exception;
    }
}
