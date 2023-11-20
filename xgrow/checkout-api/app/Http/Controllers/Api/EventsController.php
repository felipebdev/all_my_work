<?php

namespace App\Http\Controllers\Api;

use App\Facades\JwtCheckoutFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FacebookPixelRequest;
use App\Jobs\FacebookPixelJob;
use Illuminate\Support\Str;

class EventsController extends Controller
{

    private $actionSource = 'website';

    public function addPaymentInfo(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('AddPaymentInfo');
        return response()->noContent();
    }

    public function addToCart(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('AddToCart');
        return response()->noContent();
    }


    public function completeRegistration(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('CompleteRegistration');
        return response()->noContent();
    }

    public function contact(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('Contact');
        return response()->noContent();
    }

    public function initiateCheckout(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('InitiateCheckout');
        return response()->noContent();
    }

    public function lead(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('Lead', [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
        ]);
        return response()->noContent();
    }

    public function pageView(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('PageView');
        return response()->noContent();
    }

    public function purchase(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('Purchase', [
            'value' => (float) $request->value ?? 0.00,
            'currency' => $request->currency ?? 'BRL',
            'content_ids' => [
                (string) JwtCheckoutFacade::getPlanId() ?? '',
            ],
            'content_name' => $request->content_name ?? '',
            // 'content_type' => 'product'
        ]);
        return response()->noContent();
    }

    public function subscribe(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('Subscribe', [
            'value' => (float) $request->value ?? 0.00,
            'currency' => $request->currency ?? 'BRL',
            'predicted_ltv' => (float) $request->predicted_ltv ?? 0.00,
            'content_ids' => [
                (string) JwtCheckoutFacade::getPlanId() ?? '',
            ],
            'content_name' => $request->content_name ?? '',
        ]);
        return response()->noContent();
    }

    public function viewContent(FacebookPixelRequest $request)
    {
        $this->sendDataToJob('ViewContent', [
            'content_name' => $request->content_name ?? '',
        ]);
        return response()->noContent();
    }

    private function sendDataToJob(string $eventName, array $customData = [])
    {
        $data = [
            'event_name' => $eventName,
            'event_time' => time(),
            'user_data' => [
                'client_ip_address' => request()->client_ip_address,
                'client_user_agent' => request()->client_user_agent,
            ],
            'event_source_url' => request()->url,
            'action_source' => $this->actionSource,
            'custom_data' => $customData,
        ];

        $email = request()->email ?? null;
        if ($email) {
            $data['user_data']['em'] = hash('sha256', strtolower($email));
        }

        $data['event_id'] = request()->event_id ?? (string) Str::uuid();

        FacebookPixelJob::dispatch(
            JwtCheckoutFacade::getPlatformId(),
            JwtCheckoutFacade::getPlanId(),
            $data,
            request()->test_event_code
        )->onConnection('pixel');
    }
}
