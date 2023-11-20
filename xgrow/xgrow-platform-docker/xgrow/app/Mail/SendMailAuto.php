<?php

namespace App\Mail;

use App\Email;
use Illuminate\Support\Facades\Auth;
use StdClass;
use App\Platform;
use App\Subscriber;
use App\PlatformUser;
use App\EmailPlatform;

class SendMailAuto extends AbstractConfigurableMail
{

    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailData)
    {
        $this->emailData = $emailData;
    }

    public function getDataUser()
    {
        if (Auth::user()) {
            $user = Auth::user();
            $dataUser = PlatformUser::find($user->id);
        } elseif (auth('api')->user()) {
            $user = auth('api')->user();
            $dataUser = Subscriber::find($user->id);
        } elseif (isset($this->emailData['platform_id'])) {
            $user = new StdClass;
            $user->platform_id = $this->emailData['platform_id'];
            $dataUser = $this->emailData['user'];
        }

        if ($this->emailData['subscriber']) {
            $dataUser = Subscriber::find($this->emailData['user']->id);
        }

        return $dataUser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $dataUser = $this->getDataUser();

        if (!isset($dataUser) || $dataUser === null) {
            return $this
                ->subject("Not found")
                ->markdown('emails.auto')
                ->with([
                    'message' => "Assinante / usuário não encontrado!"
                ]);
        }

        $platform = Platform::find($dataUser->platform_id);

        $this->prepareReplyTo($platform->reply_to_email, $platform->reply_to_name);
        $this->loadEmailConfig($platform->id);
        $this->setHeaderName($platform->name);

        $emailId = $this->emailData['email_id'] ?? 1;

        $mainTemplate = Email::where('id', $emailId)->first();

        $emailTemplate = EmailPlatform::where('platform_id', $platform->id)->where('email_id', $emailId)->first()
            ?? $mainTemplate;

        $platform = Platform::find($platform->id);

        $message = str_replace('##NOME_ASSINANTE##', $dataUser->name, $emailTemplate->message);
        $message = str_replace('##EMAIL_ASSINANTE##', $dataUser->email, $message);
        $message = str_replace('##LINK_PLATAFORMA##', '<a href="'.$platform->url.'">'.$platform->url.'</a>', $message);
        $message = str_replace('##NOME_PLATAFORMA##', $platform->name, $message);
        $message = str_replace('##CODIGO_SEGURANCA##', $this->emailData['code'] ?? '', $message);
        $message = str_replace('##AUTO##', $this->emailData['password'] ?? '', $message);
        $message = str_replace('##TIPO_DOCUMENTO_ASSINANTE##', strtoupper($dataUser->document_type) ?? '', $message);
        $message = str_replace('##NUMERO_DOCUMENTO_ASSINANTE##', $dataUser->document_number ?? '', $message);
        $message = str_replace('##CELULAR_ASSINANTE##', $dataUser->cel_phone ?? '', $message);

        //links para e-mail de boleto
        if (isset($this->emailData['boleto_url'])) {
            $message = str_replace('##BOLETO_URL##', '<a href="'.$this->emailData['boleto_url'].'">Visualizar boleto</a>', $message);
        }
        $message = str_replace('##BOLETO_QRCODE##', $this->emailData['boleto_qrcode'] ?? '', $message);
        $message = str_replace('##BOLETO_PDF##', $this->emailData['boleto_pdf'] ?? '', $message);
        $message = str_replace('##BOLETO_BARCODE##', $this->emailData['boleto_barcode'] ?? '', $message);

        $message = str_replace('##LINK_CALLCENTER##', getenv('URL_CALLCENTER') ?? '', $message);

        if (isset($this->emailData['plan_name']) && isset($this->emailData['plan_name']) !== '') {
            $message = str_replace('##PLANO##', $this->emailData['plan_name'], $message);
        }

        if (isset($this->emailData['plans']) && !empty($this->emailData['plans'])) {
            $plans = "";
            foreach ($this->emailData['plans'] as $plan) {
                $price = formatCoin($plan->price, $plan->currency);
                $plans .= "{$plan->name} - {$price}<br>";
            }

            $message = str_replace('##PRODUTOS##', $plans ?? '', $message);
            $message = str_replace('##CODIGO_COMPRA##', $this->emailData['order_code'] ?? '', $message);
            $message = str_replace('##VALOR_PAGO##', formatCoin($this->emailData['price']) ?? '', $message);
        }

        $supportText = 'Para dúvidas relacionada ao produto, <br>entre em contato com o suporte do produtor.';

        if (isset($this->emailData['subscriber_model']) && !empty($this->emailData['subscriber_model'])) {
            $subject = 'Dados de Acesso';
            $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor.";
            $subscriptions = $this->emailData['subscriber_model']->subscriptions->first();

            if(!empty($subscriptions)){
                $product = $subscriptions->plan->product;
                $subject = 'Dados de Acesso - '.$product->name;
                $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor em: <strong>{$product->support_email}</strong>";
            }

        } else {
            $subject = $emailTemplate->subject;
            if (empty($subject) || trim($subject) == '') {
                $subject = $platform->name.' - '.$mainTemplate->subject ?? 'Aviso';
            }
        }

        $message = str_replace('##MENSAGEM_SUPORTE##', $supportText, $message);

        return $this
            ->subject($subject)
            ->markdown('emails.auto')
            ->with([
                'message' => $message,
                'subject' => $subject,
                'supportText' => $supportText
            ]);
    }
}

