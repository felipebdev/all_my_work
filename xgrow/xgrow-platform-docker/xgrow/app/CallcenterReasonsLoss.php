<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallcenterReasonsLoss extends Model
{
    protected $table = 'callcenter_reasons_loss';

    protected $fillable = [
        'callcenter_id',
        'description',
    ];

    public function callcenter()
    {
        return $this->belongsTo(CallcenterConfig::class, 'callcenter_id', 'id');
    }
}
