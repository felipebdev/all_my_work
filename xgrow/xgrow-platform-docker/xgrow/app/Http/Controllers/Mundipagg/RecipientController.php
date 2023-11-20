<?php


namespace App\Http\Controllers\Mundipagg;

use App\Helpers\CollectionHelper;
use App\Http\Traits\CustomResponseTrait;
use App\Services\Checkout\CheckoutBaseService;
use Exception;
use App\Client;
use App\Platform;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use MundiAPILib\APIException;
use App\Services\MundipaggService;
use Illuminate\Support\Facades\Auth;
use App\Services\Mundipagg\SplitService;
use ErrorException;
use MundiAPILib\Models\CreateWithdrawRequest;
use MundiAPILib\Models\UpdateTransferSettingsRequest;
use MundiAPILib\Models\UpdateRecipientBankAccountRequest;
use MundiAPILib\Models\UpdateAutomaticAnticipationSettingsRequest;

/**
 * This is the legacy controller for financial actions (direct access to Payment Gateway).
 *
 * @deprecated Methods should be continuously replaced by {{@see \App\Http\Controllers\Financial\BankingController}}
 */
class RecipientController
{
    use CustomResponseTrait;

    protected static int $transferTaxAmount = 367;

    public static function convertPagarmeStatusToMundipagg($status): ?string
    {
        return [
            'pending_transfer' => 'pending',
            'transferred' => 'transferred',
            'failed' => 'failed',
            'processing' => 'processing',
            'canceled' => 'canceled',
        ][$status] ?? null;
    }

    private static function getRecipients()
    {
        $mundipaggService = new MundipaggService(Auth::user()->platform_id);
        return $mundipaggService->getClient()->getRecipients();
    }

    private static function getRecipientId()
    {
        $platform = Platform::findOrFail(Auth::user()->platform_id);

        return $platform->recipient_id ?? Client::where('id', $platform->customer_id)->first()->recipient_id;
    }

    /**
     * Get recipient data
     *
     * @deprecated Controller method replaced by {@see getBankAccountData()}
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function getClientRecipient(){
        $recipientId = self::getRecipientId();
        if(!empty($recipientId))
            return self::getRecipients()->getRecipient($recipientId);
        return null;
    }

    /**
     * List recipient withdrawals
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function listWithdrawals()
    {
        $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
        $result = $pagarme->transfers()->getList(['recipient_id' => self::getPagarMeRecipientId()]);

        $resultCollection = collect($result);

        $final = $resultCollection->map(fn(\stdClass $item) => [
            'id'  => $item->id,
            'amount' => $item->amount,
            'fee' => $item->fee,
            'created_at' => $item->date_created,
            'status' => self::convertPagarmeStatusToMundipagg($item->status),
        ]);

        return $final;
    }

    public function listWithdrawalsDatatablesData(Request $request)
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $page = $request->input('page', 1);
        $data = [];
        $totalPages = 1;
        $records = 0;

        try {

            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));

            $recipientId = self::getPagarMeRecipientId();

            if($recipientId){

                $result = $pagarme->transfers()->getList([
                    'recipient_id' => $recipientId,
                    'page' => $page,
                    'count' => $length,
                ]);

                $resultCollection = collect($result);

                $final = $resultCollection->map(fn(\stdClass $item) => [
                    'id'  => $item->id,
                    'amount' => $item->amount,
                    'fee' => $item->fee,
                    'created_at' => $item->date_created,
                    'status' => self::convertPagarmeStatusToMundipagg($item->status),
                ]);

                $totalPages = ceil($final->count() / $length);

                $data = $final->toArray();

                $records = $final->count();

            }

        }
        catch (Exception $e) {
            Log::warning('Falha ao obter lista de saques', [
                'exception' => $e,
            ]);

            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        }

        $datatables = [
            'data' => $data,
            'input' => [
                'length' => $length,
                'start' => $start
            ],
            'recordsFiltered' => $records,
            'recordsTotal' => $records,
            'totalPages' => $totalPages
        ];

        return response()->json($datatables);
    }

    public function listWithdrawalsClient(Request $request): JsonResponse
    {
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $page = $request->input('page', 1);
        $data = [];
        $totalPages = 1;
        $records = 0;

        try {
            $filters = [];
            if ($request->dateRange !== null) {
                $dateRange = explode('|', $request->dateRange);

                $filters['created_before'] = strtotime($dateRange[1] . " 23:59:59");
                $filters['created_after'] = strtotime($dateRange[0] . " 00:00:00");
            }

            $data = $this->listWithdrawalsCheckout($filters);

            $results = collect($data['data']);

            $final = $results->map(fn(\stdClass $item) => [
                'id'  => $item->id,
                'amount' => $item->amount,
                'fee' => '',
                'created_at' => $item->created_at,
                'status' => $item->status,
            ]);

            $totalPages = ceil($final->count() / $length);

            $data = $final->toArray();

            $records = $final->count();
            
        } catch (\Exception $e) {
            return $this->customJsonResponse('Falha ao realizar ação.', 400, ['errors' => $e->getMessage()]);
        }

        $datatables = [
            'data' => $data,
            'input' => [
                'length' => $length,
                'start' => $start
            ],
            'recordsFiltered' => $records,
            'recordsTotal' => $records,
            'totalPages' => $totalPages
        ];

        return response()->json($datatables);
    }

    /**
     * @param $filters
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listWithdrawalsCheckout($filters = [])
    {
        try {
            $filters['count'] = 1000;
            $res = $this->checkoutBaseService()->get('transfers', ['query' => $filters]);
            return ['data' => json_decode($res->getBody()), 'code' => 200];
        } catch (ClientException $e) {
            return ['data' => json_decode($e->getResponse()->getBody()->getContents())->message, 'code' => 400];
        }
    }

    public function checkoutBaseService()
    {
        $checkoutBaseService = new CheckoutBaseService;

        return $checkoutBaseService->connectionConfig(
            Auth::user()->platform_id ?? request()->route()->parameters()['platformId'],
            Auth::user()->id,
            ['acting_as' => 'client']
        );
    }

    /**
     * Get recipient withdrawal
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function getWithdrawal($withdrawal_id)
    {
        try {
            $witdraw = self::getRecipients()->getWithdrawById(self::getRecipientId(), $withdrawal_id);
        } catch (APIException $e) {
            return response()->json($e, 400);
        }
        return $witdraw;
    }

    /**
     * Send withdrawal request
     * @param Request $witdrawRequest
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function sendWithdrawal(Request $request)
    {
        $translations = [
            '' => 'Erro ao realizar saque',
            'The withdraw could not be created.' => 'O saque não pôde ser efetuado.',
            'The request is invalid.' => 'A requisição é inválida.',
            'A non well formed numeric value encountered' => 'Um valor numérico mal formado encontrado',
        ];

        try {
            $withdrawRequest = new CreateWithdrawRequest();

            $requestedAmount = (int) number_format($request->amount, 2, '', '');

            $withdrawRequest->amount = $requestedAmount - self::$transferTaxAmount;

            $withdraw = self::getRecipients()->createWithdraw(self::getRecipientId(), $withdrawRequest);

            return $withdraw;
        } catch (APIException | ErrorException $e) {
            $newE = [
                'message' => $translations[$e->getMessage()] ?? $e->getMessage(),
                'exception' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            Log::debug('RecipientController::sendWithdrawal exception', array_merge($newE, [
                'original_message' => $e->getMessage(),
                'request' => $request->all(),
            ]));

            return response()->json((object)$newE, 400);
        }
    }

    /**
     * Set recipient transfer settings
     * @param $transfer_enabled
     * @param $transfer_interval
     * @param $transfer_day
     * @return JsonResponse
     * @throws \MundiAPILib\APIException
     */
    public function setTransferSettings(Request $request)
    {

        try {
            $transferSettings = new UpdateTransferSettingsRequest();
            $transferSettings->transferEnabled = $request->transfer_enabled;
            $transferSettings->transferInterval = $request->transfer_interval;
            $transferSettings->transferDay = $request->transfer_day;
            $transfer = self::getRecipients()->updateRecipientTransferSettings(self::getRecipientId(), $transferSettings);
        } catch (APIException $e) {
            return response()->json($e, 400);
        }

        return $transfer;
    }

    public function setAutomaticAnticipationSettings(Request $request)
    {

        try {
            $anticipationSettings = new UpdateAutomaticAnticipationSettingsRequest();
            $anticipationSettings->enabled = $request->enabled;
            $anticipationSettings->days = explode(',', $request->days);
            $anticipationSettings->delay = $request->delay;
            $anticipationSettings->type = $request->type;
            $anticipationSettings->volumePercentage = $request->volumePercentage;
            $transfer = self::getRecipients()->updateAutomaticAnticipationSettings(self::getRecipientId(), $anticipationSettings);
        } catch (APIException $e) {
            return response()->json($e, 400);
        }

        return $transfer;
    }

    /**
     * @deprecated Anticipation is not used anymore
     *
     * Get antecipation's limit
     * @return JsonResponse
     */
    public function getAntecipationLimits()
    {
        $paymentDate = Carbon::now()->nextWeekday()->setTime(8, 0);
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
     * @return JsonResponse
     */
    public function sendAntecipation(Request $request)
    {
        $paymentDate = Carbon::now()->nextWeekday()->setTime(8, 0);

        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
            $anticipation = $pagarme->bulkAnticipations()->create([
                'recipient_id' => self::getPagarMeRecipientId(),
                'build' => 'true',
                'payment_date' => $paymentDate->valueOf(),
                'requested_amount' => str_replace('.', '', (string) number_format($request->amount, 2, '.', '.')),
                'timeframe' => 'start',
                'automatic_transfer' => true
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }

        return $anticipation;
    }

    /**
     * Confirm antecipation
     * @param $antecipation_id
     * @return JsonResponse
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

        return $anticipation;
    }

    /**
     * List antecipations
     * @return JsonResponse
     * @throws APIException
     */
    public function listAntecipations()
    {
        try {
            $pagarme = new \PagarMe\Client(env('PAGARME_API_KEY'));
            $anticipations = $pagarme->bulkAnticipations()->getList([
                'recipient_id' => self::getPagarMeRecipientId()
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
        return $anticipations;
    }


    /**
     * Get pagar-me customer id
     * @return mixed
     * @throws APIException
     * @throws Exception
     */
    public function getPagarMeRecipientId()
    {
        $recipient = $this->getClientRecipient();

        if($recipient) {
            foreach ($recipient->gatewayRecipients as $attr) {
                if ($attr->gateway == 'pagarme')
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
        $splitService = new SplitService(Auth::user()->platform_id);
        $client = $splitService->getClientRecipient();

        try {
            $updateRequest = new UpdateRecipientBankAccountRequest();
            $updateRequest->bankAccount = $updateRequest->bankAccount ?? (object)[];

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
        } catch (APIException $e) {
            return response()->json($e, 400);
        }

        return $response;
    }
}
