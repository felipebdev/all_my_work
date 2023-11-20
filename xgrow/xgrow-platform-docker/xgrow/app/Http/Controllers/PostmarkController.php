<?php

namespace App\Http\Controllers;

use App\Subscriber;
use Illuminate\Http\Request;

/**
 *
 */
class PostmarkController extends Controller
{
    /**
     * @param string $email
     * @return mixed
     */
    public function getAllMessagesByEmail(string $email)
    {
        return self::initPostmark("https://api.postmarkapp.com/messages/outbound?recipient=$email&count=25&offset=0");
    }

    /**
     * @param string $messageId
     * @return mixed
     */
    public function detailsMessage(string $messageId)
    {
        return self::initPostmark("https://api.postmarkapp.com/messages/outbound/$messageId/details");
    }

    /**
     * @param Request $request
     */
    public function bounceWebhook(Request $request)
    {
        Subscriber::addBounceCase($request->all());
    }

    /**
     * @return mixed
     */
    public function bounces()
    {
        return self::initPostmark("https://api.postmarkapp.com/bounces?type=HardBounce&count=500&offset=0");
    }

    /**
     * @param string $url
     * @param string $httpVerb
     * @return mixed
     */
    static function initPostmark(string $url, string $httpVerb = 'GET')
    {
        $postmarkToken = env('POSTMARK_TOKEN');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $httpVerb,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest',
                "X-Postmark-Server-Token: {$postmarkToken}"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return json_decode($response, true);
    }
}
