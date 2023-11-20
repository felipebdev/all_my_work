<?php

namespace Tests\Feature\Services\Finances\PaymentChange;

use App\Services\Finances\PaymentChange\ChangeCardUrlService;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ChargeCardUrlServiceTest extends TestCase
{
    private ChangeCardUrlService $changeCardUrlService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->changeCardUrlService = $this->app->make(ChangeCardUrlService::class);
    }

    public function test_example()
    {
        $urlWithToken = $this->changeCardUrlService->generateUrlWithToken(Uuid::NIL, 'example@xgrow.com', '');

        $this->assertIsString($urlWithToken);
    }
}
