<?php

namespace App\Services\Finances\Checkout;

use App\OneClick;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class OneClickRepository
{

    protected const MAX_TRIES = 3;

    protected const ONE_CLICK_TIME_IN_MINUTES = 30;

    protected const LOCK_TIME_IN_SECONDS = 60; // lock time to prevent duplicated use of hash

    public function createHash(
        string $platformId,
        string $subscriberId,
        string $paymentMethod,
        ?string $gatewayCardId,
        int $installments = 1,
        ?string $previousHash = null
    ): OneClick {
        $oneClick = new OneClick();

        $oneClick->id = $this->generanteHash();
        $oneClick->platform_id = $platformId;
        $oneClick->subscriber_id = $subscriberId;
        $oneClick->expires_at = Carbon::now()->addMinutes(self::ONE_CLICK_TIME_IN_MINUTES);
        $oneClick->payment_method = $paymentMethod;
        $oneClick->card_id = $gatewayCardId;
        $oneClick->installments = $installments;
        $oneClick->previous_id = $previousHash;
        $oneClick->save();

        return $oneClick;
    }

    public function isHashValid(string $hash): bool
    {
        return OneClick::where('id', $hash)
            ->where('tries', '<', self::MAX_TRIES)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->exists();
    }

    public function getWithoutLock(string $hash): ?OneClick
    {
        return OneClick::where('id', $hash)
            ->where('tries', '<', self::MAX_TRIES)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    /**
     * Get a lock (or return null) and increment tries
     *
     * @param  string  $hash
     * @return \App\OneClick|null
     */
    public function requestLock(string $hash): ?OneClick
    {
        $now = Carbon::now();

        $affected = OneClick::where('id', $hash)
            ->where('tries', '<', self::MAX_TRIES)
            ->where('used', false)
            ->where('expires_at', '>', $now)
            ->where(function ($query) use ($now) {
                $query->whereNull('locked_at')
                    ->orWhere('locked_at', '<', $now->clone()->subSeconds(self::LOCK_TIME_IN_SECONDS));
            })->update([
                'locked_at' => $now,
                'tries' => DB::raw('tries + 1'),
            ]);

        if ($affected == 0) {
            return null; // ops, hash not available due to concurrent use
        }

        $model = OneClick::findOrFail($hash);

        return $model;
    }

    /**
     * Release the lock
     *
     * @param  string  $hash
     */
    public function releaseLock(string $hash): self
    {
        OneClick::where('id', $hash)->update(['locked_at' => null]);
        return $this;
    }

    public function markHashAsUsed(string $hash): self
    {
        OneClick::where('id', $hash)->update(['used' => true]);
        return $this;
    }

    protected function generanteHash(): string
    {
        return Uuid::uuid4();
    }
}
