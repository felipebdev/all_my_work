<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallcenterRestrictedIp extends Model
{
    protected $table = 'callcenter_restricted_ip';

    protected $fillable = [
        'callcenter_id',
        'ip_address',
    ];

    protected $casts = [
        'ip_address' => 'string'
    ];

    public function callcenter()
    {
        return $this->belongsTo(CallcenterConfig::class, 'callcenter_id', 'id');
    }
}
