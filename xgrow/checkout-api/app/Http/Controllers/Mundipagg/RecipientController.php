<?php


namespace App\Http\Controllers\Mundipagg;


use App\Services\Mundipagg\MundipaggSplitService;
use Exception;
use App\Client;
use App\Platform;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MundiAPILib\APIException;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Auth;
use App\Services\Mundipagg\SplitService;
use ErrorException;
use MundiAPILib\Models\CreateWithdrawRequest;
use MundiAPILib\Models\UpdateTransferSettingsRequest;
use MundiAPILib\Models\UpdateRecipientBankAccountRequest;
use MundiAPILib\Models\UpdateAutomaticAnticipationSettingsRequest;

class RecipientController
{
    private static function getRecipients() {
        $mundipaggService = new MundipaggService();
        return $mundipaggService->getClient()->getRecipients();
    }

    private static function getRecipientId() {
        $platform = Platform::findOrFail(Auth::user()->platform_id);
        return $platform->recipient_id ?? Client::where('id', $platform->customer_id)->first()->recipient_id;
    }

    /**
     * Get recipient data
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function getClientRecipient() {
        return response()->json(self::getRecipients()->getRecipient(self::getRecipientId()));
    }

    /**
     * Get recipient balance
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function getClientBalance() {
        $recipientId = self::getRecipientId();
        if (empty($recipientId)) {
            return response()->json(null, 204);
        }

        return response()->json(self::getRecipients()->getBalance(self::getRecipientId()));
    }

    /**
     * List recipient withdrawals
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function listWithdrawals() {
        return response()->json(self::getRecipients()->getWithdrawals(self::getRecipientId()));
    }

    public function listWithdrawalsDatatablesData(Request $request) {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $page = ($start / $length) + 1;

        try {
            $paginator = self::getRecipients()->getWithdrawals(self::getRecipientId(), $page, $length);
        }
        catch(Exception $e) {}

        $datatables = [
            'data' => $paginator->data ?? [],
            'input' => [
                'length' => $length,
                'start' => $start
            ],
            'recordsFiltered' => $paginator->paging->total ?? 0,
            'recordsTotal' => $paginator->paging->total ?? 0
        ];

        return response()->json($datatables);
    }

    /**
     * Get recipient withdrawal
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function getWithdrawal($withdrawal_id) {
        try {
            $witdraw = self::getRecipients()->getWithdrawById(self::getRecipientId(), $withdrawal_id);
        }
        catch(APIException $e) {
            return response()->json($e, 400);
        }
        return response()->json($witdraw);
    }

    /**
     * Send withdrawal request
     * @param Request $witdrawRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function sendWithdrawal(Request $request) {
        try {
            $withdrawRequest = new CreateWithdrawRequest();
            $withdrawRequest->amount = str_replace('.','',(string) number_format($request->amount, 2, '.', '.'));
            $witdraw = self::getRecipients()->createWithdraw(self::getRecipientId(), $withdrawRequest);
        }
        catch(APIException $e) {
            $translatedMessage = $e->message;

            if ($e->message === "The withdraw could not be created.")
                $translatedMessage = "O saque não pôde ser efetuado.";

            if ($e->message === "The request is invalid.")
                $translatedMessage = "A requisição é inválida.";

            $e->message = $translatedMessage;

            return response()->json($e, 400);
        }
        catch(ErrorException $e) {
            if ($e->getMessage() === "A non well formed numeric value encountered")
                $translatedMessage = "Um valor numérico mal formado encontrado";

            $newE = (object) [
                'message' => $translatedMessage,
                'exception' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            return response()->json($newE, 400);
        }

        return response()->json($witdraw);
    }

    /**
     * Set recipient transfer settings
     * @param $transfer_enabled
     * @param $transfer_interval
     * @param $transfer_day
     * @return \Illuminate\Http\JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function setTransferSettings(Request $request) {

        try {
            $transferSettings = new UpdateTransferSettingsRequest();
            $transferSettings->transferEnabled = $request->transfer_enabled;
            $transferSettings->transferInterval = $request->transfer_interval;
            $transferSettings->transferDay= $request->transfer_day;
            $transfer = self::getRecipients()->updateRecipientTransferSettings(self::getRecipientId(), $transferSettings);
        }
        catch(APIException $e) {
            return response()->json($e, 400);
        }

        return response()->json($transfer);
    }

    public function setAutomaticAnticipationSettings(Request $request) {

        try {
            $anticipationSettings = new UpdateAutomaticAnticipationSettingsRequest();
            $anticipationSettings->enabled = $request->enabled;
            $anticipationSettings->days = explode(',',$request->days);
            $anticipationSettings->delay = $request->delay;
            $anticipationSettings->type = $request->type;
            $anticipationSettings->volumePercentage = $request->volumePercentage;
            $transfer = self::getRecipients()->updateAutomaticAnticipationSettings(self::getRecipientId(), $anticipationSettings);
        }
        catch(APIException $e) {
            return response()->json($e, 400);
        }

        return response()->json($transfer);
    }

    /**
     * Get antecipation's limit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAntecipationLimits()
    {
        $paymentDate = Carbon::now()->nextWeekday()->setTime(8,0);
        $recipientId = self::getRecipientId();
        if (empty($recipientId)) {
            return response()->json(null, 204);
        }

        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));

            $anticipationLimits = $pagarme->bulkAnticipations()->getLimits([
                'recipient_id' => self::getPagarMeRecipientId(),
                'payment_date' => $paymentDate->valueOf(),
                'timeframe' => 'start'
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return response()->json($anticipationLimits);
    }

    /**
     * Create antecipation
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendAntecipation(Request $request)
    {
        $paymentDate = Carbon::now()->nextWeekday()->setTime(8,0);

        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
            $anticipation = $pagarme->bulkAnticipations()->create([
                'recipient_id' => self::getPagarMeRecipientId(),
                'build' => 'true',
                'payment_date' => $paymentDate->valueOf(),
                'requested_amount' => str_replace('.','',(string) number_format($request->amount, 2, '.', '.')),
                'timeframe' => 'start',
                'automatic_transfer' => true
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return response()->json($anticipation);
    }

    /**
     * Confirm antecipation
     * @param $antecipation_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmAntecipation($antecipation_id)
    {
        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
            $anticipation = $pagarme->bulkAnticipations()->confirm([
                'recipient_id' => self::getPagarMeRecipientId(),
                'bulk_anticipation_id' => $antecipation_id,
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return response()->json($anticipation);
    }

    /**
     * List antecipations
     * @return \Illuminate\Http\JsonResponse
     * @throws APIException
     */
    public function listAntecipations() {
        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
            $anticipations = $pagarme->bulkAnticipations()->getList([
                'recipient_id' => self::getPagarMeRecipientId()
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return response()->json($anticipations);
    }


    /**
     * Get pagar-me customer id
     * @return mixed
     * @throws APIException
     */
    public function getPagarMeRecipientId() {
        foreach( self::getClientRecipient()->original->gatewayRecipients as $cod=>$attr ) {
            if( $attr->gateway == 'pagarme' ) {
                return $attr->pgid;
            }
        }
        return null;
    }

    /**
     * Change the default recipient's bank account
     * @param Request $request
     * @return Response
     * @throws APIException
     */
    public function changeRecipientBankAccount(Request $request)
    {
        $mundipaggSplitService = new MundipaggSplitService(Auth::user()->platform_id);
        $client = $mundipaggSplitService->getClientRecipientIdOrCreate();

        try {
            $updateRequest = new UpdateRecipientBankAccountRequest();
            $updateRequest->bankAccount = $updateRequest->bankAccount ?? (object) [];

            $updateRequest->bankAccount->holder_name = $request->holder_name;

            if ($request->type_person == 'F') {
                $updateRequest->bankAccount->holder_type = 'individual';

                $document = str_replace('.', '', $request->document);
                $document = str_replace('-', '', $document);
                $updateRequest->bankAccount->holder_document = $document;
            } else {
                $updateRequest->bankAccount->holder_type = 'company';

                $document = str_replace('.', '', $request->document);
                $document = str_replace('/', '', $document);
                $document = str_replace('-', '', $document);
                $updateRequest->bankAccount->holder_document = $document;
            }

            $updateRequest->bankAccount->bank = $request->bank;
            $updateRequest->bankAccount->branch_number = $request->branch;
            $updateRequest->bankAccount->branch_check_digit = $request->branch_check_digit;
            $updateRequest->bankAccount->account_number = $request->account;
            $updateRequest->bankAccount->account_check_digit = $request->account_check_digit;
            $updateRequest->bankAccount->type = $request->account_type;

            $response = self::getRecipients()->updateRecipientDefaultBankAccount($client, $updateRequest);
        }
        catch(APIException $e) {
            return response()->json($e, 400);
        }

        return response()->json($response);
    }
}
