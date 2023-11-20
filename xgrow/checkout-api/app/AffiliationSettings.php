<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AffiliationSettings extends Model
{
    protected $table = 'affiliation_settings';

    //use HasFactory;

    protected $fillable = [
        'product_id',
        'approve_request_manually',
        'receive_email_notifications',
        'buyers_data_access_allowed',
        'support_email',
        'instructions',
        'commission',
        'cookie_duration',
        'assignment',
        'invite_link',
        'created_at',
        'updated_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

}
