<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriber\SubscriberDataUpdateRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Services\EmailService;
use App\Services\Subscriber\DocumentAuthenticityService;
use App\Subscriber;
use App\Utils\DateTimeFormatter;
use App\Utils\Formatter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriberDataController extends Controller
{

    use CustomResponseTrait;

    private DocumentAuthenticityService $authenticityService;

    private $emailService;

    public function __construct(
        DocumentAuthenticityService $authenticityService,
        EmailService $emailService
    ) {
        $this->authenticityService = $authenticityService;
        $this->emailService = $emailService;
    }

    public function show($subscriber_id)
    {
        $subscriber = Subscriber::findOrFail($subscriber_id);

        if (Auth::user()->platform_id != $subscriber->platform_id) {
            return $this->customJsonResponse('Aluno não encontrado na plataforma', Response::HTTP_NOT_FOUND);
        }

        $subscriberInfo = $subscriber->only([
            'name',
            'email',
            'address_country',
            'document_number',
            'document_type',
            'cel_phone',
        ]);

        if ($subscriberInfo['document_type'] != 'OTHER') {
            $subscriberInfo['document_number'] = Formatter::onlyDigits($subscriberInfo['document_number']);
        }

        $dateInfo = [
            'created_at' => DateTimeFormatter::fromLocalToIso8601String($subscriber->created_at),
            'login' => DateTimeFormatter::fromUtcToIso8601String($subscriber->login),
        ];

        $response = array_merge($subscriberInfo, $dateInfo);

        return $this->customJsonResponse('success', Response::HTTP_OK, $response);
    }

    public function update($subscriber_id, SubscriberDataUpdateRequest $request)
    {
        $platformUser = Auth::user();
        $subscriber = Subscriber::findOrFail($subscriber_id);


        if ($platformUser->platform_id != $subscriber->platform_id) {
            return $this->customJsonResponse('Aluno não encontrado na plataforma', Response::HTTP_NOT_FOUND);
        }

        if ($this->emailAlreadyUsed($request->email, $subscriber_id)) {
            return $this->customJsonResponse('Email já cadastrado', Response::HTTP_CONFLICT);
        }

        $update = $request->validated();

        $message = 'Dados Atualizados com sucesso';

        $subscriber->update($update);

        if ($request->raw_password) {
            Log::info('Subscriber password changed by Platform User', [
                'platform_user_id' => $platformUser->id,
                'platform_id' => $platformUser->platform_id,
                'subscriber_id' => $subscriber->id,
                'subscriber_email' => $subscriber->email
            ]);

            $this->emailService->sendMailNewRegisterSubscriber($subscriber, $request->raw_password);

            $message = "Senha alterada com sucesso. E-mail com a nova senha enviado para {$subscriber->name}.";
        }

        return $this->customJsonResponse($message);
    }

    /**
     * Check if the email has already been used
     * @param $email
     * @param $subscriber_id
     * @return bool
     */
    private function emailAlreadyUsed($email, $subscriber_id)
    {
        $platform_id = Auth::user()->platform_id;

        $subscriber = Subscriber::query()
            ->where('platform_id', $platform_id)
            ->where('email', $email)
            ->where('id', '<>', $subscriber_id)
            ->first();

        $subscriberStatus = $subscriber->status ?? null;
        if ($subscriber && $subscriberStatus != Subscriber::STATUS_LEAD) {
            return true;
        }

        return false;
    }

}
