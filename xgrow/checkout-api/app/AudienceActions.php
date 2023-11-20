<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AudienceActions extends Model
{
    protected $fillable = [
        'id',
        'audience_id',
        'change_card',
        'resend_access_data',
        'resend_boleto',
        'link_pending',
        'link_offer'
    ];

    protected $casts = [
        'change_card' => 'boolean',
        'resend_access_data' => 'boolean',
        'resend_boleto' => 'boolean',
    ];

    public function audience() {
        $this->belongsTo(Audience::class, 'audience_id', 'id');
    }
}
