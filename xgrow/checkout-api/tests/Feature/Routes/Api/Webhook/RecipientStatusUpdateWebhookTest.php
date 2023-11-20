<?php

namespace Tests\Feature\Routes\Api\Webhook;

use App\Platform;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RecipientStatusUpdateWebhookTest extends TestCase
{

    use DatabaseTransactions;

    public const CLIENTE_RECIPIENT = 'rp_D5gpmVYuxHaArWkP'; // Mochileiro das Galáxias: cliente@xgrow.com

    public function test_example()
    {
        Platform::query()->update(['recipient_status' => 'registered']);

        $data = json_decode($this->json, true);
        $data['data']['id'] = self::CLIENTE_RECIPIENT;

        $response = $this->json('POST', '/api/checkout/recipient/update/mundipagg', $data);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertDatabaseHas('platforms', [
            'recipient_id' => self::CLIENTE_RECIPIENT,
            'recipient_status' => 'refused',
        ]);
    }

    private $json = /** @lang JSON */
        <<< EOT
{
  "id": "hook_xxxxxxxxxxxxxxxx",
  "account": {
    "id": "acc_xxxxxxxxxxxxxxxx",
    "name": "Xgrow"
  },
  "type": "recipient.updated",
  "created_at": "2023-01-24T13:28:27Z",
  "data": {
    "id": "rp_xxxxxxxxxxxxxxxx",
    "name": "Isabella Juliana Aragão",
    "email": "isabella.juliana.aragao@xgrow.com",
    "document": "24539965336",
    "description": " - Juliana Aragao",
    "type": "individual",
    "payment_mode": "bank_transfer",
    "status": "refused",
    "created_at": "2023-01-24T13:28:27",
    "updated_at": "2023-01-24T13:28:30",
    "transfer_settings": {
      "transfer_enabled": false,
      "transfer_interval": "Daily",
      "transfer_day": 0
    },
    "default_bank_account": {
      "id": "ba_xxxxxxxxxxxxxxxx",
      "holder_name": "Isabela Aragao",
      "holder_type": "individual",
      "holder_document": "24539965336",
      "bank": "077",
      "branch_number": "0001",
      "branch_check_digit": "9",
      "account_number": "2384423",
      "account_check_digit": "0",
      "type": "checking",
      "status": "active",
      "created_at": "2023-01-24T13:28:27",
      "updated_at": "2023-01-24T13:28:27"
    },
    "gateway_recipients": [
      {
        "gateway": "pagarme",
        "status": "refused",
        "pgid": "re_xxxxxxxxxxxxxxxxxxxxxxxxx",
        "createdAt": "2023-01-24T13:28:27",
        "updatedAt": "2023-01-24T13:28:27"
      }
    ],
    "automatic_anticipation_settings": {
      "enabled": true,
      "type": "1025",
      "volume_percentage": 100,
      "delay": 29,
      "days": [
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        30,
        31
      ]
    }
  }
}
EOT;


}
