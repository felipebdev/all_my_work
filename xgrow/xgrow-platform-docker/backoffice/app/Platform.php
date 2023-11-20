<?php

namespace App;

use App\Http\Traits\ElasticsearchTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Platform extends Model
{
    use Sluggable, SluggableScopeHelpers, SoftDeletes, HasFactory, ElasticsearchTrait;

    protected $hidden = ['pivot'];

    public function sluggable()
    {
        return [
            'name_slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'restrict_ips' => 'bool'
    ];

    protected $fillable = [
        'id', 'name', 'url', 'name_slug', 'slug', 'customer_id',
        'template_id', 'active_sales', 'template_schema', 'restrict_ips',
        'ips_available', 'featured_image', 'recipient_id', 'cover'
    ];

    public function platformSiteConfig()
    {
        return $this->hasOne(PlatformSiteConfig::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function platformsUsers()
    {
        return $this->hasMany(PlatformUser::class, 'platform_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'customer_id', 'id');
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower($value);
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
