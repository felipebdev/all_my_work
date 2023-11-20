<?php

namespace Tests\Feature\Services\Checkout;

use App\Facades\MagicToken;
use Tests\TestCase;

class MagicTokenFacadeTest extends TestCase
{
    public function test_encode()
    {
        $jwt = MagicToken::generate('00000', '11111');

        $this->assertIsString($jwt);
    }

    public function test_decode()
    {
        $jwt = MagicToken::generate('00000', '11111');

        $decoded = MagicToken::decode($jwt);

        $this->assertIsObject($decoded);

        $this->assertObjectHasAttribute('platform_id', $decoded);
        $this->assertIsString($decoded->platform_id);

        $this->assertObjectHasAttribute('subscriber_id', $decoded);
        $this->assertIsString($decoded->subscriber_id);
    }
}
