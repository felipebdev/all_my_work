<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SendMailChangeCard;
use App\Services\Contracts\JwtPlatformServiceInterface;
use App\Services\EmailService;
use App\Subscriber;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    private $jwtPlatformService;

    public function __construct(JwtPlatformServiceInterface $jwtPlatformService)
    {
        $this->jwtPlatformService = $jwtPlatformService;
    }

    public function requestToken(Request $request)
    {
        $email = $request->email;
        $documentNumber = $request->document_number;
        $platformId = $request->platform_id;
        $baseUrl = $request->url;

        $strippedDocument = $this->cleanupDocumentNumber($documentNumber);

        $sqlStripDocument = 'REPLACE(REPLACE(REPLACE(document_number, ".", ""), "-", ""), "/", "")';

        $subscriber = Subscriber::select(
                "subscribers.document_number",
                'subscribers.email',
                'subscribers.platform_id'
        )
            ->whereRaw("$sqlStripDocument = ? ", $strippedDocument)
            ->where('email', $email)
            ->where('platform_id', $platformId)
            ->first();

        if (!$subscriber) {
            return response()->json('User not found', 404);
        }

        $token = $this->jwtPlatformService
            ->generateToken($platformId, $subscriber->email, $subscriber->document_number ?? '');

        $urlWithToken = "$baseUrl?token={$token}";

        try {
            EmailService::mail([$subscriber->email], new SendMailChangeCard($platformId, $subscriber, $urlWithToken));
        } catch (\Exception $exception) {
            return response()->json('Falha ao enviar email, favor entrar em contato com suporte', 500);
        }

        return response()->json(array_merge(
            $subscriber->setAppends([])->toArray(),
            ['url' => $baseUrl]
        ));
    }

    private function cleanupDocumentNumber($documentNumber)
    {
        return preg_replace('/[^0-9]/', '', $documentNumber);
    }

}
