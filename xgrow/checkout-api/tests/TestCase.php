<?php

namespace Tests;

use App\Logs\ChargeLog;
use App\Services\Charges\NoLimitChargeService;
use App\Services\Mundipagg\CreditCardRecurrenceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public static function setUpBeforeClass(): void
    {
        ChargeLog::$favoriteChannel = 'null';
    }

    /**
     * @before
     */
    public function resetForcedFailOnTearUp()
    {
        CreditCardRecurrenceService::$forceFailStatusDebug = false;
        NoLimitChargeService::$forceFailStatusDebug = false;
    }

    /**
     * @after
     */
    public function resetCarbonNowOnTearDown(): void
    {
        // IMPORTANT: Reset the date to not affect the next test!
        Carbon::setTestNow();
    }
}
