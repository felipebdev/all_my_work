<?php

namespace App\Services;



use App\Platform;
use Illuminate\Http\JsonResponse;
use MundiAPILib\APIException;

class ClientWithdrawalsService
{

    public $mundipaggService;

    public function init($platform_id)
    {
        $this->mundipaggService = new MundipaggService($platform_id);
        return $this->mundipaggService;
    }

    private function getRecipients()
    {
        return $this->mundipaggService->getClient()->getRecipients();
    }

    private function getRecipientId()
    {
        $platform = Platform::findOrFail($this->mundipaggService->getPlatformId());
        return $platform->recipient_id ?? Client::where('id', $platform->customer_id)->first()->recipient_id;
    }

    public function getWithdrawalsByPlatform($platform_id){
        $this->init($platform_id);
        return $this->getRecipients()->getWithdrawals($this->getRecipientId())->data;
    }

    public function getWithdrawalsByClient($constumer_id)
    {
        //Listar todas as plataformas do cliente e criar um form
        /*
        $platforms[0] = '75faed31-56dd-4149-9c4a-af966112dbf8';
        $platforms[1] = '75faed31-56dd-4149-9c4a-af966112dbf8';
        foreach($platforms as $platform) {
           $withdrawals[] = $this->getWithdrawalsByPlatform($platform);
        }
        $withdrawals = collect($withdrawals)
            ->flatten();

        return $withdrawals;
        */
    }

    /**
     * Get recipient data
     * @return JsonResponse
     * @throws APIException
     */
    public function getClientRecipient()
    {
        return self::getRecipients()->getRecipient(self::getRecipientId());
    }

    /**
     * Get recipient balance
     * @return JsonResponse
     * @throws APIException
     */
    public function getClientBalance()
    {
        $recipientId = self::getRecipientId();
        if (empty($recipientId)) {
            return response()->json(null, 204);
        }

        return self::getRecipients()->getBalance(self::getRecipientId());
    }

}
