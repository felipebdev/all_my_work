<?php

namespace App\Services\Mundipagg;

use MundiAPILib\Models\CreateCheckoutPaymentRequest;
use MundiAPILib\Models\CreatePaymentRequest;
use MundiAPILib\Models\CreateOrderItemRequest;
use MundiAPILib\Models\CreateOrderRequest;
use MundiAPILib\Models\CreateCreditCardPaymentRequest;
use MundiAPILib\Models\CreateCustomerRequest;
use Auth;
use App\Plan;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Services\MundipaggService;

class OrderService extends Controller
{

    private $ordersController;

    public function __construct($platform_id)
    {
        $mundipaggService = new MundipaggService($platform_id);
        $client = $mundipaggService->getClient();
        $this->ordersController = $client->getOrders();
    }

    public function store($course, $platformUrl)
    {
        $customer = new CreateCustomerRequest();
        $customer->name = "Digite aqui seu nome";

        $creditCard = new CreateCreditCardPaymentRequest();
        $creditCard->capture = true;
        $creditCard->installments = $course->plan->installment;

        $request = new CreateOrderRequest();

        $request->items = [
            new CreateOrderItemRequest(),
            new CreateOrderItemRequest()
        ];
        $request->items[0]->description = Plan::PLAN_ITEM_REGISTRATION;
        $request->items[0]->cycles = 1;
        $request->items[0]->quantity = 1;
        $request->items[0]->amount = str_replace('.', '', $course->plan->setup_price);
        $request->items[0]->code = $course->id;
        $request->items[0]->category = Plan::PLAN_ITEM_REGISTRATION;

        $request->items[1]->description = $course->name;
        $request->items[1]->quantity = 1;
        $request->items[1]->amount = str_replace('.', '', $course->plan->price);
        $request->items[1]->code = $course->id;
        $request->items[1]->category = Plan::ORDER_ITEM_CATEGORY_COURSE;

        $request->payments = [new CreatePaymentRequest()];
        $request->payments[0]->paymentMethod = "checkout";
        $request->payments[0]->amount = str_replace('.', '', $course->plan->price);
        $request->payments[0]->checkout = new CreateCheckoutPaymentRequest();
        $request->payments[0]->checkout->expiresIn = 120;
        $request->payments[0]->checkout->billingAddressWditable = false;
        $request->payments[0]->checkout->customerEditable = true;
        $request->payments[0]->checkout->acceptedPaymentMethods = ["credit_card"];
        $request->payments[0]->checkout->successUrl = $platformUrl;

        $request->customer = $customer;

        try {
            $result = $this->ordersController->createOrder($request);
            $url = ($result->checkouts[0]->status === 'open') ? $result->checkouts[0]->paymentUrl : "error";
            return ['status' => 'success', 'data' => $url];

        } catch (\Exception $e) {
            return ['status' => 'error', 'data' => $e];
        }
    }
}
