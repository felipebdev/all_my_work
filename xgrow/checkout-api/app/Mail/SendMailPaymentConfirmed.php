<?php

namespace App\Mail;

use App\Email;
use App\Payment;
use App\Product;
use App\Subscriber;

/**
 * Class SendMailPaymentConfirmed
 *
 * @package App\Mail
 */
class SendMailPaymentConfirmed extends BaseMail
{
    private $subscriber;
    private $payments;

    public function __construct($platformId, Subscriber $subscriber, Payment ...$payments)
    {
        parent::__construct($platformId, [$subscriber->email], Email::CONSTANT_EMAIL_PAYMENT_CONFIRMED);
        $this->subscriber = $subscriber;
        $this->payments = $payments;
    }

    /**
     * This implementation uses a fixed template instead of template from DB
     *
     * @override BaseMail::setTemplate()
     *
     * @param $emailId
     * @param  null  $platformId
     * @return \App\Mail\BaseMail|void
     */
    protected function setTemplate($emailId, $platformId = null)
    {
        $template = new \stdClass();
        $template->subject = 'Confirmação de pagamento';
        $template->message = 'Pagamento confirmado';

        $this->template = $template;
    }

    public function build()
    {
        $firstPayment = $this->payments[0];
        $paymentType = $firstPayment->type;

        $plans = $firstPayment->plans;

        $subject = $this->template->subject;

        $hasInterest = false;
        $installmentMessages = [];
        foreach ($this->payments as $payment) {
            $installments = $payment->installments ?? 1;
            if ($installments > 1) {
                $hasInterest = true;
                if ($paymentType == Payment::TYPE_SALE) {
                    $valuePerInstallment = $payment->price / max(1, $installments);
                    $value = formatCoin($valuePerInstallment);
                    $price = formatCoin($payment->price);
                    $installmentMessages[] = "{$price} em {$installments}x de {$value}";
                } elseif ($paymentType == Payment::TYPE_UNLIMITED) {
                    $installmentNumber = $payment->installment_number;
                    $value = formatCoin($payment->price * $installments);
                    $installmentMessages[] = "Parcela {$installmentNumber} de {$installments} (total de {$value})";
                }
            } else {
                $installmentMessages[] = "Parcela única";
            }
        }

        $product = Product::where('id', $plans[0]->product_id)->first();

        $checkoutEmail = $product->support_email;

        $supportText = '';
        if ($checkoutEmail) {
            $supportText = "Para dúvidas relacionada ao produto, <br>entre em contato com o produtor em: <strong>{$checkoutEmail}</strong>";
        }

        $totalPaid = array_sum(array_map(fn(Payment $payment) => $payment->price, $this->payments));
        $interestMessage = $hasInterest ? ' (acrescido de juros)' : '';

        $pricePaidByCustomer = formatCoin($totalPaid).$interestMessage;

        $hasInternalLearningArea = $firstPayment->plans()->first()->product->internal_learning_area;

        $warningSpamHtml = '';
        if ($hasInternalLearningArea) {
            $warningSpam = 'Enviaremos os dados de acesso em outro e-mail separado. Caso não receba, verifique na sua caixa de lixo eletrônico, aba promoções ou similares.';
            $warningSpamHtml = '<div style="margin-bottom: 20px;">'.$warningSpam.'</div>';
        }

        return $this
            ->to($this->recipients)
            ->subject($subject)
            ->view('emails.payment-confirmed')
            ->with([
                'SUBJECT' => $subject,
                'PREVIEW' => 'Pagamento confirmado',
                'CODIGO_COMPRA' => $firstPayment->order_code ?? '',
                'NOME_ASSINANTE' => $this->subscriber->name ?? '',
                'NOME_CURSO' => $plans->pluck('name')->join('; '),
                'NOME_PRODUTO' => $plans->pluck('name')->join('; '),
                'HORA_COMPRA' => $firstPayment->created_at->format('d/m/Y H:i:s'),
                'QUANTIDADE_COMPRA' => 1, // ?
                'VALOR_COMPRA' => formatCoin($firstPayment->plans_value), // verificar esse valor
                'PARCELAS' => join('; ', $installmentMessages),
                'VALOR_PAGO' => $pricePaidByCustomer,
                'NOME' => $this->subscriber->name ?? '',
                'CPF' => $this->subscriber->document_number ?? '',
                'EMAIL' => $this->subscriber->email ?? '',
                'TELEFONE' => $this->subscriber->main_phone ?? $this->subscriber->cel_phone ?? '',
                'WARNING_SPAM_HTML' => $warningSpamHtml,
                'supportText' => $supportText,
            ]);
    }

}
