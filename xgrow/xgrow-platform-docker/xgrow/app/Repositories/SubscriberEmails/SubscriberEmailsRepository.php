<?php

namespace App\Repositories\SubscriberEmails;

use App\Http\Controllers\PostmarkController;
use App\Services\EmailService;
use App\Subscriber;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class SubscriberEmailsRepository
{
    public function listEmailsPostmark($email)
    {
        $postmark = new PostmarkController;

        $listEmails = $postmark->getAllMessagesByEmail($email);

        $contentEmails = [];
        if (isset($listEmails['Messages'])) {
            foreach ($listEmails['Messages'] as $listEmail) {

                $details = $postmark->detailsMessage($listEmail['MessageID']);

                $messageEvents = [];

                if (array_key_exists('Tag', $details) && $details['Tag'] === Auth::user()->platform_id) {

                    if (array_key_exists('MessageEvents', $details)) {

                        foreach ($details['MessageEvents'] as $messageEvent) {

                            $messageEvents[] = [
                                'type' => $messageEvent['Type'],
                                'received_at' => Carbon::createFromTimestamp(strtotime($messageEvent['ReceivedAt']), 'UTC')->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s'),
                            ];
                        }
                    }

                    if (array_key_exists('Subject', $details)) {
                        $contentEmails[] = [
                            'id' => $listEmail['MessageID'],
                            'subject' => $details['Subject'],
                            'send_date' => Carbon::createFromTimestamp(strtotime($listEmail['ReceivedAt']), 'UTC')->setTimezone('America/Sao_Paulo')->format('d/m/Y H:i:s'),
                            'status' => $details['Status'],
                            'messages_events' => $messageEvents
                        ];
                    }
                }
            }
        }
        return $contentEmails;
    }

    /**
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function resendData($id)
    {
        $subscriber = Subscriber::find($id);

        if (!$subscriber) {
            throw new Exception("Assinante não foi encontrado!", 404);
        }

        $subscriptions = $subscriber->subscriptions->first();

        if (empty($subscriptions)) {

            throw new Exception("Para enviar os dados de acesso é necessário que o aluno possua pelo menos um produto cadastrado.", 404);
        }

        $emailService = new EmailService();

        $ret = $emailService->sendMailNewRegisterSubscriber($subscriber);

        if (!$ret) {

            throw new Exception("Plano desse assinante não está habilitado para envio de e-mail!", 404);
        }

        return $ret;
    }
}
