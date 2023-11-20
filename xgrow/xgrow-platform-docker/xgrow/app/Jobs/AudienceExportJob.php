<?php

namespace App\Jobs;

use App\Downloads;
use App\Exports\AudienceExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class AudienceExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $platformId;
    protected $conditions;
    protected $type;

    private $storage;
    private $download;

    public function __construct(
        $userId,
        string $platformId,
        array $conditions,
        string $type
    ) {
        $this->userId = $userId;
        $this->platformId = $platformId;
        $this->conditions = $conditions;
        $this->type = $type;
        $this->storage = env('STORAGE_DIR', 'local');
    }

    public function handle()
    {
        switch ($this->type) {
            case 'csv':
                $extension = 'csv';
                $writerType = ExcelType::CSV;
                break;
            default:
                $extension = 'xlsx';
                $writerType = ExcelType::XLSX;
        }

        $fullpath = $this->generateName('audience', $extension);

        $this->download = Downloads::create([
            'status' => 'pending',
            'period' => '',
            'filters' => '',
            'filename' => basename($fullpath),
            'platform_id' => $this->platformId,
            'platforms_users_id' => $this->userId,
        ]);

        Excel::store(new AudienceExport($this->platformId, $this->conditions), $fullpath, $this->storage, $writerType);

        Downloads::where('id', $this->download->id)->update([
            'status' => 'completed',
            'filesize' => Storage::disk($this->storage)->size($fullpath),
            'url' => Storage::disk($this->storage)->url($fullpath),
        ]);
    }

    public function failed(Throwable $exception)
    {
        Downloads::where('id', $this->download->id)->update([
            'status' => 'failed'
        ]);
    }

    private function generateName($filename, $extension): string
    {
        return '/xgrow_reports/'.date('Y_m_d')."_{$filename}_".rand(11111111, 99999999).'.'.$extension;
    }


}
