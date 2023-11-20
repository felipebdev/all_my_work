<?php

namespace App\Http\Controllers;

use App\Services\PandaVideoService;
use Illuminate\Http\Request;

ini_set('memory_limit', '2048M');

class VideoUploadController extends Controller
{

	private $panda;

    public function __construct(PandaVideoService $panda)
    {
        $this->panda = $panda;
    }

    public function send(Request $request)
    {
    	try {
            $video = $request->file('file');
             $data = $this->panda->upload($video);
            return response()->json(['response' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
	}

    public function getDataUpload()
    {
    	try {
    		ini_set('memory_limit', '2048M');
          	$data_upload = $this->panda->getDataUpload();
            return response()->json(['response' => 'success', 'data_upload' => $data_upload]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
	}

    public function getVideo(Request $request)
    {
    	try {
    		$video = $this->panda->getVideo($request->token, $request->video_id);
            return response()->json(['response' => 'success', 'video' => $video]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
	}

}
