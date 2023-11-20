<?php


namespace App\Http\Controllers\Mundipagg;

use App\Services\TransactionService;
use App\Transaction;
use App\Utils\TransactionItem;
use Exception;
use MundiAPILib\Models\GetOrderResponse;

class MundipaggExceptionController
{
    const DEFAULT_MESSAGE = 'Pagamento não autorizado. Verifique os dados do cartão e tente novamente ou consulte a sua operadora de cartão de crédito';

    public static function getMessage($result) {
        /*if( isset($result->charges) ) {
            if( isset($result->charges[0]->lastTransaction) ) {
                if( isset($result->charges[0]->lastTransaction->acquirerReturnCode) ) {
                    if (array_key_exists($result->charges[0]->lastTransaction->acquirerReturnCode, self::getMessageList())) {
                        return self::getMessageCode($result->charges[0]->lastTransaction->acquirerReturnCode);
                    }
                }
            }
        }*/
        return self::DEFAULT_MESSAGE;
    }

    public static function createFailedTransaction(
        string $platformId,
        string $subscriberId,
        GetOrderResponse $result,
        ?int $paymentId = null
    ) {
        try {
            $transactionService = app()->make(TransactionService::class);
            $plans = [];
            foreach ($result->items as $item) {
                $plans[] = new TransactionItem(
                    $item->id,
                    $item->amount / 100,
                    $item->description,
                    $item->code,
                    $item->category ?? '',
                );
            }

            $transactionService->create(
                $platformId,
                (int) $subscriberId,
                $result->charges[0]->code,
                $result->charges[0]->lastTransaction->id,
                $result->charges[0]->lastTransaction->acquirerReturnCode,
                $result->charges[0]->amount / 100,
                $plans,
                $result->charges[0]->paymentMethod,
                ($result->charges[0]->paymentMethod === 'credit_card' ? 
                    $result->charges[0]->lastTransaction->card->id : 
                    null
                ),
                $paymentId
            );
        } catch(Exception $e) {
            report($e);
        }
    }

    public static function createSuccessfulTransaction(
        string $platformId,
        string $subscriberId,
        GetOrderResponse $result,
        ?int $paymentId = null
    ) {
        try {
            /** @var TransactionService $transactionService */
            $transactionService = app()->make(TransactionService::class);
            $plans = [];
            foreach ($result->items as $item) {
                $plans[] = new TransactionItem(
                    $item->id,
                    $item->amount / 100,
                    $item->description,
                    $item->code,
                    $item->category ?? '',
                );
            }

            $transactionService->create(
                $platformId,
                (int) $subscriberId,
                $result->charges[0]->code,
                $result->charges[0]->lastTransaction->id,
                $result->charges[0]->lastTransaction->acquirerReturnCode,
                $result->charges[0]->amount / 100,
                $plans,
                $result->charges[0]->paymentMethod,
                ($result->charges[0]->paymentMethod === 'credit_card')
                    ? $result->charges[0]->lastTransaction->card->id
                    : null,
                $paymentId,
                Transaction::STATUS_SUCCESS
            );
        } catch(Exception $e) {
            report($e);
        }
    }
    private static function getMessageCode($code) {
        $error = self::getMessageList();
        return $error[$code];
    }

    private static function getMessageList() {
        return [
            "0" => "Transação autorizada",
            "1000" => "Transação não autorizada",
            "1001" => "Cartão vencido",
            "1002" => "Transação não permitida",
            "1003" => "Rejeitado pelo emissor",
            "1004" => "Cartão com restrição",
            "1005" => "Transação não autorizada",
            "1006" => "Tentativas de senha excedidas",
            "1007" => "Rejeitado emissor",
            "1008" => "Rejeitado emissor",
            "1009" => "Transação não autorizada",
            "1010" => "Valor inválido",
            "1011" => "Cartão inválido",
            "1013" => "Transação não autorizada",
            "1014" => "Tipo de conta inválido",
            "1015" => "Função não suportada",
            "1016" => "Saldo insuficiente",
            "1017" => "Senha inválida",
            "1019" => "Transação não permitida",
            "1020" => "Transação não permitida",
            "1021" => "Rejeitado emissor",
            "1022" => "Cartão com restrição",
            "1023" => "Rejeitado emissor",
            "1024" => "Transação não permitida",
            "1025" => "Cartão bloqueado",
            "1027" => "Excedida a quantidade de transações para o cartão",
            "1042" => "Tipo de conta inválido",
            "1045" => "Código de segurança inválido",
            "1048" => "Nova senha inválida",
            "1049" => "Banco/emissor do cartão inválido",
            "2000" => "Cartão com restrição",
            "2001" => "Cartão vencido",
            "2002" => "Transação não permitida",
            "2003" => "Rejeitado emissor",
            "2004" => "Cartão com restrição",
            "2005" => "Transação não autorizada",
            "2006" => "Tentativas de senha excedidas",
            "2007" => "Cartão com restrição",
            "2008" => "Cartão com restrição",
            "2009" => "Cartão com restrição",
            "5003" => "Erro interno",
            "5006" => "Erro interno",
            "5025" => "Código de segurança (CVV) do cartão não foi enviado",
            "5054" => "Erro interno",
            "5062" => "Transação não permitida para este produto ou serviço",
            "5086" => "Cartão poupança inválido",
            "5088" => "Transação não autorizada Amex",
            "5089" => "Erro interno",
            "5092" => "O valor solicitado para captura não é válido",
            "5093" => "Banco emissor Visa indisponível",
            "5095" => "Erro interno",
            "5097" => "Erro interno",
            "9102" => "Transação inválida",
            "9103" => "Cartão cancelado",
            "9107" => "O banco/emissor do cartão ou a conexão parece estar offline",
            "9108" => "Erro no processamento",
            "9109" => "Erro no processamento",
            "9111" => "Emissor não respondeu em tempo",
            "9112" => "Emissor indisponível",
            "9113" => "Transmissão duplicada",
            "9124" => "Código de segurança inválido",
            "9999" => "Erro não especificado",
            "IMSG" => "Algum dado enviado na criação da transação não condiz com o modo de leitura aceito pela adquirente.",
        ];
    }
}
