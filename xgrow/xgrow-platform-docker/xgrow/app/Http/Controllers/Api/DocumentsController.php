<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Auth\ClientStatus;
use Illuminate\Support\Facades\Auth;

class DocumentsController extends Controller
{
    public function index()
    {
        $status = ClientStatus::withPlatform(Auth::user()->platform_id, Auth::user()->email);

        return view('documents.index', [
            'clientApproved' => $status->clientApproved,
            'recipientStatusMessage' => $status->recipientStatusMessage,
            'verifyDocument' => $status->mustVerify
        ]);
    }
}
