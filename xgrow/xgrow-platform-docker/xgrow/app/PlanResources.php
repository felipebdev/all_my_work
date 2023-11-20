<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanResources extends Model
{
    const TYPE_ORDER_BUMP = 'O';
    const TYPE_UPSELL = 'U';

    protected $fillable = ['id', 'product_id', 'product_plan_id', 'plan_id', 'platform_id',
        'type', 'discount', 'message', 'video_url', 'image_id', 'accept_event', 'decline_event',
        'accept_url', 'decline_url', 'created_at', 'updated_at'];

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    function plans(){
        return $this->hasOne(Plan::class, 'id', 'plan_id');
    }

    function products(){
        return $this->hasMany(Product::class, 'id', 'product_id');
    }
}
