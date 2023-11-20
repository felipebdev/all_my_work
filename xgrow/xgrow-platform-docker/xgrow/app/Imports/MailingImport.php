<?php

namespace App\Imports;

use App\Services\EmailTaggedService;
use Exception;
use App\Coupon;
use App\CouponMailing;
use App\Jobs\SendMail;
use App\Mail\SendMailCoupon;
use App\Services\EmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
class MailingImport implements ToModel, WithUpserts, WithBatchInserts, WithChunkReading, ShouldQueue
{
    private $platformId;
    private $coupon;

    public function __construct($platformId, Coupon $coupon) {
        $this->platformId = $platformId;
        $this->coupon = $coupon;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info("Gravando mailing de cupom", $row);
        return CouponMailing::updateOrCreate(
            [
                'email' => $row[0] ?? '',
                'name' => $row[1] ?? '',
                'notes' => $row[2] ?? '',
                'coupon_id' => $this->coupon->id ?? '',
                'isSent' => false
            ]
        );
    }

    public function batchSize(): int
    {
        return config('excel.imports.batch_size');
    }

    public function chunkSize(): int
    {
        return config('excel.imports.chunk_size');
    }

    public function uniqueBy()
    {
        return ['coupon_id', 'email'];
    }
}
