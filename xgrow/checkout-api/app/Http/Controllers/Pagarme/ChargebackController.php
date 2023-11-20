<?php

namespace App\Http\Controllers\Pagarme;

use App\Http\Controllers\Controller;
use App\Jobs\PagarmeChargebackUpdate;

class ChargebackController extends Controller
{
    public function chargebackData()
    {
        PagarmeChargebackUpdate::dispatch();
    }

    private function fakeChargebackAPI()
    {
        //    "object": "chargeback",
        //    "id": "cb_cko8ki0jh0ry10hdimqh2scqj",
        //    "installment": 2,
        //    "transaction_id": 307554919,
        //    "amount": 10828,
        //    "reason_code": "4837",
        //    "card_brand": "mastercard",
        //    "updated_at": "2021-05-03T12:17:00.221Z",
        //    "created_at": "2021-05-03T12:17:00.221Z",
        //    "date_updated": "2021-05-03T12:17:00.221Z",
        //    "date_created": "2021-05-03T12:17:00.221Z",
        //    "accrual_date": "2021-05-02T03:00:00.000Z",
        //    "status": "presented",
        //    "cycle": 1
        $data = '[{"object":"chargeback","id":"cb_cko8ki0jh0ry10hdimqh2scqj","installment":2,"transaction_id":12187024,"amount":10828,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-03T12:17:00.221Z","created_at":"2021-05-03T12:17:00.221Z","date_updated":"2021-05-03T12:17:00.221Z","date_created":"2021-05-03T12:17:00.221Z","accrual_date":"2021-05-02T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko8kgecm0ro20hdixfi7peux","installment":3,"transaction_id":307554919,"amount":10828,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-03T12:15:44.807Z","created_at":"2021-05-03T12:15:44.807Z","date_updated":"2021-05-03T12:15:44.807Z","date_created":"2021-05-03T12:15:44.807Z","accrual_date":"2021-05-02T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko8kf5lq0rgy0hdisxbh5z3z","installment":4,"transaction_id":307554919,"amount":10828,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-03T12:14:46.814Z","created_at":"2021-05-03T12:14:46.814Z","date_updated":"2021-05-03T12:14:46.814Z","date_created":"2021-05-03T12:14:46.814Z","accrual_date":"2021-05-02T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko8kdafi0qkt0hdi2j6yb1hq","installment":1,"transaction_id":307554919,"amount":10828,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-03T12:13:19.758Z","created_at":"2021-05-03T12:13:19.758Z","date_updated":"2021-05-03T12:13:19.758Z","date_created":"2021-05-03T12:13:19.758Z","accrual_date":"2021-05-02T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko8kbily0quu0hdied94abr5","installment":5,"transaction_id":307554919,"amount":10828,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-03T12:11:57.046Z","created_at":"2021-05-03T12:11:57.046Z","date_updated":"2021-05-03T12:11:57.046Z","date_created":"2021-05-03T12:11:57.046Z","accrual_date":"2021-05-02T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko74lix40jc10hdi0apcj69p","installment":1,"transaction_id":288325285,"amount":770,"reason_code":"4837","card_brand":"mastercard","updated_at":"2021-05-02T12:04:03.976Z","created_at":"2021-05-02T12:04:03.976Z","date_updated":"2021-05-02T12:04:03.976Z","date_created":"2021-05-02T12:04:03.976Z","accrual_date":"2021-05-01T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko5po02j0em20hdinsokat9k","installment":3,"transaction_id":307525115,"amount":13346,"reason_code":"83","card_brand":"visa","updated_at":"2021-05-01T12:18:19.099Z","created_at":"2021-05-01T12:18:19.099Z","date_updated":"2021-05-01T12:18:19.099Z","date_created":"2021-05-01T12:18:19.099Z","accrual_date":"2021-04-30T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko5pno0m0fql0hdinng0uqp5","installment":4,"transaction_id":307525115,"amount":13346,"reason_code":"83","card_brand":"visa","updated_at":"2021-05-01T12:18:03.478Z","created_at":"2021-05-01T12:18:03.478Z","date_updated":"2021-05-01T12:18:03.478Z","date_created":"2021-05-01T12:18:03.478Z","accrual_date":"2021-04-30T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko5plurj0fjk0hdiaft4a9r9","installment":1,"transaction_id":307525115,"amount":13346,"reason_code":"83","card_brand":"visa","updated_at":"2021-05-01T12:16:38.911Z","created_at":"2021-05-01T12:16:38.911Z","date_updated":"2021-05-01T12:16:38.911Z","date_created":"2021-05-01T12:16:38.911Z","accrual_date":"2021-04-30T03:00:00.000Z","status":"presented","cycle":1},{"object":"chargeback","id":"cb_cko5pl16f0fhg0hdi1fnmbobs","installment":2,"transaction_id":307525115,"amount":13346,"reason_code":"83","card_brand":"visa","updated_at":"2021-05-01T12:16:00.567Z","created_at":"2021-05-01T12:16:00.567Z","date_updated":"2021-05-01T12:16:00.567Z","date_created":"2021-05-01T12:16:00.567Z","accrual_date":"2021-04-30T03:00:00.000Z","status":"presented","cycle":1}]';
        return json_decode($data, true);
    }

    private function fakeTransaction()
    {
        //{
        //  "object": "transaction",
        //  "status": "paid",
        //  "refuse_reason": null,
        //  "status_reason": "acquirer",
        //  "pix_expiration_date": null,
        //  "metadata": {
        //    "antecipation_value": "2.66",
        //    "customer_value": "63.65",
        //    "order_code": "512OAODC9L",
        //    "plan": "perae do teste",
        //    "plan_id": "182",
        //    "plans_value": "67.00",
        //    "price": "76.08",
        //    "request_id": "cbcb8a14-6343-406a-9571-eaca076a1d7f",
        //    "service_value": "12.43",
        //    "tax_value": "3.35",
        //    "transaction_code": "tran_P2vYppVszPsdkY1M",
        //    "value": "67"
        //  }
        //}

        $data = '[{"object":"transaction","id":12187024,"status":"paid","refuse_reason":null,"status_reason":"acquirer","pix_expiration_date":null,"metadata": {"antecipation_value": "2.66","customer_value": "63.65","order_code": "512OAODC9L","plan": "perae do teste","plan_id": "182","plans_value": "67.00","price": "76.08","request_id": "cbcb8a14-6343-406a-9571-eaca076a1d7f","service_value": "12.43","tax_value": "3.35","transaction_code": "tran_P2vYppVszPsdkY1M","value": "67"}}]';
        return json_decode($data, true);
    }
}
