<?php

namespace Tests\Feature\Services\Finances\Balance\Objects;

use App\Services\Finances\Balance\Objects\BalanceResponse;
use Tests\TestCase;

class BalanceResponseTest extends TestCase
{

    public function test_balance_response_object_serialization()
    {
        $json = <<< EOT
{
    "object": "balance",
    "waiting_funds": {
        "amount": 123
    },
    "available": {
        "amount": 456
    },
    "transferred": {
        "amount": 789
    }
}
EOT;

        $balanceResponse = BalanceResponse::fromPagarmeObject(json_decode($json));

        $serialized = $balanceResponse->jsonSerialize();

        $this->assertEquals('balance', $serialized['object']);
        $this->assertEquals(456, $serialized['current']);
        $this->assertEquals(456, $serialized['available']);
        $this->assertEquals(456, $serialized['pending']);
        $this->assertEquals(789, $serialized['transferred']);
    }


    public function test_balance_negative_pending()
    {
        $json = <<< EOT
{
    "object": "balance",
    "waiting_funds": {
        "amount": -123
    },
    "available": {
        "amount": 456
    },
    "transferred": {
        "amount": 789
    }
}
EOT;

        $balanceResponse = BalanceResponse::fromPagarmeObject(json_decode($json));

        $serialized = $balanceResponse->jsonSerialize();

        $this->assertEquals('balance', $serialized['object']);
        $this->assertEquals(456, $serialized['current']);
        $this->assertEquals(333, $serialized['available']);
        $this->assertEquals(333, $serialized['pending']);
        $this->assertEquals(789, $serialized['transferred']);
    }

}
