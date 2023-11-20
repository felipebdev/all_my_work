<?php

namespace App;

use App\Category;
use App\Content;
use App\Course;
use App\Forum;
use App\Menu;
use App\Platform;
use App\PlatformSiteConfig;
use App\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Menu extends Model
{
    protected $fillable = [
        'item_type','item_id', 'order', 'visible','platform_id'
    ];

    const DEFAULT_IMAGE = 'https://fandone.us-east-1.linodeobjects.com/graduation-cap.png';

     public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    static function createItem($data){
        if(!isset($data['order'])){
            $order = Menu::where('platform_id', $data['platform_id'])->max('order');
            $order++;
            $data['order'] = $order;
        }
        Menu::create($data);
    }

    static function deleteItem($data){
        $item = Menu::where('item_type', $data['item_type'])
                    ->where('item_id', $data['item_id'])
                    ->where('platform_id', $data['platform_id'])->first();

        if($item){
            Menu::where('platform_id',  $data['platform_id'])
             ->where('order', '>', $item->order)
             ->update(['order' => DB::raw('`order`-1')]);

            $item->delete();
        }

         //Menu::updateMenuJs($data['platform_id']);

    }

    static function visibilityItem($data){
        $item = Menu::where('item_type', $data['item_type'])
                    ->where('item_id', $data['item_id'])
                    ->where('platform_id', $data['platform_id'])
                    ->first();
        if($item)
            $item->update(['visible' => $data['visible']]);
    }


    static function updateMenuJs($platform_id){

        /*
        $platform = Platform::find($platform_id);

        //template antigo js
        if($platform->template_schema == 1){
             $menu = new Menu();

            $content = "var config_menu = " . json_encode($menu->setUpMenu($platform_id), JSON_PRETTY_PRINT);

            createFileConfig("menu.js", $content, $platform->name_slug);
        }
        */
       
    }

    public function filterByContentRestrictions($items, $subscriber){
        $data = [];
        foreach($items as $item){
            if($item['type'] == 3){
                //se não tem permissão não adiciona ao menu
                if(Content::notAllowed($item['id'], auth('api')->user()->id))
                    continue;
            }
            array_push($data, $item); 
        }
        return $data;
    }

    public function filterBySectionRestrictions($items, $subscriber){
        $data = [];
        foreach($items as $item){
            if($item['type'] == 1){
                //se não tem permissão não adiciona ao menu
                if(Section::notAllowed($item['id'], auth('api')->user()->id))
                    continue;
            }
            array_push($data, $item); 
        }
        return $data;
    }

    public function filterByCourseRestrictions($items, $subscriber){
        $data = [];

        foreach($items as $item){
            if($item['type'] == 2 and $item['id'] > 0){
                //se não tem permissão não adiciona ao menu
                if(Course::notAllowed($item['id'], auth('api')->user()->id))
                    continue;
            }
            array_push($data, $item); 
        }

        return $data;
    }

    public function setUpMenu($platform_id){

    	$items = $this->where('platform_id', $platform_id)->where('visible', 1)->orderBy('order','ASC')->get();

    	$menu = [];
    	foreach($items as $item){
    		$image = null;
            $name_slug = null;
            $template = null;
            $course = null;
            $has_external_link = 0;
            $external_link = null;
    		//Seção
    		if($item->item_type == 1){
    			$section = Section::find($item->item_id);
                if($section){
                    $image = $section->thumb->filename;
                    $name_slug = $section->name_slug;
                    $template = $section->template;
                    $title = $section->name;
                }
                else continue;

    		}
    		else if($item->item_type == 2){ //cursos

                $title = 'Cursos';
                if($item->item_id > 0){
                    $model = new Course();
                    $course = $model->find($item->item_id);

                    //Gambiarra temporária para Mentoria 4.0 => retirar qnd tiver a opção de editar título e imagem do menu (caso não selecione imagem pega do model)
                    if($item->item_id == 11 and config('app.env') == 'production'){
                        $image = "https://fandone.us-east-1.linodeobjects.com/d67d99ee-6b33-45b4-a9cc-7b3ed74d489c.png";
                        $title = "Mentoria";
                    }
                    else{
                        $image = $course->thumb->filename;
                        $title = $course->name;
                    }

                    $course_model = $course->template->course_model;
                    $course->course_model = $course_model;
                }
                else{
                    $config = PlatformSiteConfig::where('platform_id', $platform_id)->with('course_icon')->first();
                    $image = ($config->course_icon) ? $config->course_icon->filename : Menu::DEFAULT_IMAGE;
                }
    		}
    		else if($item->item_type == 3){ //conteúdos
    			$content = Content::find($item->item_id);
                if($content){
                    $title = $content->title;
                    $image = $content->thumb_small->filename;
                    $name_slug = $content->section->name_slug;
                    $template = $content->section->template;
                    $has_external_link = $content->has_external_link;
                    $external_link = $content->external_link;
                }
                else continue;
    		}
            else if($item->item_type == 4){ //categorias
                $category = Category::find($item->item_id);
                if($category){
                    $title = $category->name;
                    $image = $category->thumb->filename;
                }
                else continue;
            }
            else if($item->item_type == 5){ //Fórum
                $forum = Forum::where('platform_id', $platform_id)->with('thumb')->first();
                if($forum){
                    $title = 'Fórum';
                    $image = ($forum->thumb) ? $forum->thumb->filename : Menu::DEFAULT_IMAGE;
                }
                else continue;
            }

    		$data['type'] = $item->item_type;
    		$data['title'] = $title;
            $data['id'] = $item->item_id;

            $data['short_title'] = resumeString($title);
    		
            $data['key'] = $item->id;
    		$data['image'] = $image;
            $data['name_slug'] = $name_slug;
            $data['course'] = $course;
            $data['template'] = $template;
            $data['has_external_link'] = $has_external_link;
            $data['external_link'] = $external_link;


    		array_push($menu, $data);
    	}
    	return $menu;
    }

}
