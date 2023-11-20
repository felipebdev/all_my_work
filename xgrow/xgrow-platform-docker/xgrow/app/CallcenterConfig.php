<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CallcenterConfig extends Model
{
    protected $table = 'callcenter_config';

    protected $fillable = [
        'active',
        'period_restriction',
        'initial_date',
        'initial_hour',
        'final_date',
        'final_hour',
        'ip_restriction',
        'allow_changes',
        'limit_leads',
        'number_leads',
        'allow_reasons_loss',
        'show_email',
        'show_address',
        'platform_id'
    ];

    protected $casts = [
        'initial_date' => 'date',
        'final_date' => 'date',
        'active' => 'boolean',
        'period_restriction' => 'boolean',
        'ip_restriction' => 'boolean',
        'allow_changes' => 'boolean',
        'limit_leads' => 'boolean',
        'allow_reasons_loss' => 'boolean',
        'show_email' => 'boolean',
        'show_address' => 'boolean',
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }

    public function reasons()
    {
        return $this->hasOne(CallcenterReasonsLoss::class, 'callcenter_id', 'id');
    }

    public function restricted_ip()
    {
        return $this->hasOne(CallcenterRestrictedIp::class, 'callcenter_id', 'id');
    }
}
