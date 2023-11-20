<?php

namespace Tests\Feature\Traits\Json;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;

trait ValidatesBoletoJsonPayload
{

    public function assertJsonPayload(TestResponse $response): void
    {
        $response->assertJsonStructure([
            '*' => [
                'status',
                'order_code',
                'boleto_pdf',
                'boleto_qrcode',
                'boleto_barcode',
                'boleto_url',
                'boleto_line',
                'pix_qrcode',
                'pix_qrcode_url',
                'magicToken',
                'one_click'
            ],
        ]);

        $response->assertJsonPath('0.status', 'pending');

        $response->assertJson(function (AssertableJson $json) {
            $json->first(fn($json) => $json->whereAllType([
                'status' => 'string',
                'order_code' => 'string',
                'boleto_pdf' => 'string',
                'boleto_qrcode' => 'string',
                'boleto_barcode' => 'string',
                'boleto_url' => 'string',
                'boleto_line' => 'string',
                'pix_qrcode' => 'null',
                'pix_qrcode_url' => 'null',
                'magicToken' => 'string',
                'one_click' => 'string',
            ]));
        });
    }
}
