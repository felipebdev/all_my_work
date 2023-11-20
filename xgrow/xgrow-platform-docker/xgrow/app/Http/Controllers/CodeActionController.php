<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailService;

class CodeActionController extends Controller
{
    private $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendCode(Request $request)
    {
        try {
            $code = rand(11111111, 99999999);
            $request->session()->put('code', $code);
            if (config('app.env') == 'production') {
                $this->emailService->sendMailCodeAction(Auth::user(), $code);
                return response(['email' => Auth::user()->email, 200]);
            }
            return response(['email' => '(AMBIENTE DE DEV - PIN: ' . $code . ')', 200]);
        } catch (Exception $e) {
            return response()->json(['error' => true, 'response' => $e->getMessage()]);
        }
    }

    public function verifyPinCode(Request $request)
    {
        try {
            if ($request->session()->get('code') == $request->input(['code'])) {
                return response()->json(['message' => 'Seu arquivo está sendo gerado. Aguarde.']);
            } else {
                return response()->json(['message' => 'O PIN digitado é inválido inválido.'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
