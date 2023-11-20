<?php

namespace App\Services\Finances\Objects;

/**
 * This class is intended to associate "raw" message errors to a user-friendly message error/action.
 */
class FailureMessage
{
    public const EXPIRED = 'A data de vencimento do seu cartão está vencida, por favor verifique.';
    public const CONTACT_BANK = 'Entre em contato com o seu banco para solicitar a autorização dessa compra. Informe o valor da transação.';
    public const BLOCKED = 'Esse cartão encontra-se bloqueado, por favor utilize outro cartão.';
    public const WRONG_CVV = 'O CVV digitado está incorreto. Verifique os dados no seu cartão e tente novamente.';
    public const WRONG_NUMBER = 'O número do cartão digitado está incorreto. Por favor verifique os dados do seu cartão de crédito.';
    public const WRONG_TYPE = 'O tipo de conta selecionado não existe.';
    public const PLEASE_RETRY = 'Por favor tente novamente.';
    public const RETRY_TRANSACTION = 'Retente a transação.';
    public const INCREASE_LIMIT = 'Entre em contato com o seu banco para solicitar o aumento do seu limite.';
    public const INVALID_PASSWORD = 'Senha digitada inválida do cartão de crédito.';
    public const ANOTHER_CARD = 'Utilize outro cartão ou entre em contato com o seu banco para solicitar a autorização dessa compra. Informe o valor da transação.';
    public const LIMIT = 'Você excedeu o limite de tentativas nesse cartão de crédito. Entre em contato com seu banco para solicitar a liberação.';
    public const DUPLICATED = 'É possível que o cliente já tenha realizado a compra com sucesso e está erroneamente enviando o pagamento uma segunda vez.';
    public const INVALID = 'É possível que o tipo de operação seja inválido. Ex.: cartão de débito onde se aceita apenas crédito.';

    public static function make(string $message, string $friendlyMessage = ''): self
    {
        return new static($message, $friendlyMessage);
    }

    private string $message;
    private string $friendlyMessage;

    protected function __construct(string $message, string $friendlyMessage)
    {
        $this->message = $message;
        $this->friendlyMessage = $friendlyMessage;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFriendlyMessage(): string
    {
        return $this->friendlyMessage;
    }

}
