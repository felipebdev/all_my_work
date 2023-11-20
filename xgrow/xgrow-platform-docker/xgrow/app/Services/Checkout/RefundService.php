<?php

namespace App\Services\Checkout;

use App\Mail\SendMailRefund;
use App\PaymentPlan;
use App\Services\EmailService;
use DomainException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\StreamInterface;

class RefundService
{
    private CheckoutBaseService $checkoutBaseService;

    public function __construct(CheckoutBaseService $checkoutBaseService)
    {
        $this->checkoutBaseService = $checkoutBaseService;
    }

    /** Refund checkout service
     * @param  mixed  $data
     * @return StreamInterface
     * @throws BindingResolutionException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws DomainException
     * @throws GuzzleException
     */
    public function refund($data)
    {
        $req = $this->checkoutBaseService->connectionConfig(Auth::user()->platform_id, Auth::user()->id);
        $res = $req->post('refund', ['json' => $data]);
        return $res->getBody();
    }

    public function refundRequest(Request $request)
    {
        $type = $request->input('type');
        $paymentPlanId = $request->input('payment_plan_id');

        $data = $this->typeRefundFormat($type, $request);

        $this->refund($data);

        $this->sendRefundEmail($paymentPlanId);
    }

    /**
     * Return correct format for type of refund
     *
     * @param $type
     * @param $request
     * @return array
     */
    protected function typeRefundFormat($type, $request)
    {
        if ($type === 'boleto') {
            $data = [
                'payment_method' => $type,
                'metadata' => [],
                'reason' => $request->input('reason'),
                'bank_code' => $request->input('bank_code'),
                'agency' => $request->input('agency'),
                'agency_digit' => $request->input('agency_digit'),
                'account' => $request->input('account'),
                'account_digit' => $request->input('account_digit'),
                'account_type' => $request->input('account_type'),
                'document_number' => $request->input('document_number'),
                'legal_name' => $request->input('legal_name'),
            ];
        }

        if ($type === 'credit_card') {
            $data = [
                'payment_method' => $type,
                'reason' => $request->input('reason'),
                'metadata' => [],
                'refund_all' => !($request->input('single') === true)
            ];
        }

        if ($type === 'pix') {
            $data = [
                'payment_method' => $type,
                'reason' => $request->input('reason'),
                'metadata' => []
            ];
        }

        if ($request->has('payment_plan_id')) {
            //partial refund (refund only a single product)
            $data['payment_plan_id'] = $request->input('payment_plan_id');
        } elseif ($request->has('payment_id')) {
            //single refund (refund main product and order bumps)
            $data['payment_id'] = $request->input('payment_id');
        }

        return $data;
    }

    /**
     * Send email to subscriber
     *
     * @param  int  $paymentPlanId
     * @throws \Exception
     */
    private function sendRefundEmail(int $paymentPlanId): void
    {
        $paymentPlan = PaymentPlan::find($paymentPlanId);
        $payment = $paymentPlan->payment;
        $subscriber = $payment->subscriber;

        $mail = new SendMailRefund(
            $payment->platform_id,
            $subscriber,
            $paymentPlan,
            $payment->order_code,
            $payment->plans_value,
            null,
            $payment->updated_at
        );

        EmailService::mail([$subscriber->email], $mail);
    }
}
