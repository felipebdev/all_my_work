<?php


namespace App\Services\Finances\Transaction;

use App\Services\Finances\Objects\FailureMessage;
use App\Services\Finances\Payment\Exceptions\FailedTransaction;
use App\Services\TransactionService;
use App\Transaction;
use App\Utils\TransactionItem;
use Exception;
use MundiAPILib\Models\GetOrderResponse;

use function app;
use function report;

class GatewayTransaction
{
    const DEFAULT_MESSAGE = 'Pagamento não autorizado. Verifique os dados do cartão e tente novamente ou consulte a sua operadora de cartão de crédito';

    const DEFAULT_NO_ACQUIRER_CODE = 2005;

    /**
     * Create a new exception from an Order
     *
     * @param $orderResult \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse
     * @return \App\Services\Finances\Payment\Exceptions\FailedTransaction
     */
    public static function makeExceptionForOrder($orderResult): FailedTransaction
    {
        $failures = GatewayTransaction::getOrderFailures($orderResult);

        $first = $failures[0];
        $message = $first['message'].'. '.$first['friendly_message'];

        $failedException = new FailedTransaction(trim($message), $first['code'] ?? 0);
        $failedException->withFailures($failures);

        return $failedException;
    }

    /**
     * Create a new exception from a Charge
     *
     * @param $chargeResult \MundiAPILib\Models\GetChargeResponse|\PagarmeCoreApiLib\Models\GetChargeResponse
     * @return \App\Services\Finances\Payment\Exceptions\FailedTransaction
     */
    public static function makeExceptionForCharge($chargeResult): FailedTransaction
    {
        $failure = GatewayTransaction::getChargeFailure($chargeResult);

        $message = $failure['message'].'. '.$failure['friendly_message'];

        $failedException = new FailedTransaction(trim($message), $failure['code'] ?? 0);
        $failedException->withFailures([$failure]);

        return $failedException;
    }

    /**
     * @param $orderResult \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse
     * @return array
     */
    public static function getOrderFailures($orderResult): array
    {
        $failures = [];
        foreach ($orderResult->charges as $charge) {
            $failure = self::getChargeFailure($charge);

            if ($failure) {
                $failures[] = $failure;
            }
        }

        return array_reverse($failures);
    }

    /**
     * @param $chargeResult \MundiAPILib\Models\GetChargeResponse|\PagarmeCoreApiLib\Models\GetChargeResponse
     * @return array
     */
    private static function getChargeFailure($chargeResult): array
    {
        $returnCode = (string) ($chargeResult->lastTransaction->acquirerReturnCode ?? self::DEFAULT_NO_ACQUIRER_CODE);

        if ($returnCode == 0) {
            return []; // success
        }

        $failureMessage = self::getFailure($returnCode);

        $failure = [
            'last_four_digits' => (string) ($chargeResult->lastTransaction->card->lastFourDigits ?? null),
            'brand' => (string) ($chargeResult->lastTransaction->card->brand ?? null),
            'code' => $returnCode,
            'message' => $failureMessage->getMessage(),
            'friendly_message' => $failureMessage->getFriendlyMessage(),
        ];

        return $failure;
    }

    /**
     * @param  string  $platformId
     * @param  string  $subscriberId
     * @param  \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse|null  $getOrderResponse
     * @param  string|null  $origin
     * @param  int|null  $paymentId
     */
    public static function createFailedTransaction(
        string $platformId,
        string $subscriberId,
        $getOrderResponse = null,
        ?string $origin = null,
        ?int $paymentId = null
    ) {
        try {
            /** @var TransactionService $transactionService */
            $transactionService = app()->make(TransactionService::class);
            $plans = [];
            $items = $getOrderResponse->items ?? [];
            foreach ($items as $item) {
                $plans[] = new TransactionItem(
                    $item->id,
                    $item->amount / 100,
                    $item->description,
                    $item->code,
                    $item->category ?? '',
                );
            }

            $amount = $getOrderResponse->charges[0]->amount ?? null;
            $transactionService->create(
                $platformId,
                (int) $subscriberId,
                $getOrderResponse->charges[0]->code ?? '',
                $getOrderResponse->charges[0]->lastTransaction->id ?? null,
                $getOrderResponse->charges[0]->lastTransaction->acquirerReturnCode ?? null,
                $amount / 100,
                $plans,
                $getOrderResponse->charges[0]->paymentMethod ?? '',
                $origin,
                $getOrderResponse->charges[0]->lastTransaction->card->id ?? null,
                $paymentId
            );
        } catch (Exception $e) {
            report($e);
        }
    }

    /**
     * @param  string  $platformId
     * @param  string  $subscriberId
     * @param  \MundiAPILib\Models\GetOrderResponse|\PagarmeCoreApiLib\Models\GetOrderResponse  $getOrderResponse
     * @param  string|null  $origin
     * @param  int|null  $paymentId
     */
    public static function createSuccessfulTransaction(
        string $platformId,
        string $subscriberId,
        $getOrderResponse,
        ?string $origin = null,
        ?int $paymentId = null
    ) {
        try {
            /** @var TransactionService $transactionService */
            $transactionService = app()->make(TransactionService::class);
            $plans = [];
            foreach ($getOrderResponse->items as $item) {
                $plans[] = new TransactionItem(
                    $item->id,
                    $item->amount / 100,
                    $item->description,
                    $item->code,
                    $item->category ?? '',
                );
            }

            $cardId = $getOrderResponse->charges[0]->paymentMethod === 'credit_card'
                ? $getOrderResponse->charges[0]->lastTransaction->card->id
                : null;

            $transactionService->create(
                $platformId,
                (int) $subscriberId,
                $getOrderResponse->charges[0]->code,
                $getOrderResponse->charges[0]->lastTransaction->id,
                $getOrderResponse->charges[0]->lastTransaction->acquirerReturnCode,
                $getOrderResponse->charges[0]->amount / 100,
                $plans,
                $getOrderResponse->charges[0]->paymentMethod,
                $origin,
                $cardId,
                $paymentId,
                Transaction::STATUS_SUCCESS
            );
        } catch (Exception $e) {
            report($e);
        }
    }

    private static function getFailure($code): FailureMessage
    {
        $error = self::getFailureList();
        return $error[$code] ?? FailureMessage::make(self::DEFAULT_MESSAGE);
    }

    private static function getFailureList()
    {
        return [
            "0" => FailureMessage::make("Transação autorizada"),
            "1000" => FailureMessage::make("Transação não autorizada", FailureMessage::CONTACT_BANK),
            "1001" => FailureMessage::make("Cartão vencido", FailureMessage::CONTACT_BANK),
            "1002" => FailureMessage::make("Transação não permitida", FailureMessage::CONTACT_BANK),
            "1003" => FailureMessage::make("Rejeitado pelo emissor", FailureMessage::CONTACT_BANK),
            "1004" => FailureMessage::make("Cartão com restrição", FailureMessage::CONTACT_BANK),
            "1005" => FailureMessage::make("Transação não autorizada", FailureMessage::CONTACT_BANK),
            "1006" => FailureMessage::make("Tentativas de senha excedidas", FailureMessage::CONTACT_BANK),
            "1007" => FailureMessage::make("Rejeitado emissor", FailureMessage::CONTACT_BANK),
            "1008" => FailureMessage::make("Rejeitado emissor", FailureMessage::CONTACT_BANK),
            "1009" => FailureMessage::make("Transação não autorizada", FailureMessage::CONTACT_BANK),
            "1010" => FailureMessage::make("Valor inválido", FailureMessage::CONTACT_BANK),
            "1011" => FailureMessage::make("Cartão inválido", FailureMessage::WRONG_NUMBER),
            "1013" => FailureMessage::make("Transação não autorizada", FailureMessage::CONTACT_BANK),
            "1014" => FailureMessage::make("Tipo de conta inválido", FailureMessage::WRONG_TYPE),
            "1015" => FailureMessage::make("Função não suportada", FailureMessage::INVALID),
            "1016" => FailureMessage::make("Saldo insuficiente", FailureMessage::INCREASE_LIMIT),
            "1017" => FailureMessage::make("Senha inválida", FailureMessage::INVALID_PASSWORD),
            "1019" => FailureMessage::make("Transação não permitida", FailureMessage::ANOTHER_CARD),
            "1020" => FailureMessage::make("Transação não permitida", FailureMessage::ANOTHER_CARD),
            "1021" => FailureMessage::make("Rejeitado emissor", FailureMessage::ANOTHER_CARD),
            "1022" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "1023" => FailureMessage::make("Rejeitado emissor", FailureMessage::ANOTHER_CARD),
            "1024" => FailureMessage::make("Transação não permitida", FailureMessage::ANOTHER_CARD),
            "1025" => FailureMessage::make("Cartão bloqueado", FailureMessage::BLOCKED),
            "1027" => FailureMessage::make("Excedida a quantidade de transações para o cartão", FailureMessage::LIMIT),
            "1042" => FailureMessage::make("Tipo de conta inválido", FailureMessage::WRONG_TYPE),
            "1045" => FailureMessage::make("Código de segurança inválido", FailureMessage::WRONG_CVV),
            "1048" => FailureMessage::make("Nova senha inválida"),
            "1049" => FailureMessage::make("Banco/emissor do cartão inválido", FailureMessage::ANOTHER_CARD),
            "2000" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "2001" => FailureMessage::make("Cartão vencido", FailureMessage::EXPIRED),
            "2002" => FailureMessage::make("Transação não permitida", FailureMessage::ANOTHER_CARD),
            "2003" => FailureMessage::make("Rejeitado emissor", FailureMessage::ANOTHER_CARD),
            "2004" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "2005" => FailureMessage::make("Transação não autorizada", FailureMessage::ANOTHER_CARD),
            "2006" => FailureMessage::make("Tentativas de senha excedidas", FailureMessage::ANOTHER_CARD),
            "2007" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "2008" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "2009" => FailureMessage::make("Cartão com restrição", FailureMessage::ANOTHER_CARD),
            "5003" => FailureMessage::make("Erro interno", FailureMessage::ANOTHER_CARD),
            "5006" => FailureMessage::make("Erro interno", FailureMessage::ANOTHER_CARD),
            "5025" => FailureMessage::make("Código de segurança (CVV) do cartão não foi enviado",
                FailureMessage::WRONG_CVV),
            "5054" => FailureMessage::make("Erro interno", FailureMessage::ANOTHER_CARD),
            "5062" => FailureMessage::make("Transação não permitida para este produto ou serviço",
                FailureMessage::ANOTHER_CARD),
            "5086" => FailureMessage::make("Cartão poupança inválido", FailureMessage::ANOTHER_CARD),
            "5088" => FailureMessage::make("Transação não autorizada Amex", FailureMessage::ANOTHER_CARD),
            "5089" => FailureMessage::make("Erro interno", FailureMessage::PLEASE_RETRY),
            "5092" => FailureMessage::make("O valor solicitado para captura não é válido",
                FailureMessage::PLEASE_RETRY),
            "5093" => FailureMessage::make("Banco emissor Visa indisponível", FailureMessage::PLEASE_RETRY),
            "5095" => FailureMessage::make("Erro interno", FailureMessage::PLEASE_RETRY),
            "5097" => FailureMessage::make("Erro interno", FailureMessage::PLEASE_RETRY),
            "9102" => FailureMessage::make("Transação inválida", FailureMessage::ANOTHER_CARD),
            "9103" => FailureMessage::make("Cartão cancelado", FailureMessage::ANOTHER_CARD),
            "9107" => FailureMessage::make("O banco/emissor do cartão ou a conexão parece estar offline",
                FailureMessage::ANOTHER_CARD),
            "9108" => FailureMessage::make("Erro no processamento", FailureMessage::RETRY_TRANSACTION),
            "9109" => FailureMessage::make("Erro no processamento", FailureMessage::RETRY_TRANSACTION),
            "9111" => FailureMessage::make("Emissor não respondeu em tempo", FailureMessage::RETRY_TRANSACTION),
            "9112" => FailureMessage::make("Emissor indisponível", FailureMessage::RETRY_TRANSACTION),
            // "9113" => FailureMessage::make("Transmissão duplicada", FailureMessage::DUPLICATED),
            "9124" => FailureMessage::make("Código de segurança inválido", FailureMessage::WRONG_CVV),
            "9999" => FailureMessage::make("Erro não especificado", FailureMessage::RETRY_TRANSACTION),
            "IMSG" => FailureMessage::make("Algum dado enviado na criação da transação não condiz com o modo de leitura aceito pela adquirente.",
                FailureMessage::RETRY_TRANSACTION),
        ];
    }
}
