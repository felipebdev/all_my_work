<?php

namespace App\Mail;

use App\Email;
use App\Mail\Objects\MailPayload;
use RuntimeException;

abstract class FactoryMail
{
    public static function build(int $mailId, MailPayload $payload): BaseMail
    {
        switch ($mailId) {
            case Email::CONSTANT_EMAIL_NEW_REGISTER:
                return new SendMailAccessData($payload->subscriber, $payload->password);
            case Email::CONSTANT_EMAIL_BOLETO:
                return new SendMailBankSlip($payload->platformId, $payload->subscriber, $payload->payment);
            case Email::CONSTANT_EMAIL_COUPON:
                return new SendMailCoupon($payload->platformId, $payload->coupon, $payload->email, $payload->name);
            case Email::CONSTANT_EMAIL_REFUND:
                return new SendMailRefund(
                    $payload->platformId,
                    $payload->subscriber,
                    $payload->payment,
                    $payload->refundCode,
                    $payload->refundValue,
                    $payload->planValue
                );
            case Email::CONSTANT_EMAIL_PURCHASE_PROOF:
                return new SendMailPurchaseProof($payload->platformId, $payload->subscriber, $payload->payment);
            case Email::CONSTANT_EMAIL_CHANGE_CARD:
                return new SendMailChangeCard($payload->platformId, $payload->subscriber, $payload->url);
            case Email::CONSTANT_EMAIL_LINK_CALLCENTER:
                return new SendMailLinkCallcenter($payload->attendant);
            case Email::CONSTANT_EMAIL_LINK_PENDING:
                return new SendLinkPending($payload->platformId, $payload->subscriber, $payload->url);
            case Email::CONSTANT_EMAIL_LINK_OFFER:
                return new SendLinkOffer($payload->platformId, $payload->subscriber, $payload->url);
            case Email::CONSTANT_EMAIL_BANK_SLIP_EXPIRATION:
                return new SendMailBankSlipExpiration($payload->platformId, $payload->subscriber, $payload->payment);
            case Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED:
                return new SendMailRecurrencePaymentFailed(
                    $payload->platformId,
                    $payload->subscriber,
                    $payload->payment
                );
            case Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL:
                return new SendMailRecurrencePaymentFailedSubscriptionCancel(
                    $payload->platformId,
                    $payload->subscriber,
                    $payload->payment
                );
            case Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED:
                return new SendMailRecurrencePaymentRetryFailed(
                    $payload->platformId,
                    $payload->subscriber,
                    $payload->payment
                );
            case Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_SUCCESS:
                return new SendMailRecurrencePaymentSuccess(
                    $payload->platformId,
                    $payload->subscriber,
                    $payload->payment
                );
        }

        throw new RuntimeException("Mail Id {$mailId} not found");
    }
}
