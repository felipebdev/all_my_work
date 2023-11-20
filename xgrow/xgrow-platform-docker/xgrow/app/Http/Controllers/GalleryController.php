<?php

namespace App\Http\Controllers;

use App\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    private $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function images(Request $request)
    {
        try {
            $images = $this->gallery->find($request->gallery_id);

            if (is_null($images)) {
                return response(['message' => 'Galeria não encontrada.'], 400);
            }

            $images = $images->images()->get();
            $path = config('app.url_gestao') . "/gallery";

            return response()->json([
                'images' => $images,
                'path' => $path,
            ], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Erro ao requisitar galeria.'], 400);
        }
    }


}
