<?php


namespace App\Services\Mundipagg;


use App\Coupon;
use App\CreditCard;
use App\Http\Controllers\Mundipagg\MundipaggCheckoutController;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Services\MundipaggService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\GetOrderResponse;

class CheckoutUnlimitedSaleService extends CheckoutOrderService
{

    const CODE_INSUFFICIENT_FUNDS = 1016;

    /**
     * @param CreateOrderRequest $request
     * @param Platform $platform
     * @param Plan $plan
     * @param Request $data
     * @param null $cardId
     * @return GetOrderResponse
     */
    public function createOrder(CreateOrderRequest $request, Platform $platform, Plan $plan, Request $data, Subscriber $subscriber)
    {
        //Get Order items
        $request->items = $this->getItems($plan);

        //Set metadata
        $creditCardData = $this->getCreditCardData($data);
        $request->metadata = $this->getUnlimitedOrderMetadata($plan, $creditCardData[0]['installment'], $data);

        //Order payments
        $request->payments = $this->getPaymentCreditCard($platform, $plan, $data, $subscriber);

        $mundipaggService = new MundipaggService($platform->id);
        $result = $mundipaggService->getClient()->getOrders()->createOrder($request);

        return $result;
    }

    public function getUnlimitedOrderMetadata($plan, $total_installments, Request $request = null)
    {
        $metadata = parent::getOrderMetadata($plan, $request);
        $metadata['unlimited_sale'] = true;
        $metadata['total_installments'] = $total_installments;
        $metadata['obs'] = "Venda sem limite (parcela 1 de ".$total_installments.")";
        return $metadata;
    }

    public function getCreditCardData(Request $request) {
        if( empty($request->cc_info) ) {
            throw new \Exception("Dados dos cartões inválidos");
        }
        return $request->cc_info;
    }


    /**
     * Get First payment for unlimited sale
     * @param Platform $platform
     * @param Plan $plan
     * @param Request|null $request
     * @param Subscriber|null $subscriber
     * @return array
     * @throws \Exception
     */
    public function getPaymentCreditCard(Platform $platform, Plan $plan, Request $request = null, Subscriber $subscriber = null)
    {
        $payments = array();
        $totalAmount = 0;

        $creditCardData = $this->getCreditCardData($request);

        //Total items amount
        $amount = 0;
        foreach ($this->getItems($plan) as $cod => $item) {
            $amount = $amount + ($item->amount / 100);
        }

        foreach ($creditCardData as $cod => $creditCardInfo) {
            //Order payments
            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->amount = $creditCardInfo['value'];
            $paymentRequest->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD;
            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->installments = 1;
            $paymentRequest->creditCard->operationType = 'auth_only';
            $paymentRequest->creditCard->capture = false;
            $paymentRequest->creditCard->cardId = CreditCard::findOrFail($subscriber->credit_card_id)->card_id;

            $totalAmount = $totalAmount + $creditCardInfo['value'];
            $amountWithInterest = $plan->getInstallmentValue($paymentRequest->amount, ($creditCardInfo['installment'] ?? 1));
            $paymentRequest->amount = str_replace('.', '', (string)number_format($amountWithInterest, 2, '.', '.'));

            //get payment split
            $splitService = new SplitService($platform->id);
            $split = $splitService->getPaymentSplit($amountWithInterest, ($creditCardInfo['value']/$creditCardInfo['installment']), $creditCardInfo['installment'], false);
            $paymentRequest->split = $split;
            $paymentRequest->metadata = $splitService->getPaymentMetadata();

            $payments[] = $paymentRequest;
        }

        $this->checkTotalValues($request, $plan, $totalAmount);

        return $payments;
    }

    /**
     * Store unlimited sale pending payments
     * @param Subscriber $subscriber
     * @param GetOrderResponse $result
     * @param Carbon $paymentDate
     * @param Payment $firstPayment
     * @param $installment_number
     * @return Payment
     */
    public static function storePendingPayment(
        Subscriber $subscriber,
        GetOrderResponse $result,
        Carbon $paymentDate,
        Payment $firstPayment,
        $installment_number,
        $clientTaxTransaction = 1.5,
        ?array $orderBumps = null
    ) {
        foreach($result->charges as $cod => $charge) {
            $payment = new Payment();
            $payment->platform_id = $subscriber->platform_id;
            $payment->subscriber_id = $subscriber->id;
            $payment->price = ($charge->paidAmount ?? $charge->amount)/100;
            $payment->payment_date = $paymentDate;
            $payment->status = Payment::STATUS_PENDING;
            $payment->customer_id = $result->customer->id;
            $payment->type_payment = $charge->paymentMethod;
            $payment->installments = $result->metadata['total_installments'];
            $payment->installment_number = $installment_number;
            $payment->type = Payment::TYPE_UNLIMITED;
            $payment->service_value = $firstPayment->service_value;
            $payment->customer_value = $firstPayment->customer_value;
            $coupon = new Coupon();
            if( isset( $result->metadata['cupom_id'] ) ) {
                $payment->coupon_id = $result->metadata['cupom_id'];
                $coupon = Coupon::findOrFail($result->metadata['cupom_id']);
            }
            $payment->customer_value = $charge->metadata['customer_value'];
            $payment->service_value = $charge->metadata['service_value'];
            $payment->plans_value = $charge->metadata['plans_value'];
            $payment->tax_value = $charge->metadata['tax_value'];
            $payment->order_number = $firstPayment->order_number ?? null;
            $payment->save();

            $payment->installments = (!empty($payment->installments)) ? $payment->installments : 1;

            //FIXME verificar se é upsell
            $mainPlan = Plan::findOrFail($charge->metadata['plan_id']);
            $mainPlanValue = $mainPlan->getPrice();
            $mainInstallmentPlanValue = $mainPlanValue / $payment->installments;
            $mainCoupon = $coupon->getDiscountValue($mainPlanValue);
            $mainInstallmentCouponValue = $mainCoupon /$payment->installments;
            $mainPrice = $mainPlan->getInstallmentValue(($mainPlanValue-$mainCoupon), $payment->installments);
            $mainCustomerValue = round((($subscriber->platform->client->percent_split / 100) * ($mainInstallmentPlanValue-$mainInstallmentCouponValue) - $clientTaxTransaction), 2);
            $mainTax = round(((((100-$subscriber->platform->client->percent_split) / 100) * ($mainInstallmentPlanValue-$mainInstallmentCouponValue)) + $clientTaxTransaction), 2);
            $plans[$mainPlan->id] = array('tax_value' => $mainTax, 'plan_value' => $mainInstallmentPlanValue-$mainInstallmentCouponValue, 'plan_price' => $mainPrice, 'coupon_id' => $coupon->id, 'coupon_code' => $coupon->code, 'coupon_value' => $mainInstallmentCouponValue, 'customer_value' => $mainCustomerValue,'type' => 'product');


            foreach($orderBumps as $obCode => $orderBump) {
                $orderBumpPlan = Plan::findOrFail($orderBump->id);
                $orderBumpValue = $orderBumpPlan->getPrice();
                $orderBumpPrice = $mainPlan->getInstallmentValue(($orderBumpValue), $payment->installments);
                $orderBumpCustomerValue = round(($subscriber->platform->client->percent_split / 100) * $orderBumpPlan->price, 2)/$payment->installments;
                $orderBumpTax = round((((100-$subscriber->platform->client->percent_split) / 100) * $orderBumpValue),2)/$payment->installments;
                $plans[$orderBumpPlan->id] = array('tax_value' => $orderBumpTax, 'plan_value' => $orderBumpValue, 'plan_price' => $orderBumpPrice, 'coupon_id' => null, 'coupon_code' => null, 'coupon_value' => 0, 'customer_value' => $orderBumpCustomerValue, 'type' => 'order_bump');
            }

            $payment->plans()->sync($plans);
        }

        return $payment;
    }
}
