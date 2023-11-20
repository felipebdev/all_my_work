<?php

namespace App;

use App\Template;
use Illuminate\Database\Eloquent\Model;

class PlatformSiteConfig extends Model
{
    protected $fillable = ['primary_color', 'secondary_color', 'background_color', 'login_template', 'image_logo', 'image_template', 'platform_id'];


    public function template(){
        return $this->belongsTo(Template::class, 'welcome_template_id', 'id');
    }

}
