<?php

namespace App\Http\Controllers;

use App\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use stdClass;
use DB;
class TemplateController extends Controller
{
    private $template;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public function index()
    {
        $templates = $this->template->where('platform','=',0)->get();
        return view('theme.index', compact('templates'));
    }

    public function create()
    {
        $template = new stdClass;
        $template->id = 0;
        return view('theme.create', compact('template'));
    }

    public function edit($id)
    {
        $template = $this->template->find($id);
        return view('theme.create', compact('template'));
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

        $has_slide = ($request->has_slide) ? 1:0;        

        $template = $this->template->updateOrCreate(
            ['id' => $request->id],
            [
                'name' => $request->name,
                'description' => $request->description,
                'folder' => $request->folder,
                'amount_of_fixed_content' => $request->amount_of_fixed_content,
                'platform' => 0,
                'has_slide' => $has_slide,
                'tamanho_imagem' =>$request->tamanho_imagem
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
        
       return redirect()->route('template.index');

    }

}
