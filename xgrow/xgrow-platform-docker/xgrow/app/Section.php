<?php

namespace App;

use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use Sluggable;
    use SluggableScopeHelpers;

    protected static function boot()
    {
        parent::boot();
        static::created(function ($section) {
            $items['item_type'] = 1;
            $items['item_id'] = $section->id;
            $items['visible'] = $section->active;
            $items['platform_id'] =  $section->platform_id;
            Menu::createItem($items);
        });

        static::deleted (function ($section) {
            $items['item_type'] = 1;
            $items['item_id'] = $section->id;
            $items['platform_id'] =  $section->platform_id;
            Menu::deleteItem($items);

            $items['model_type'] = 1;
            $items['model_id'] = $section->id;
            Widget::deleteItem($items);
        });

        static::updated (function ($section) {
            $items['item_type'] = 1;
            $items['item_id'] = $section->id;
            $items['visible'] = $section->active;
            $items['platform_id'] =  $section->platform_id;
            Menu::visibilityItem($items);
        });

    }

    const ORDER_TYPE_CREATED_AT_DESC = 2;
    const ORDER_TYPE_CREATED_AT_ASC = 3;
    const ORDER_TYPE_LIKES_DESC = 4;
    const ORDER_TYPE_LIKES_ASC = 5;
    const ORDER_TYPE_VIEWS_DESC = 6;
    const ORDER_TYPE_VIEWS_ASC = 7;
    const ORDER_TYPE_COMMENTS_DESC = 8;
    const ORDER_TYPE_COMMENTS_ASC = 9;


    static function orderTypes()
    {
        return [
                self::ORDER_TYPE_CREATED_AT_DESC => [
                    'param'=>'created_at',
                    'type'=>'DESC'
                ],
                self::ORDER_TYPE_CREATED_AT_ASC => [
                    'param' => 'created_at',
                    'type' => 'ASC'
                ],
                self::ORDER_TYPE_LIKES_DESC => [
                    'param' => 'likes',
                    'type' => 'DESC'
                ],
                self::ORDER_TYPE_LIKES_ASC => [
                    'param' => 'likes',
                    'type' => 'ASC'
                ],
                self::ORDER_TYPE_VIEWS_DESC => [
                    'param' => 'views',
                    'type' => 'DESC'
                ],
                self::ORDER_TYPE_VIEWS_ASC => [
                    'param' => 'views',
                    'type' => 'ASC'
                ],
                self::ORDER_TYPE_COMMENTS_DESC => [
                    'param' => 'comments',
                    'type' => 'DESC'
                ],
                self::ORDER_TYPE_COMMENTS_ASC => [
                    'param' => 'comments',
                    'type' => 'ASC'
                ]
            ];
    }

    public function sluggable()
    {
        return [
            'name_slug' => [
                'source' => 'name'
            ]
        ];
    }

    protected $casts = [
        'section_key' => 'string'
    ];

    protected $fillable = [
        'name', 'description', 'url', 'thumb_id', 'image_id', 'active', 'allow_comments', 'allow_likes', 'allow_trackings',
        'section_key', 'name_slug', 'orderby_id', 'platform_id', 'template_id','featured_order',
        'content_title',"content_subtitle","content_author",'content_description','qtd_per_page','content_template_id',
        'section_title',"section_subtitle","section_author",'section_description','section_qtd_per_page', 'section_template_id',
         'restrict_date', 'restrict_plan', 'restrict_start', 'restrict_finish'
    ];

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    //icone
    public function thumb(){
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    //imagem
    public function image(){
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    public function platform(){
        return $this->belongsTo(Platform::class);
    }

    public function template(){
        return $this->belongsTo(Template::class);
    }

    public function contents(){
        return $this->hasMany(Content::class);
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'section_plan', 'section_id', 'plan_id');
    }


    static function havePermission($section_id, $subscriber_id){
        $plans = Subscriber::getPlans($subscriber_id);

        $section = self::find($section_id);

        return ($section->plans()->whereIn('plan_id', $plans)->count() > 0 or Subscriber::checkIfSubscriberHasUnlimitedPlans($subscriber_id));
    }

    static function notAllowed($section_id, $subscriber_id){
        return !self::havePermission($section_id, $subscriber_id);
    }

}
