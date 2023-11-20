<?php


namespace App\Services\Finances\Objects;

use Carbon\Carbon;

class Constants
{

    const MUNDIPAGG_CNPJ_FOREIGNER = '09599048000132'; // Numero de CNPJ para identificar estrangeiros na mundipagg

    const MUNDIPAGG_PAYMENT_METHOD_CREDIT_CARD = 'credit_card';
    const MUNDIPAGG_PAYMENT_METHOD_BOLETO = 'boleto';
    const MUNDIPAGG_PAYMENT_METHOD_PIX = 'pix';

    const MUNDIPAGG_CPF = 'CPF';
    const MUNDIPAGG_CNPJ = 'CNPJ';

    const MUNDIPAGG_INDIVIDUAL = 'individual';
    const MUNDIPAGG_COMPANY = 'company';

    const MUNDIPAGG_PAID = 'paid';
    const MUNDIPAGG_PENDING = 'pending';
    const MUNDIPAGG_FAILED = 'failed';
    const MUNDIPAGG_CANCELED = 'canceled';

    const MUNDIPAGG_AUTHORIZED_PENDING_CAPTURE = 'authorized_pending_capture';
    const MUNDIPAGG_CAPTURED = 'captured';

    public const MUNDIPAGG_ACCOUNT_TYPE_CHECKING = 'checking';
    public const MUNDIPAGG_ACCOUNT_TYPE_SAVINGS = 'savings';

    const PAGARME_TRANSACTION_PROCESSING = 'processing';
    const PAGARME_TRANSACTION_AUTHORIZED = 'authorized';
    const PAGARME_TRANSACTION_PAID = 'paid';
    const PAGARME_TRANSACTION_REFUNDED = 'refunded';
    const PAGARME_TRANSACTION_WAITING_PAYMENT = 'waiting_payment';
    const PAGARME_TRANSACTION_PENDING_REFUND = 'pending_refund';
    const PAGARME_TRANSACTION_REFUSED = 'refused';
    const PAGARME_TRANSACTION_CHARGEBACK = 'chargedback';
    const PAGARME_TRANSACTION_ANALYZING = 'analyzing';
    const PAGARME_TRANSACTION_PENDING_REVIEW = 'pending_review';

    const PAGARME_CNPJ = 'cnpj';
    const PAGARME_CPF = 'cpf';

    const PAGARME_INDIVIDUAL = 'individual';
    const PAGARME_CORPORATION = 'corporation';

    public const PAGARME_ACCOUNT_TYPE_CHECKING = 'conta_corrente';
    public const PAGARME_ACCOUNT_TYPE_SAVINGS = 'conta_poupanca';

    public const PAGARME_RECIPIENT_STATUS_REGISTRATION = 'registration';
    public const PAGARME_RECIPIENT_STATUS_AFFILIATION = 'affiliation';
    public const PAGARME_RECIPIENT_STATUS_ACTIVE = 'active';
    public const PAGARME_RECIPIENT_STATUS_REFUSED = 'refused';
    public const PAGARME_RECIPIENT_STATUS_SUSPENDED = 'suspended';
    public const PAGARME_RECIPIENT_STATUS_BLOCKED = 'blocked';
    public const PAGARME_RECIPIENT_STATUS_INACTIVE = 'inactive';

    // Constants used by checkout (they can be different from Payment gateway constants; they also can be different from
    // Payment::TYPE_PAYMENT_* constants)
    const XGROW_CREDIT_CARD = 'credit_card';
    const XGROW_BOLETO = 'boleto';
    const XGROW_PIX = 'pix';
    const XGROW_MULTIMEANS = 'multimeans';

    public const XGROW_ACCOUNT_TYPE_CHECKING = 'checking';
    public const XGROW_ACCOUNT_TYPE_SAVINGS = 'savings';

    const BOLETO_PAYOUT_LIMIT_DEFAULT = 2; // Vencimento em 2 dias úteis por padrao
    const BOLETO_EXPIRATION_WEEKDAYS = 2; // máximo de 2 dias úteis para compensação

    const PIX_EXPIRATION_SECONDS = 20 * Carbon::SECONDS_PER_MINUTE; // 20 minutes

    const XGROW_MESSAGE_PAYMENT_FAILED = 'Pagamento não autorizado. Verifique os dados do cartão e tente novamente';
    const XGROW_MESSAGE_INVALID_PARAMS = 'Não foi possível finalizar a compra. Verifique os campos preenchidos e tente novamente';

}
