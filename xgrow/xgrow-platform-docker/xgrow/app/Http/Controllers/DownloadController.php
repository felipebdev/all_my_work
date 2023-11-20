<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Auth;


class DownloadController extends Controller
{
    public function index()
    {
        return view('reports.downloads.index');
    }

    public function getAllDownloads()
    {
        try {
            $downloads = (new \App\Downloads)->selectByPlatform(Auth::user()->platform_id);
            return response()->json([
                'data' => $downloads,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()], 400);
        }
    }
}
