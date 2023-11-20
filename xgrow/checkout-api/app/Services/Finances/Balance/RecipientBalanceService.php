<?php

namespace App\Services\Finances\Balance;

use App\Exceptions\NotImplementedException;
use App\Repositories\Finances\RecipientRepository;
use App\Services\Finances\Balance\Contracts\RecipientBalanceInterface;
use App\Services\Finances\Balance\Objects\BalanceResponse;

class RecipientBalanceService
{

    private RecipientBalanceInterface $balance;

    private RecipientRepository $recipientRepository;

    private AnticipationService $anticipationService;

    public function __construct(
        RecipientBalanceAdapter $adapter,
        RecipientRepository $recipientRepository,
        AnticipationService $anticipationService
    ) {
        $driver = $adapter->driver();
        if (!$driver instanceof RecipientBalanceInterface) {
            throw new NotImplementedException('Recipient balance not implemented by driver: '.$adapter->getDefaultDriver());
        }

        $this->balance = $driver;
        $this->recipientRepository = $recipientRepository;
        $this->anticipationService = $anticipationService;
    }

    /**
     * @param  string  $platformId
     * @param  string  $userId
     * @param  string|null  $actingAs
     * @return \App\Services\Finances\Balance\Objects\BalanceResponse
     * @throws \App\Exceptions\Finances\RecipientNotExistsException
     * @throws \App\Exceptions\Finances\RecipientNotFound
     */
    public function getUserBalance(string $platformId, string $userId, string $actingAs): BalanceResponse
    {
        // get recipient's balance
        $recipient = $this->recipientRepository->getRecipientInfoByActor($platformId, $userId, $actingAs);

        $balance = $this->balance->getBalance($recipient->id);

        // calculate anticipation based on rules defined on Gateway
        $anticipationAmount = $this->anticipationService->getAnticipationAmount($platformId, $recipient);

        // adjust pending amount in balance
        $finalBalance = $balance->cloneWithAnticipation($anticipationAmount);

        return $finalBalance;
    }

}
