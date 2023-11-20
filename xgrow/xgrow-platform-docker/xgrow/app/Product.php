<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name', 'description', 'type', 'platform_id', 'category_id',
        'image_id', 'support_email', 'keywords', 'checkout_whatsapp', 'checkout_email', 'checkout_support',
        'checkout_google_tag', 'checkout_url_terms', 'checkout_support_platform',
        'checkout_layout', 'checkout_address', 'analysis_status', 'affiliation_enabled', 'created_at',
        'updated_at'
    ];

    const CURRENCIES = [
        'BRL' => 'BRL',
        // 'EUR' => 'EUR',
        // 'USD' => 'USD',
    ];

    const UPSELL_OPTIONS = [
        1 => 'Enviar para Área de Aprendizado',
        2 => 'Redirecionar para URL',
    ];

    const DELIVERY_PLANS = [
//        1 => 'Entrega Ilimitada',
        0 => 'Entrega Selecionada',
    ];

    const INSTALLMENTS = [
        '1' => '1x',
        '2' => '2x',
        '3' => '3x',
        '4' => '4x',
        '5' => '5x',
        '6' => '6x',
        '7' => '7x',
        '8' => '8x',
        '9' => '9x',
        '10' => '10x',
        '11' => '11x',
        '12' => '12x'
    ];


    public function affiliationSettings()
    {
        return $this->hasOne(AffiliationSettings::class, 'product_id', 'id');
    }

    public function image()
    {
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_product');
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'section_product');
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function producerProduct()
    {
        return $this->hasMany(ProducerProduct::class);
    }

    public function subscribers()
    {
        return $this->hasManyThrough(Subscriber::class, Plan::class);
    }

}

