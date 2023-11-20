<?php

namespace App\Http\Controllers;

use App\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
use DB;
class TemplateCourseController extends Controller
{
    private $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function index()
    {
        $templates = $this->template->where('course','=',1)->get();
        return view('themeCourse.index', compact('templates'));
    }

    public function create()
    {
        $template = new stdClass;
        $template->id = 0;
        $models = config('constants.course.models');
        $models = array('' => '') + $models;
        return view('themeCourse.create', compact('template','models'));
    }

    public function edit($id)
    {
        $template = $this->template->find($id);
        $models = config('constants.course.models');
        $models = array('' => '') + $models;
        return view('themeCourse.create', compact('template', 'models'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules['icon'] = 'required_if:id,0';

        $validator = Validator::make($request->all(), $rules);

        $validator->validate();

        $template = $this->template->updateOrCreate(
            ['id' => $request->id],
            [
                'name'=>$request->name,
                'description'=>$request->description,
                'folder'=>$request->folder,
                'course_model'=>$request->course_model,
                'course'=>1,
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



       return redirect()->route('templateCourse.index');

    }

}
