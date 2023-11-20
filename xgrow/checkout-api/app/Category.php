<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $with = ['categorizables.related'];

    protected $fillable = [
        'name', 'platform_id', 'thumb_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleted(function ($category) {
            $items['item_type'] = 4;
            $items['platform_id'] = $category->platform_id;
            $items['item_id'] = $category->id;
            Menu::deleteItem($items);

            $items['model_type'] = 4;
            $items['model_id'] = $category->id;
            Widget::deleteItem($items);

        });

    }

    public function categorizables()
    {
        return $this->hasMany(Categorizable::class);
    }

    public function getRelatedModelsAttribute()
    {
        return $this->categorizables;
    }

    public function thumb()
    {
        return $this->hasOne(File::class, 'id', 'thumb_id');
    }

    public function courses()
    {
        return $this->morphedByMany(Course::class, 'categorizable');
    }

    public function contents()
    {
        return $this->morphedByMany(Content::class, 'categorizable');
    }

    public function sections()
    {
        return $this->morphedByMany(Section::class, 'categorizable');
    }

    static function listMixContent($category_id)
    {

        $category = Category::find($category_id);
        $categorizables = $category->categorizables()->orderBy('Order', 'Asc')->get();
        $item = 0;

        foreach ($categorizables as $categorizable) {

            switch ($categorizable->categorizable_type) {
                case 'App\Section':
                    if (isset($categorizable->related->active) and $categorizable->related->active == 1 and
                        Section::havePermission($categorizable->related->id, auth('api')->user()->id)
                    ) {
                        $content[$item]['type'] = 1;
                        $content[$item]['id'] = $categorizable->related->id;
                        $content[$item]['title'] = $categorizable->related->name;
                        $content[$item]['thumb'] = $categorizable->related->thumb->filename;
                        $content[$item]['copyright'] = $categorizable->related->thumb->copyright;
                        //apenas para o template 1
                        $content[$item]['name_slug'] = $categorizable->related->name_slug;
                        $content[$item]['template'] = $categorizable->related->template_id;
                        $content[$item]['folder'] = $categorizable->related->template->folder;
                        $item++;
                    }
                    break;
                case 'App\Course':
                    if (
                        isset($categorizable->related->active) and $categorizable->related->active == 1
                        and
                        Course::havePermission($categorizable->related->id, auth('api')->user()->id)
                    ) {
                        $content[$item]['type'] = 2;
                        $content[$item]['id'] = $categorizable->related->id;
                        $content[$item]['title'] = $categorizable->related->name;
                        $content[$item]['thumb'] = $categorizable->related->thumb->filename;
                        $content[$item]['copyright'] = $categorizable->related->thumb->copyright;
                        //apenas para o template 1
                        $content[$item]['name_slug'] = '';
                        $content[$item]['template'] = $categorizable->related->template->course_model;
                        $item++;
                    }
                    break;
                default:
                    if (
                        isset($categorizable->related->published) and $categorizable->related->published == 1
                        and
                        (
                            $categorizable->related->is_course > 0
                            or
                            Content::havePermission($categorizable->related->id, auth('api')->user()->id)
                        )
                    ) {
                        $content[$item]['type'] = 3;
                        $content[$item]['id'] = $categorizable->related->id;
                        $content[$item]['title'] = $categorizable->related->title;
                        $content[$item]['has_external_link'] = $categorizable->related->has_external_link;
                        $content[$item]['external_link'] = $categorizable->related->external_link;
                        $content[$item]['thumb'] = $categorizable->related->thumb_small->filename;
                        $content[$item]['copyright'] = $categorizable->related->thumb_small->copyright;
                        //apenas para o template 1
                        if ($categorizable->related->is_course == 0) {
                            $content[$item]['name_slug'] = $categorizable->related->section->name_slug;
                            $content[$item]['template'] = 0;
                        } else {
                            $content[$item]['name_slug'] = 'cursos';
                            $content[$item]['template'] = $categorizable->related->course->template->course_model;
                            $content[$item]['curso_id'] = $categorizable->related->course_id;
                            $content[$item]['module_id'] = $categorizable->related->module_id;
                        }
                        $item++;
                    }
                    break;
            }
        }
        return $content ?? [];
    }
}
