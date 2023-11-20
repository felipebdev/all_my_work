<?php

namespace App\Http\Controllers\Api\Students;

use App\Exceptions\Finances\PaymentChange\PaymentChangeInvalidException;
use App\Exceptions\Finances\PaymentChange\PaymentChangeNotAllowedException;
use App\Facades\JwtStudentsFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Student\StudentPaymentChangeRequest;
use App\Http\Traits\CustomResponseTrait;
use App\Payment;
use App\Services\Finances\PaymentChange\PaymentChangeService;
use Illuminate\Http\Response;
use MundiAPILib\APIException;
use MundiAPILib\Exceptions\ErrorException;

class StudentsPaymentController extends Controller
{

    use CustomResponseTrait;

    private PaymentChangeService $paymentChangeService;

    public function __construct(PaymentChangeService $paymentChangeService)
    {
        $this->paymentChangeService = $paymentChangeService;
    }

    public function change(StudentPaymentChangeRequest $request)
    {
        $subscriberIds = JwtStudentsFacade::getSubscribersIds();

        $paymentId = $request->payment_id ?? null;
        $paymentMethod = $request->payment_method ?? null;
        $ccInfo = $request->cc_info ?? null;

        $payment = Payment::query()
            ->where('id', $paymentId)
            ->whereIn('subscriber_id', $subscriberIds)
            ->first();

        if (!$payment) {
            $this->customAbort('Payment not belongs to subscriber', Response::HTTP_NOT_FOUND);
        }

        if ($this->paymentChangeService->hasReachedLimit($subscriberIds)) {
            $message = 'Você só pode realizar três alterações a cada 24h, por favor aguarde';
            $this->customAbort($message, Response::HTTP_TOO_MANY_REQUESTS);
        }

        try {
            $this->paymentChangeService->changePaymentMethodOrCardInfo($payment, $paymentMethod, $ccInfo ?? []);
            return $this->customJsonResponse('ok');
        } catch (PaymentChangeInvalidException $e) {
            $this->customAbort('Nova forma de pagamento deve ser diferente da atual', Response::HTTP_CONFLICT);
        } catch (PaymentChangeNotAllowedException $e) {
            $this->customAbort('Não é permitido alterar cartão de crédito para boleto/PIX', Response::HTTP_BAD_REQUEST);
        } catch (ErrorException $e) {
            $this->customAbort('Falha no Gateway de pagamentos: '. $e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (APIException $e) {
            $this->customAbort('Falha de comunicação com o Gateway de pagamentos', Response::HTTP_SERVICE_UNAVAILABLE);
        }

    }
}
