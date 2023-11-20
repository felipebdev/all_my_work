<?php

namespace App\Services\Finances\Balance\Drivers;

use App\Exceptions\Finances\RecipientNotExistsException;
use App\Services\Finances\Balance\Contracts\RecipientBalanceInterface;
use App\Services\Finances\Balance\Objects\BalanceResponse;
use MundiAPILib\APIException;
use MundiAPILib\Exceptions\ErrorException;
use MundiAPILib\MundiAPIClient;
use PagarMe\Exceptions\PagarMeException;

use function env;

class PagarmeRecipientBalance implements RecipientBalanceInterface
{

    protected MundiAPIClient $mundipaggClient;

    public function __construct()
    {
        $this->mundipaggClient = new MundiAPIClient(env('MUNDIPAGG_SECRET_KEY'));
    }

    public function getBalance(string $recipientId): BalanceResponse
    {
        try {
            $data = $this->mundipaggClient->getRecipients()->getBalance($recipientId);

            return BalanceResponse::fromMundipaggObject($data);
        } catch (PagarMeException | APIException| ErrorException $e) {
            throw new RecipientNotExistsException("Recipient not exists (id: {$recipientId})");
        }
    }
}
