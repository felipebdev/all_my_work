<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 */
class AffiliationSettings extends Model
{
    /**
     * @var string
     */
    protected $table = "affiliation_settings";

    /**
     * @var string[]
     */
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
        'invite_link'
    ];
    /**
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @param $productId
     * @return mixed
     */
    public function getAffiliateSettings($productId)
    {
        return $this->where('product_id', $productId)->first();
    }
}
