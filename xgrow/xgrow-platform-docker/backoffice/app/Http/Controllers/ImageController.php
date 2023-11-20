<?php

namespace App\Http\Controllers;

use App\Gallery;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use stdClass;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class ImageController extends Controller
{
    private $image;
    private $gallery;

    public function __construct(Image $image, Gallery $gallery)
    {
        $this->image = $image;
        $this->gallery = $gallery;
    }

    public function index($id)
    {
        $gallery = $this->gallery->with('images')->findOrFail($id);
        return view('gallery.image', compact('gallery'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store($gallery_id, Request $request)
    {
        $gallery = $this->gallery->find($gallery_id);

        try {
            $file = $request->file;

            $extension = $file->getClientOriginalExtension();
            $name = $file->getClientOriginalName();

            $uuid = (string) Uuid::generate(4);
            $filename = sprintf('%s.%s',
                                    $uuid,
                                    $extension
                                    );

            $gallery->images()->create([
                'filename' => $filename,
                'size' => $file->getSize(),
                'type' => $file->getClientMimeType(),
                'original_name' => $name
            ]);
        } catch (\Exception $e) {
            toastr()->warning('Ocorreu um erro. Tente novamente.', 'Aviso');
        }

        Storage::disk('gallery')->put($filename, File::get($file));
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy($gallery_id, $id)
    {

        try {
            $image = $this->image->findOrFail($id);
            Storage::disk('gallery')->delete($image->filename);
            $image->delete();
            return redirect()->route('gallery.image.index', [$gallery_id]);
        } catch (Exception $e) {
            //toastr()->error("Não foi possível realizar a operação. Tente novamente.", "Erro");
            //return back();
        }

        //toastr()->success("A image foi excluída!", 'Sucesso');

        //return back();
        

    }


}
