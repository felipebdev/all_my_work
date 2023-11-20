<?php

namespace App;

use App\Author;
use App\Content;
use App\Menu;
use App\PlatformSiteConfig;
use App\PlatformUser;
use App\Section;
use App\Template;
use App\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Platform extends Model
{
    protected $casts = [
        'id' => 'string'
    ];

    protected $fillable = [
        'id', 'name', 'url', 'name_slug', 'customer_id', 'template_id', 'url_official',
        'reply_to_email', 'reply_to_name', 'active_sales', 'pixel_id', 'google_tag_id',
        'thumb_id', 'cover'];

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function widget()
    {
        return $this->hasMany(Widget::class);
    }

    public function authors()
    {
        return $this->hasMany(Author::class);
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class);
    }

    public function contents()
    {
        return $this->hasManyThrough(Content::class, Section::class, 'platform_id', 'section_id');
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function platformSiteConfig()
    {
        return $this->hasOne(PlatformSiteConfig::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'customer_id');
    }

    public function producer()
    {
        return $this->hasMany(Producer::class);
    }


    public function users()
    {
        return $this->belongsToMany(
            PlatformUser::class,
            'platform_user',
            'platform_id',
            'platforms_users_id'
        )->withPivot(["type_access", "permission_id"]);;
    }

    public function thumb()
    {
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    static function checkPermission($platform_id, $user_id, $slug)
    {
        $platform_user = DB::table('platform_user')
            ->where('platform_id', $platform_id)
            ->where('platforms_users_id', $user_id)
            ->first();

        if ($platform_user === null) {
            // user doesn't own any platform
            return false;
        }

        if ($platform_user->type_access === 'full') {
            // restrictions not set, user has all rights
            return true;
        }

        $permissions = DB::table('platform_user')
            ->join('permissions', 'platform_user.permission_id', 'permissions.id')
            ->join('permission_role', 'permissions.id', 'permission_role.permission_id')
            ->join('roles', 'roles.id', 'permission_role.role_id')
            ->where('platform_user.platform_id', $platform_id)
            ->where('platform_user.platforms_users_id', $user_id)
            ->where('roles.slug', $slug)->count();

        if ($permissions > 0) {
            return true;
        }


        return false;
    }

    static function checkProducerPermission($platformId, $userId)
    {
        $hasAccess = DB::table('producers')
            ->join('producer_products', 'producer_products.producer_id', 'producers.id')
            ->where('producers.platform_id', $platformId)
            ->where('producers.platform_user_id', $userId)
            ->where('producer_products.status', ProducerProduct::STATUS_ACTIVE)
            ->exists();

        return $hasAccess;
    }

}
