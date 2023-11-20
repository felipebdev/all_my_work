<?php

namespace Tests\Feature\Services\Checkout;

use App\Services\Finances\Checkout\OneClickRepository;
use App\Subscriber;
use Tests\TestCase;

class OneClickBuyRepositoryTest extends TestCase
{
    private OneClickRepository $oneClickRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->oneClickRepository = $this->app->make(OneClickRepository::class);
    }

    public function test_token_use()
    {
        $subscriber = Subscriber::first();
        $oneClick = $this->oneClickRepository->createHash(
            $subscriber->platform_id,
            $subscriber->id,
            'credit_card',
            'card1'
        );

        $token = $oneClick->id;

        $first = $this->oneClickRepository->requestLock($token);
        $this->assertNotNull($first); // acquires model and lock (first try)

        $this->assertTrue($this->oneClickRepository->isHashValid($token)); // valid

        // mark as used
        $this->oneClickRepository->markHashAsUsed($token);

        $second = $this->oneClickRepository->requestLock($token);
        $this->assertNull($second); // token used

        $this->assertFalse($this->oneClickRepository->isHashValid($token)); // invalid (used)
    }

    public function test_lock_request_release()
    {
        $subscriber = Subscriber::first();
        $oneClick = $this->oneClickRepository->createHash(
            $subscriber->platform_id,
            $subscriber->id,
            'credit_card',
            'card1'
        );

        $token = $oneClick->id;

        $this->assertTrue($this->oneClickRepository->isHashValid($token));

        $locked = $this->oneClickRepository->requestLock($token);
        $this->assertNotNull($locked); // acquires model and lock

        $lockMustFail = $this->oneClickRepository->requestLock($token);
        $this->assertNull($lockMustFail); // model is locked

        $this->oneClickRepository->releaseLock($token);

        $lockReleased = $this->oneClickRepository->requestLock($token);
        $this->assertNotNull($lockReleased); // acquires model after release
    }

    public function test_tries_limit()
    {
        $subscriber = Subscriber::first();
        $oneClick = $this->oneClickRepository->createHash(
            $subscriber->platform_id,
            $subscriber->id,
            'credit_card',
            'card1'
        );

        $token = $oneClick->id;

        $first = $this->oneClickRepository->requestLock($token);
        $this->assertNotNull($first); // acquires model and lock (first try)

        $second = $this->oneClickRepository->releaseLock($token)->requestLock($token);
        $this->assertNotNull($second); // acquires model and lock (second try)

        $third = $this->oneClickRepository->releaseLock($token)->requestLock($token);
        $this->assertNotNull($third); // acquires model and lock (third try)

        $fourth = $this->oneClickRepository->releaseLock($token)->requestLock($token);
        $this->assertNull($fourth); // not available (fourth try)
    }
}
