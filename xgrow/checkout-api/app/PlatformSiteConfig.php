<?php

namespace App;

use App\Platform;
use App\Template;
use Illuminate\Database\Eloquent\Model;

class PlatformSiteConfig extends Model
{
    //,'welcome_template_id'
    protected $fillable = ['primary_color', 'secondary_color', 'background_color','login_primary_color',
    'rodape_background_color','rodape_primary_color','cabecalho_primary_color','cabecalho_background_color','login_background_color',
    'login_template', 'image_logo_id', 'image_template_id', 'platform_id',
    'second_background_color','search_background_color','search_color','button_color',
    'cabecalho_secondary_color',
    'image_logo_login_id',
    'image_logo_rodape_id',
    'seo_title',
    'seo_description',
    'seo_keywords',
    'copyright',
    'research_bar',
    'suport',
    'user_profile',
    'favicon_id',
    'button_font_color',
    'background_image_id',
    'status_background_image',
    'card_color',
    'course_primary_color',
    'course_second_color',
    'course_card_color',
    'course_second_card_color',
    'course_button_color',
    'course_button_background',
    'course_icon_id',
    'course_module_content_color',
    'email_support',
    'status_background_image_login',
    'approve_comments',

];

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    public function welcome_template(){
        return $this->belongsTo(Template::class, 'welcome_template_id', 'id');
    }

    public function image_logo(){
        return $this->hasOne(File::class, 'id', 'image_logo_id');
    }

    public function image_template(){
        return $this->hasOne(File::class, 'id', 'image_template_id');
    }

    public function image_logo_login(){
        return $this->hasOne(File::class, 'id', 'image_logo_login_id');
    }

    public function image_logo_rodape(){
        return $this->hasOne(File::class, 'id', 'image_logo_rodape_id');
    }

    public function favicon(){
        return $this->hasOne(File::class, 'id', 'favicon_id');
    }

    public function background_image(){
        return $this->hasOne(File::class, 'id', 'background_image_id');
    }

    public function course_icon(){
        return $this->hasOne(File::class, 'id', 'course_icon_id');
    }


    static function updateTemplateJs($platform_id){

        $template = new PlatformSiteConfig();

        $content = "var config_template = " . json_encode($template->setUpTemplate($platform_id), JSON_PRETTY_PRINT);

        $platform = Platform::find($platform_id);

        createFileConfig("template.js", $content, $platform->name_slug);
    }

    public function setUpTemplate($platform_id){


        $config = $this->where('platform_id', $platform_id)->first();
        if($config){
        	$config->logo_filename = ($config->image_logo_id != null) ? $config->image_logo->filename: '';
	        $config->template_filename = ($config->image_template_id != null) ? $config->image_template->filename: '';
	        $config->logo_login_filename = ($config->image_logo_login_id != null) ? $config->image_logo_login->filename : '';
	        $config->logo_rodape_filename = ($config->image_logo_rodape_id != null) ? $config->image_logo_rodape->filename : '';
	        $config->favicon_filename = ($config->favicon_id != null) ? $config->favicon->filename : '';
	        $config->background_filename = ($config->background_image_id != null) ? $config->background_image->filename : '';
        }
        

        return $config;

    }
}
