<?php

namespace Modules\Integration\Models;

use App\Platform;
use App\Utils\FillPlatformTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Modules\Integration\Casts\Secret;

class Integration extends Model
{
    use SoftDeletes, FillPlatformTrait;

    protected $table = 'apps';
    protected $fillable = [
        'platform_id',
        'is_active',
        'description',
        'code',
        'type',
        'api_key',
        'api_account',
        'api_webhook',
        'api_secret',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'api_key' => Secret::class,
        'api_account' => Secret::class,
        'api_webhook' => Secret::class,
        'api_secret' => Secret::class,
        'metadata' => Secret::class
    ];

    public function getTypeAttribute($value)
    {
        return strtolower($value);
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = strtolower($value);
    }

    public function platform()
    {
        return $this->belongsTo(
            Platform::class, 
            'platform_id', 
            'id'
        );
    }

    public function actions()
    {
        return $this->hasMany(
            Action::class,
            'app_id',
            'id'
        );
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->where('platform_id', Auth::user()->platform_id)
            ->firstOrFail();
    }

    public function scopeOnPlatform($query, ?string $platformId = null) 
    {
        if (empty($platformId)) $platformId = Auth::user()->platform_id;
        return $query->where('apps.platform_id', '=', $platformId);
    }

    public function scopeIsActive($query) 
    {
        return $query->where('apps.is_active', '=', true);
    }
}
