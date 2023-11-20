<?php

namespace App\Http\Controllers;

use App\Gallery;
use Illuminate\Http\Request;
use stdClass;
use Illuminate\Support\Facades\Validator;

class GalleryController extends Controller
{
    private $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function index()
    {
        $galleries = $this->gallery->get();
        return view('gallery.index', compact('galleries'));
    }

    public function create()
    {
        $gallery = new stdClass;
        $gallery->id = 0;
        return view('gallery.create', compact('gallery'));
    }

    public function edit($id)
    {
        $gallery = $this->gallery->find($id);

        return view('gallery.edit', compact('gallery'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules['name'] = 'required';
        $validator = Validator::make($request->all(), $rules);
        $validator->validate();

        $gallery = $this->gallery->updateOrCreate(
            ['id' => $request->id],
            $request->all()
        );

       return redirect()->route('gallery.index');

    }

    public function destroy($id){
        $gallery = $this->gallery->find($id);
        if($gallery->images()->count() == 0){
            $gallery->delete();
        }
        return redirect()->route('gallery.index');
    }


}
