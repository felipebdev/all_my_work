<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
	private $file;

	public function __construct(File $file)
	{
		$this->file = $file;
	}

    public function delete(Request $request){
    	try {
            $file = $this->file->find($request->id);
            Storage::disk('public_local')->delete($file->filename);
            $file->delete();
            return response()->json(['response' => 'success']);
        } catch (Exception $e) {
            return response()->json(['response' => 'fail']);
        }
    }

    public function download(Request $request){

        /*
        $file = $this->file->where('filename', $request->filename)->first();

        return response()->download($file->filename, $file->original_name);
        */
    }
}
