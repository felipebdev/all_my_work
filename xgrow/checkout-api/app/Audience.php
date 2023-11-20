<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{

    protected $fillable = [
        'id',
        'platform_id',
        'name',
        'description',
        'condition_text',
        'callcenter_active',
        'callcenter_end_date'
    ];

    protected $casts = [
        'callcenter_active' => 'boolean',
    ];

    public function action(){
        return $this->hasOne(AudienceActions::class, 'audience_id', 'id');
    }

    public function audienceConditions()
    {
        return $this->hasMany(AudienceCondition::class);
    }

    public function campaigns()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_audience', 'campaign_id', 'audience_id');
    }


    public function attendants()
    {
        return $this->belongsToMany(Attendant::class, 'attendant_audience', 'attendant_id', 'audience_id');
    }


}
