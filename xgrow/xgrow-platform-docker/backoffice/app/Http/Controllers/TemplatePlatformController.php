<?php

namespace App\Http\Controllers;

use App\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
use DB;
class TemplatePlatformController extends Controller
{
    private $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function index()
    {
        $templates = $this->template->where('platform','=',1)->get();
        return view('themePlatform.index', compact('templates'));
    }

    public function create()
    {
        $template = new stdClass;
        $template->id = 0;
        $models = config('constants.templates_platform.models');
        $models = array('' => '') + $models;
        $folders = config('constants.templates_platform_folder.models');
        $folders = array('' => '') + $folders;
        return view('themePlatform.create', compact('template','models','folders'));
    }

    public function edit($id)
    {
        $template = $this->template->find($id);
        $models = config('constants.templates_platform.models');
        $models = array('' => '') + $models;
        $folders = config('constants.templates_platform_folder.models');
        $folders = array('' => '') + $folders;
        return view('themePlatform.create', compact('template','models','folders'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules['icon'] = 'required_if:id,0';

        $content_model = (isset($request->content_model)) ?? 1;
        $request->request->add(['content_model' => $content_model]);

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $template = $this->template->updateOrCreate(
            ['id' => $request->id],
            [
                'name'=>$request->name,
                'description'=>$request->description,
                'folder'=>$request->folder,
                'amount_of_fixed_content'=>0,
                'platform'=>1,
                'content_model'=>1,
            ]
        );

        $file = $request->file('icon');

        if(isset($file)){
            $thumb = $template->file()->create([
                'file' => $file
            ]);
            $template->update(
                [
                    'thumb_id' => $thumb->id
                ]
            );
        }
       return redirect()->route('templatePlatform.index');
    }

    public function destroy($id){
        $template = $this->template->find($id);

        if($template->active==0)
        {
            $template->active = 1;
        }else{
            $template->active = 0;

        }

        
        $template->save();
        return redirect()->route('templatePlatform.index');
    }

}
