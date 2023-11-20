<?php

namespace App;

use App\Category;
use App\Content;
use App\Course;
use App\Platform;
use App\Section;
use App\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Widget extends Model
{
    protected $fillable = [
        'widget_type', 'model_type', 'model_id', 'quantity', 'order', 'platform_id', 'image_id', 'icon_id', 'title', 'font', 'color'
    ];

     public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function file()
    {
        return $this->morphOne(File::class, 'filable');
    }

    //imagem
    public function image(){
        return $this->hasOne(File::class, 'id', 'image_id');
    }

    //icone
    public function icon(){
        return $this->hasOne(File::class, 'id', 'icon_id');
    }


    static function deleteItem($data){
        $item = Widget::where('model_type', $data['model_type'])
                    ->where('model_id', $data['model_id'])
                    ->where('platform_id', $data['platform_id'])->first();

        if($item){
            Widget::where('platform_id',  $data['platform_id'])
             ->where('order', '>', $item->order)
             ->update(['order' => DB::raw('`order`-1')]);

            $item->delete();
        }
        
    }

    public function filterByCourseRestrictions($items, $subscriber){
        $data = [];
 

        foreach($items as $item){
            if($item['widget_type'] == 6){
                $item_content = []; 
                foreach($item['content'] as $content){
                    if($content['type'] == 2 and Course::notAllowed($content['id'], auth('api')->user()->id))
                        continue;
                    array_push($item_content, $content);
                }
                $item['content'] = $item_content;
            }
            else if($item['widget_type'] == 5){
                //se não tem permissão não adiciona ao menu
                if($item['model_id'] > 0){
                    if(Course::notAllowed($item['model_id'], auth('api')->user()->id))
                        continue;
                }
                else{
                    $item_content = []; 
                    foreach($item['content'] as $content){
                        if(Course::notAllowed($content['id'], auth('api')->user()->id))
                            continue;
                        array_push($item_content, $content);
                    }
                    $item['content'] = $item_content;
                }
                
            }

            array_push($data, $item); 
        }

        return $data;
    }

    public function filterBySectionRestrictions($items, $subscriber){
        $data = [];

        foreach($items as $item){
            if($item['widget_type'] == 1){
                 if(Section::notAllowed($item['model_id'], auth('api')->user()->id))
                        continue;
            }
            else if($item['widget_type'] == 6){
                $item_content = []; 
                foreach($item['content'] as $content){
                    if($content['type'] == 1 and Section::notAllowed($content['id'], auth('api')->user()->id))
                        continue;
                    array_push($item_content, $content);
                }
                $item['content'] = $item_content;
            }

            array_push($data, $item); 
        }

        return $data;
    }

    public function filterByContentRestrictions($items, $subscriber){
        $data = [];

        foreach($items as $item){
            if($item['widget_type'] == 2){
                 if(Content::notAllowed($item['model_id'], auth('api')->user()->id))
                        continue;
            }
            else if($item['widget_type'] == 4){
                $item_content = []; 
                foreach($item['content'] as $content){
                    if(Content::notAllowed($content->id, auth('api')->user()->id))
                        continue;
                    array_push($item_content, $content);
                }
                $item['content'] = $item_content;
            }

            array_push($data, $item); 
        }

        return $data;
    }


    public function setUpWidget($platform_id){
    	$items = $this->where('platform_id', $platform_id)->with('image:id,filename,copyright')->with('icon:id,filename,copyright')->orderBy('order','ASC')->get();
        
    	$widgets = [];
    	foreach($items as $item){

    		$section = null;
    		$content = null;
            $sql = null;

            //Seção ou Últimos Conteúdos
    		if($item->widget_type == 1 or $item->widget_type == 4){
    			if($item->model_id > 0){
    				$section = Section::with('template')->find($item->model_id);
                    if($section){
                        $order = ($section->orderby_id > 0) ? $section->orderby_id: Section::ORDER_TYPE_CREATED_AT_DESC;

                        $order_array = Section::orderTypes();

                        $section_id = $item->model_id;
                        $quantity = $item->quantity;
                        $param = $order_array[$order]['param'];
                        $type = $order_array[$order]['type'];

                        $sql = "SELECT c.created_at as created_at, c.id, c.title, c.has_external_link, c.external_link, f.filename, f.copyright, s.name_slug, s.template_id, t.folder, (SELECT count(*) FROM comments WHERE contents_id = c.id) as comment_count FROM contents c INNER JOIN files f INNER JOIN sections s INNER JOIN templates t ON c.thumb_big_id = f.id AND c.section_id = s.id AND s.template_id = t.id WHERE c.section_id=$section_id AND c.published = 1 ORDER BY $param $type LIMIT $quantity";

                        $content =  DB::select($sql);

                    }
                    else continue;
    			}
    			else{ 
	    			$content =  DB::select("SELECT c.id, c.title, c.has_external_link, c.external_link, f.filename, f.copyright, s.name_slug, s.template_id, t.folder FROM contents c INNER JOIN files f INNER JOIN sections s INNER JOIN templates t ON c.thumb_big_id = f.id AND c.section_id = s.id AND s.template_id = t.id WHERE c.is_course = 0 AND s.platform_id = :platform_id AND c.published = 1 ORDER BY c.created_at DESC LIMIT :quantity", 
                        [
                            ':quantity' => $item->quantity,
                            ':platform_id' => $platform_id
                        ]);
    			}

    		}
    		else if($item->widget_type == 2){ //Conteúdo
    			$content = Content::with('thumb_small:id,filename,copyright')
                                    ->with('thumb_big:id,filename,copyright')
                                    ->with('section')
                                    ->where('published', 1)
                                    ->find($item->model_id);
                if($content) $section = Section::with('template')->find($content->section_id);
                else continue;
    		}
            else if($item->widget_type == 5){ //Cursos
                if($item->model_id > 0){ // Chamada para um único curso
                    $content = Course::with('thumb:id,filename,copyright')
                                      ->with('template:id,course_model')
                                      ->where('active', 1)
                                      ->find($item->model_id);
                    if($content){
                        $content['description'] = resumeString(strip_tags($content->description, '<p><br>'), 360);
                    }
                    else continue;
                }
                else{
                    $new_course = new Course();

                    $content = $new_course->where('platform_id', $platform_id)
                    ->where('active', 1)
                    ->with('thumb:id,filename,copyright')
                    ->with('template:id,course_model')
                    ->get();
                }
                
            }
            else if($item->widget_type == 6){ //Categoria
                    $category = Category::find($item->model_id);
                    if($category){
                        $content = Category::listMixContent($item->model_id);
                    }
                    else continue;
            }

    		$data = $item;
    		$data['content'] = $content;
    		$data['section'] = $section;

    		array_push($widgets, $data);
    		
    	}
    	
    	return $widgets;
    }
	

}
