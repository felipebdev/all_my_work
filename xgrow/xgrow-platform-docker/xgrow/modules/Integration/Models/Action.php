<?php

namespace Modules\Integration\Models;

use App\Plan;
use App\Platform;
use App\Product;
use App\Utils\FillPlatformTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Integration\Casts\Secret;
use Vkovic\LaravelCustomCasts\HasCustomCasts;

class Action extends Model
{
    use SoftDeletes, FillPlatformTrait, HasCustomCasts;

    protected $table = 'app_actions';

    protected $fillable = [
        'app_id',
        'platform_id',
        'is_active',
        'description',
        'event',
        'action',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => Secret::class
    ];

    public function platform()
    {
        return $this->belongsTo(Platform::class, 'platform_id', 'id');
    }

    public function integration()
    {
        return $this->belongsTo(Integration::class, 'app_id', 'id');
    }

    public function plans()
    {
        return $this->belongsToMany(
            Plan::class,
            'app_action_products',
            'app_action_id',
            'plan_id'
        );
    }

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'app_action_products',
            'app_action_id',
            'product_id'
        );
    }

    public function resolveRouteBinding($value)
    {
        return $this->where('id', $value)
            ->where('app_actions.platform_id', '=', Auth::user()->platform_id)
            ->firstOrFail();
    }

    public function scopeOnPlatform($query, ?string $platformId = null)
    {
        if (empty($platformId)) $platformId = Auth::user()->platform_id;
        return $query->where('app_actions.platform_id', '=', $platformId);
    }

    public function scopeIsActive($query)
    {
        return $query->where('app_actions.is_active', '=', true);
    }
}
