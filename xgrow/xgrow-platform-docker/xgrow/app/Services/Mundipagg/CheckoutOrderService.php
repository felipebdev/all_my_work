<?php


namespace App\Services\Mundipagg;


use App\Coupon;
use App\CreditCard;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\Mundipagg\CreditCardController;
use App\Http\Controllers\Mundipagg\MundipaggCheckoutController;
use App\Payment;
use App\Plan;
use App\Platform;
use App\Services\MundipaggService;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use MundiAPILib\Models\CreateAddressRequest;
use MundiAPILib\Models\CreateBoletoPaymentRequest;
use MundiAPILib\Models\CreateCardRequest;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreatePixPaymentRequest;
use MundiAPILib\Models\GetOrderResponse;
use MundiAPILib\Models\UpdateOrderStatusRequest;

class CheckoutOrderService
{

    protected $munditoken;
    protected $orderBumps;

    /**
     * @return mixed
     */
    public function getMunditoken()
    {
        return $this->munditoken;
    }

    /**
     * @param mixed $munditoken
     */
    public function setMunditoken($munditoken): void
    {
        $this->munditoken = $munditoken;
    }

    public function createTestOrder(CreateOrderRequest $request, Platform $platform, Plan $plan, $cardId, Request $data)
    {
        //Set metadata
        $request->metadata = $this->getOrderMetadata($plan, $data);

        //Test Item
        $itemPrice = new CreateOrderItemRequest();
        $itemPrice->description = "Validação do cartão";
        $itemPrice->quantity = 1;
        $itemPrice->amount = 500;
        $itemPrice->code = $plan->id;
        $itemPrice->category = Plan::ORDER_ITEM_CATEGORY_COURSE;
        $request->items = array($itemPrice);

        //Order payments
        $payments = [new CreatePaymentRequest()];
        $payments[0]->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD;
        $payments[0]->creditCard = new CreateCreditCardPaymentRequest();
        $payments[0]->amount = 500;
        $payments[0]->creditCard->installments = $plan->installment;
        $payments[0]->creditCard->statementDescriptor = 'Teste Xgrow';
        $payments[0]->creditCard->cardId = $cardId;

        //Create credit card address
        if( strlen($data->address_zipcode) > 0 ) {
            $payments[0]->creditCard->card = new CreateCardRequest();
            $payments[0]->creditCard->card->billingAddress = $this->getAddress($data);
        }
        $request->payments = $payments;

        $mundipaggService = new MundipaggService($platform->id);
        return $mundipaggService->getClient()->getOrders()->createOrder($request);
    }

    public function getOrderMetadata($plan, Request $request = null) {
        $metadata = array();
        foreach($this->getItems($plan) as $cod=>$item) {
            if( $item->category == 'product' ) {
                $metadata['plan_id'] = $item->code;
                $metadata['plan'] = $item->description;
                $metadata['value'] = $item->amount/100;
            }
            else { //order_bump
                $metadata['order_bump_plan_id'] = $item->code;
                $metadata['order_bump_plan'] = $item->description;
                $metadata['order_bump_value'] = $item->amount/100;
            }
        }
        if( $request ) {
            $cupom = $request->get('cupom');
            if( strlen($cupom) > 0 ) {
                $coupon = CouponController::findCoupon($plan->platform_id, $plan->id, $cupom);
                if( $coupon ) {
                    $metadata['cupom'] = $cupom;
                    $metadata['cupom_id'] = $coupon->id;
                }
            }
        }
        return $metadata;
    }

    public function createOrder(CreateOrderRequest $request, Platform $platform, Plan $plan, Request $data, Subscriber $subscriber)
    {
        //Get Order items
        $request->items = $this->getItems($plan);

        //Set metadata
        $request->metadata = $this->getOrderMetadata($plan, $data);

        //Order payments
        $request->closed = true;
        if( $data->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD ) {
            $request->closed = false;
            $request->payments = $this->getPaymentCreditCard($platform, $plan, $data, $subscriber);
        }
        else
        {
            $request->payments = array($this->getPayment($platform, $plan, $data));
        }

        $mundipaggService = new MundipaggService($platform->id);
        $order = $mundipaggService->getClient()->getOrders()->createOrder($request);

        //Credit card
        if( $data->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD ) {

            //Store credit card
            if( isset($order->charges) ) {
                foreach ($order->charges as $cod => $charge) {
                    if ($charge->paymentMethod == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD) {
                        if ($charge->lastTransaction) {
                            if ($charge->lastTransaction->card) {
                                CreditCardController::save($subscriber, $charge->lastTransaction->card);
                            }
                        }
                    }
                }
            }

            //Unlimited sale
            if ($plan->unlimited_sale == true && $plan->type_plan == Plan::PLAN_TYPE_SALE && count($request->payments) == 1) {
                if (isset($order->charges)) {
                    //if insufficient founds error code and unlimited sale is enabled
                    foreach ($order->charges as $cod => $charge) {
                        if ($charge->lastTransaction->acquirerReturnCode == CheckoutUnlimitedSaleService::CODE_INSUFFICIENT_FUNDS) {
                            $checkoutUnlimitedSale = new CheckoutUnlimitedSaleService();
                            $checkoutUnlimitedSale->setOrderBumps($this->getOrderBumps());
                            $order = $checkoutUnlimitedSale->createOrder($request, $platform, $plan, $data, $subscriber);
                        }
                    }
                }
            }
        }

        //Capture and confirm credit card
        if( $order->status == 'pending' && $data->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD ) {
            $order = $this->confirmCreditCard($mundipaggService, $order);
        }

        return $order;
    }

    public function confirmCreditCard(MundipaggService $mundipaggService, GetOrderResponse $order)
    {
        $capture = true;
        if (isset($order->charges)) {
            foreach ($order->charges as $cod => $charge) {
                if ($charge->lastTransaction->status == 'authorized_pending_capture') {
                    $capture &= true;
                } else {
                    $capture &= false;
                }
            }
            if ($capture) {
                //Capture charges
                foreach ($order->charges as $cod => $charge) {
                    $mundipaggService->getClient()->getCharges()->captureCharge($charge->id);
                }
                //Set paid order
                $orderCancelStatus = new UpdateOrderStatusRequest();
                $orderCancelStatus->status = 'paid';
                $mundipaggService->getClient()->getOrders()->updateOrderStatus($order->id, $orderCancelStatus);
            } else {

                //Cancel authorized charges
                foreach ($order->charges as $cod => $charge) {
                    if ($charge->lastTransaction->status == 'authorized_pending_capture') {
                        $mundipaggService->getClient()->getCharges()->cancelCharge($charge->id);
                    }
                }

                //Cancel order
                $orderCancelStatus = new UpdateOrderStatusRequest();
                $orderCancelStatus->status = 'failed';
                $mundipaggService->getClient()->getOrders()->updateOrderStatus($order->id, $orderCancelStatus);
            }
        }
        return $mundipaggService->getClient()->getOrders()->getOrder($order->id);
    }


    public function getItems(Plan $plan, $parcel_number = 1)
    {
        if ($plan->price > 0) {
            $itemPrice = new CreateOrderItemRequest();
            $itemPrice->description = $plan->name;
            $itemPrice->quantity = 1;
            $itemPrice->amount = str_replace('.', '', $plan->getPrice($parcel_number));
            $itemPrice->code = $plan->id;
            $itemPrice->category = 'product';
            $items[] = $itemPrice;
        }
        $orderBumps = (array)$this->getOrderBumps();
        foreach($orderBumps as $cod => $orerBumpPlan) {
            $itemOrderBump = new CreateOrderItemRequest();
            $itemOrderBump->description = $orerBumpPlan->name;
            $itemOrderBump->quantity = 1;
            $itemOrderBump->amount = str_replace('.', '', $orerBumpPlan->getPrice($parcel_number));
            $itemOrderBump->code = $orerBumpPlan->id;
            $itemOrderBump->category = 'order_bump';
            $items[] = $itemOrderBump;
        }
        return $items;
    }

    public function getPaymentCreditCard(Platform $platform, Plan $plan, Request $request = null, Subscriber $subscriber = null)
    {
        $payments = array();
        $totalAmount = 0;

        if( empty($request->cc_info) ) {
            throw new \Exception("Dados dos cartões inválidos");
        }

        foreach ($request->cc_info as $cod => $creditCardInfo) {
            //Order payments
            $paymentRequest = new CreatePaymentRequest();
            $paymentRequest->amount = $creditCardInfo['value'];
            $paymentRequest->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD;

            $paymentRequest->creditCard = new CreateCreditCardPaymentRequest();
            $paymentRequest->creditCard->statementDescriptor = Str::limit(Str::camel(removeAccentsAndEspecialChars($platform->name)), 13, '');
            $paymentRequest->creditCard->installments = $creditCardInfo['installment'] ?? 1;
            $paymentRequest->creditCard->operationType = 'auth_only';
            $paymentRequest->creditCard->capture = false;

            //For upsell and multiple cards send las four digits and brand card
            if( isset($creditCardInfo['last_four_digits']) && isset($creditCardInfo['brand']) ) {
                $findCard = CreditCard::where('subscriber_id', '=', $subscriber->id)->where('brand', '=', $creditCardInfo['brand'])->where('last_four_digits', '=', $creditCardInfo['last_four_digits'])->first();
                if( $findCard ) {
                    $paymentRequest->creditCard->cardId = $findCard->card_id;
                }
            }
            else
            {
                $paymentRequest->creditCard->cardToken = $creditCardInfo['token'];
            }

            $totalAmount = $totalAmount + $creditCardInfo['value'];

            $amountWithInterest = ($creditCardInfo['installment'] ?? 1) * $plan->getInstallmentValue($paymentRequest->amount, ($creditCardInfo['installment'] ?? 1));
            $paymentRequest->amount = str_replace('.', '', (string)number_format($amountWithInterest, 2, '.', '.'));

            //get payment split
            $splitService = new SplitService($platform->id);
            $split = $splitService->getPaymentSplit($amountWithInterest, $creditCardInfo['value'], $creditCardInfo['installment']);
            $paymentRequest->split = $split;
            $paymentRequest->metadata = $splitService->getPaymentMetadata();

            $payments[] = $paymentRequest;
        }

        $this->checkTotalValues($request, $plan, round($totalAmount, 2));

        return $payments;
    }

    /**
     * Verifica se o total informado nos cartões não é diferente do total do pedido
     * @param Plan $plan
     * @param $totalAmount
     * @throws \Exception
     */
    public function checkTotalValues(Request $request, Plan $plan, $totalAmount)
    {
        //verificar se o total amount não é diferente do valor do pedido
        $amount = $this->getTotalAmount($request, $plan);

        if ($amount <> $totalAmount) {
            throw new \Exception("O valor total dos cartões deve ser igual valor total da compra");
        }
    }

    public function getPayment(Platform $platform, Plan $plan, Request $data = null, $cardId = null, $split = true, $parcel_number = 1)
    {
        //Order payments
        $payment = new CreatePaymentRequest();

        if( $data->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_BOLETO ) //Boleto
        {
            $payment->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_BOLETO;
            $payment->boleto = new CreateBoletoPaymentRequest();
            $payment->boleto->instructions = "Pagar até o vencimento";
            $payment->boleto->dueAt = Carbon::now()->addWeekdays(2); //Vencimento em 2 dias úteis
            $data->installmentSelected = 1; //Se boleto força numero de parcelas para 1
        }
        elseif ( $data->payment_method == MundipaggCheckoutController::PAYMENT_METHOD_PIX )
        {
            $payment->paymentMethod = MundipaggCheckoutController::PAYMENT_METHOD_PIX;
            $payment->pix = new CreatePixPaymentRequest();
            $payment->pix->expiresIn = 1200; //Tempo de validade do pix em segundos = 20 minutos;

        }

        //Sum items amount
        $amount = $this->getTotalAmount($data, $plan);
        $amountWithInterest = ($data->installmentSelected > 0 ? $data->installmentSelected : 1)*$plan->getInstallmentValue($amount, $data->installmentSelected);
        $payment->amount = str_replace('.','',(string) number_format($amountWithInterest, 2, '.', '.'));

        if( $split )
        {
            //get payment split
            $splitService = new SplitService($platform->id);
            $split = $splitService->getPaymentSplit($amountWithInterest, $amount, $data->installmentSelected);
            $payment->split = $split;
            $payment->metadata = $splitService->getPaymentMetadata();
        }

        return $payment;
    }

    public function getTotalAmount(Request $request, Plan $plan, $parcel_number = null) {
        $amount = 0;
        foreach($this->getItems($plan, $parcel_number) as $cod=>$item) {
            $itemValue = round(($item->amount/100),2);

            //If main product check coupon
            if( $item->category == 'product' ) {
                if( strlen($request->cupom) > 0 ) {
                    $cupom = $this->getCupom($request);
                    if( $cupom ) {
                        $itemValue = $itemValue - $cupom->getDiscountValue($itemValue);
                    }
                }
            }
            $amount = $amount+$itemValue;
        }

        return round($amount,2);
    }

    public function getCupom(Request $request) {
        $coupon = CouponController::findCoupon($request->platform_id, $request->plan_id, $request->cupom);
        if( $coupon ) {
            $subscriber = Subscriber::findOrFail($request->subscriber_id);
            if( CouponController::isAvailable($coupon, $subscriber->email) ) {
                return $coupon;
            }
            else
            {
                throw new \Exception("Cupom não disponível");
            }
        }
        throw new \Exception("Cupom não encontrado");
    }

    public static function storePayment(
        string $paymentSource,
        Subscriber $subscriber,
        GetOrderResponse $result,
        Carbon $paymentDate,
        ?string $orderNumber,
        $clientTaxTransaction,
        ?array $orderBumps = null,
        ?Payment $originalFailedPayment = null
    ) {
        $paymentDate = clone $paymentDate; //date only into this scope

        //Check multiple means
        $multiple_means = false;
        if(count($result->charges) > 1) {
            $multiple_means = true;
        }

        foreach($result->charges as $cod => $charge) {

            $payment = new Payment();
            $payment->platform_id = $subscriber->platform_id;
            $payment->subscriber_id = $subscriber->id;
            $payment->price = ($charge->paidAmount ?? $charge->amount)/100;
            $payment->payment_date = $paymentDate;
            $payment->status = ($paymentDate->isFuture() && $charge->paymentMethod == MundipaggCheckoutController::PAYMENT_METHOD_CREDIT_CARD ? Payment::STATUS_PENDING : $result->status);
            $payment->order_id = $result->id;
            $payment->charge_id = $charge->id;
            $payment->customer_id = $result->customer->id;
            $payment->charge_code = $charge->code;
            $payment->order_code = $result->code;
            $payment->type_payment = $charge->paymentMethod;
            $payment->payment_source = $paymentSource;
            $payment->installment_number = 1;
            $payment->installments = $charge->lastTransaction->installments ?? 1;
            $payment->multiple_means = $multiple_means;
            if( isset($result->metadata['total_installments']) ) {
                $payment->installments = $result->metadata['total_installments'];
            }

            //Set payment type (Simple sale, Unlimited sale or Subscription)
            $mainPlan = Plan::findOrFail($charge->metadata['plan_id'] ?? $subscriber->plan_id);
            $payment->type = ($mainPlan->type_plan == Plan::PLAN_TYPE_SALE ? Payment::TYPE_SALE : Payment::TYPE_SUBSCRIPTION);
            if( isset($result->metadata) ) {
                if( isset($result->metadata['unlimited_sale']) ) {
                    if ($result->metadata['unlimited_sale'] == true) {
                        $payment->type = Payment::TYPE_UNLIMITED;
                    }
                }
            }
            $coupon = new Coupon();
            if( isset( $result->metadata['cupom_id'] ) ) {
                $payment->coupon_id = $result->metadata['cupom_id'];
                $coupon = Coupon::findOrFail($result->metadata['cupom_id']);
                $coupon->usage = $coupon->usage + 1;
                $coupon->save();
            }

            $payment->customer_value = $charge->metadata['customer_value'] ?? null;
            $payment->service_value = $charge->metadata['service_value'] ?? null;
            $payment->plans_value = $charge->metadata['plans_value'] ?? null;
            $payment->tax_value = $charge->metadata['tax_value'] ?? null;
            $payment->antecipation_value = $charge->metadata['antecipation_value'] ?? null;
            $payment->order_number = $orderNumber;

            if ( $charge->paymentMethod == MundipaggCheckoutController::PAYMENT_METHOD_BOLETO ) {
                $payment->boleto_barcode = $charge->lastTransaction->barcode;
                $payment->boleto_qrcode = $charge->lastTransaction->qrCode;
                $payment->boleto_pdf = $charge->lastTransaction->pdf;
                $payment->boleto_url = $charge->lastTransaction->url;
                $payment->boleto_line = $charge->lastTransaction->line;
            }

            if( $charge->paymentMethod == MundipaggCheckoutController::PAYMENT_METHOD_PIX ) {
                $payment->pix_qrcode = $charge->lastTransaction->qrCode;
                $payment->pix_qrcode_url = $charge->lastTransaction->qrCodeUrl;
            }

            if (!is_null($originalFailedPayment)) {
                $payment->payment_id = $originalFailedPayment->id; // save retry reference
            }

            $payment->save();

            //FIXME verificar se é upsell
            $mainPlanValue = $mainPlan->getPrice();
            $mainCoupon = $coupon->getDiscountValue($mainPlanValue);
            $mainPrice = $mainPlan->getInstallmentValue(($mainPlanValue-$mainCoupon), $payment->installments)*$payment->installments;

            $customerValue = ($subscriber->platform->client->percent_split / 100) * ($mainPlanValue-$mainCoupon);
            $taxValue = ((100-$subscriber->platform->client->percent_split) / 100) * ($mainPlan->price-$mainCoupon);
            $mainCustomerValue = ($payment->type == Payment::TYPE_UNLIMITED) ?
                round($customerValue, 2) :
                round($customerValue - $clientTaxTransaction, 2);

            $mainTax = ($payment->type == Payment::TYPE_UNLIMITED) ?
                round($taxValue, 2) :
                round($taxValue + $clientTaxTransaction, 2);

            if (round((($mainCustomerValue + $mainTax) - $mainPlanValue), 2) == 0.01) {
                $mainTax = $mainTax - 0.01;
            }

            if( $payment->type == Payment::TYPE_UNLIMITED ) {
                $mainTax = ($mainTax/$payment->installments) + $clientTaxTransaction;
                $mainPrice = $mainPrice/$payment->installments;
                $mainCustomerValue = ($mainCustomerValue/$payment->installments) - $clientTaxTransaction;
                $mainCoupon = $mainCoupon/$payment->installments;
                $mainPlanValue = $mainPlanValue/$payment->installments;
            }
            $plans[$mainPlan->id] = array('tax_value' => round($mainTax/count($result->charges),2), 'plan_value' => $mainPlanValue-$mainCoupon, 'plan_price' => round($mainPrice/count($result->charges),2), 'coupon_id' => $coupon->id, 'coupon_code' => $coupon->code, 'coupon_value' => round($mainCoupon/count($result->charges),2) ?? 0, 'customer_value' => round($mainCustomerValue/count($result->charges),2),'type' => 'product');

            //Get order bumps plans values
            $plans = self::getOrderBumpsPlans($orderBumps, $mainPlan, $payment, $subscriber->platform->client->percent_split, $result, $plans);
            $payment->plans()->sync($plans);
        }

        return $payment;
    }

    private static function getOrderBumpsPlans($orderBumps, $mainPlan, $payment, $percentSplit, $result, $return) {
        //Save order bumps plan_rice
        if (is_array($orderBumps)) {
            foreach ($orderBumps as $obCode => $orderBump) {
                $orderBumpValue = $orderBump->getPrice();
                $orderBumpPrice = $mainPlan->getInstallmentValue(($orderBumpValue), $payment->installments) * $payment->installments;
                $orderBumpCustomerValue = round(($percentSplit / 100) * $orderBump->price, 2);
                $orderBumpTax = round((((100 - $percentSplit) / 100) * $orderBumpValue), 2);

                //For unlimited sell divide values for parcels number
                if ($payment->type == Payment::TYPE_UNLIMITED) {
                    $orderBumpTax = $orderBumpTax / $payment->installments;
                    $orderBumpPrice = $orderBumpPrice / $payment->installments;
                    $orderBumpCustomerValue = $orderBumpCustomerValue / $payment->installments;
                }
                //Store payment_plan for order_bump
                $return[$orderBump->id]['tax_value'] = round($orderBumpTax / count($result->charges), 2);
                $return[$orderBump->id]['plan_value'] = $orderBumpValue;
                $return[$orderBump->id]['plan_price'] = round($orderBumpPrice / count($result->charges), 2);
                $return[$orderBump->id]['coupon_id'] = null;
                $return[$orderBump->id]['coupon_code'] = null;
                $return[$orderBump->id]['coupon_value'] = 0;
                $return[$orderBump->id]['customer_value'] = round($orderBumpCustomerValue / count($result->charges), 2);
                $return[$orderBump->id]['type'] = 'order_bump';
            }
        }
        return $return;
    }

    public function cancelCharge(GetOrderResponse $result, $platform_id) {
        $mundipaggService = new MundipaggService($platform_id);
        $mundipaggService->getClient()->getCharges()->cancelCharge($result->charges[0]->id);
    }

    /**
     * @return mixed
     */
    public function getOrderBumps()
    {
        return $this->orderBumps;
    }

    /**
     * @param mixed $orderBumps
     */
    public function setOrderBumps($orderBumps): void
    {
        $this->orderBumps = $orderBumps;
    }

    public function getAddress($request) {
        $address = new CreateAddressRequest();
        $address->zipCode = preg_replace('/[^0-9]/', '', $request->address_zipcode);
        //$address->number = $request->address_number;
        $address->city = $request->address_city;
        //$address->complement = $request->address_comp;
        $address->country = 'BR';
        //$address->neighborhood = $request->address_district;
        $address->state = $request->address_state;
        //$address->street = $request->address_street;
        $address->line1 = ( $request->address_number ?? '0' ) . ', '. ( $request->address_street ?? 'Rua não informada' ) .', '. ($request->address_district ?? 'Bairro não informado');
        return $address;
    }

}
