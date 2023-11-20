<?php

/**
 * Sample use of Cloud Functions to send POST action on Cloud Event (Google Pub/Sub)
 */

/**
 * Boot
 */
require_once '../../../../vendor/autoload.php';

/**
 * Prepare event like Google Cloud
 */

use Google\CloudFunctions\CloudEvent;

$data = /** @lang JSON */
    <<< JSON
{
  "To":"5535992572841",
  "Type":"Template",
  "Text": "Hello",
  "TemplateId":"471535891733039",
  "Fields": {
    "property1":"52PKC1TG35",
    "property2":"Plano100",
    "property3":"Tom Saraiva (33183057905)",
    "property4":"venda100@xgrow.com",
    "property5":"fd6ec7e8-0481-48c9-b91a-8b2f46e5871e"
  }
}
JSON;

$encoded = base64_encode($data);

$googleJson = /** @lang JSON */
    <<< JSON
{
  "message": {
    "data" : $encoded
  }
}
JSON;

$googleData = json_decode($googleJson, true);

use Google\CloudFunctions\CloudEvent;

$event = CloudEvent::fromArray([
    'id' => 'id',
    'source' => 'source',
    'specversion' => 'specversion',
    'type' => 'type',
    'datacontenttype' => 'datacontettype',
    'dataschema' => 'dataschema',
    'subject' => 'subject',
    'time' => 'time',
    'data' => $googleData,
]);

/**
 * Cloud Function definition begins here
 */

$log = fopen('php://stderr', 'wb');

function logmessage(string $message): void
{
    global $log;

    fwrite($log, $message.PHP_EOL);
}

function main(CloudEvent $cloudevent)
{
    $message = $cloudevent->getData()['message'];

    $base64 = $message['data'] ?? null;
    if ($base64) {
        logmessage('Using from base64_decode');
        $message = json_decode(base64_decode($base64), true);
    }

    logmessage('Message:');
    logmessage(var_export($message, true));

    if (!$message) {
        logmessage('Bad message');
    }

    $fields = $message['Fields'] ?? [];
    $templateId = $message['TemplateId'] ?? '';

    $payload = [
        'From' => '5511913327436',
        'To' => $message['To'],
        'Type' => 'Template',
        'TemplateId' => $templateId,
        'Text' => 'Test message',
        'Fields' => [
            '1' => $fields['property1'] ?? '',
            '2' => $fields['property2'] ?? '',
            '3' => $fields['property3'] ?? '',
            '4' => $fields['property4'] ?? '',
            '5' => $fields['property5'] ?? '',
            '6' => $fields['property6'] ?? '',
            '7' => $fields['property7'] ?? '',
            '8' => $fields['property8'] ?? '',
            '9' => $fields['property9'] ?? '',
        ],
        'ButtonFields' => [
            [
                '1' => $fields['property1'] ?? '',
            ]
        ],
    ];

    leadlovers($payload);
}

function leadlovers(array $data): bool
{
    $url = 'https://llapi.leadlovers.com/webapi';
    $token = 'B63F562123574CC6XXXXXXXXXXXXXXXX';

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url.'/zaplovers/sendmessage'.'?token='.''.$token.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'Content-Type:application/json'
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    $info = curl_getinfo($curl);

    curl_close($curl);

    if ($err) {
        logmessage('cURL Error #: '.$err.PHP_EOL);
        return false;
    }

    $httpCode = $info['http_code'];

    if ($httpCode >= 400) {
        logmessage('cURL Error #: '.$response.PHP_EOL);
        return false;
    }

    logmessage('cURL Success #:'. $response);
    return true;
}

/**
 * Coud Function ends here
 */

/**
 * Call entrypoint
 */
main($event);
