<?php

namespace App\Services\Finances\Checkout;

use App\Exceptions\Checkout\HashLockedException;
use App\Exceptions\Checkout\HashNotfoundException;
use App\OneClick;
use App\Subscriber;

class OneClickBuyService
{

    /**
     * Static method to simplify access to this service
     */
    public static function createOneClickForSubscriber(
        Subscriber $subscriber,
        string $paymentMethod,
        ?string $cardId,
        int $installments = 1,
        ?string $previousHash = null
    ): OneClick
    {
        /** @var self $self */
        $self = app()->make(self::class);
        return $self->oneClickRepository->createHash(
            $subscriber->platform_id,
            $subscriber->id,
            $paymentMethod,
            $cardId,
            $installments,
            $previousHash
        );
    }

    public OneClickRepository $oneClickRepository;

    public function __construct(OneClickRepository $oneClickRepository)
    {
        $this->oneClickRepository = $oneClickRepository;
    }

    public function successfullOneClick(string $hash): void
    {
        $this->oneClickRepository->markHashAsUsed($hash);
    }

    /**
     *
     *
     * @param  string  $hash
     * @return \App\OneClick
     * @throws \App\Exceptions\Checkout\HashNotfoundException
     */
    public function getOneClick(string $hash): OneClick
    {
        $oneClick = $this->oneClickRepository->getWithoutLock($hash);

        if (!$oneClick) {
            throw new HashNotfoundException('Hash invalid or gone');
        }

        return $oneClick;
    }

    /**
     * Try to use Hash.
     *
     * Using a Hash implies that this hash will be temporarily locked, preventing concurrent usage;
     * if successfully locked, also increase "tries".
     *
     * @param  string  $hash
     * @throws \App\Exceptions\Checkout\HashLockedException
     * @throws \App\Exceptions\Checkout\HashNotfoundException
     */
    public function useOneClick(string $hash): OneClick
    {
        $oneClick = $this->oneClickRepository->requestLock($hash);
        if (!$oneClick) {
            if ($this->oneClickRepository->isHashValid($hash)) {
                throw new HashLockedException('Hash being used, please wait');
            }

            throw new HashNotfoundException('Hash invalid or gone');
        }

        // "trap" to release lock after response
        app()->terminating(function () use ($hash) {
            $this->oneClickRepository->releaseLock($hash);
        });

        return $oneClick;
    }


}
