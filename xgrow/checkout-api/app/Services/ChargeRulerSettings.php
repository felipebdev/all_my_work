<?php

namespace App\Services;

use App\ChargeRuler;
use App\Email;

class ChargeRulerSettings
{
    /**
     * @deprecated
     */
    public static function defaultNotificationsForBoleto(string $platformId, bool $isActive = true)
    {
        $type = ChargeRuler::TYPE_BOLETO;
        return collect([
            self::newRule($platformId, $type, 1, $isActive, 1, Email::CONSTANT_EMAIL_BOLETO),
            self::newRule($platformId, $type, 2, $isActive, 2, Email::CONSTANT_EMAIL_BANK_SLIP_EXPIRATION),
        ]);
    }

    public static function defaultChargesForSubscription(?string $platformId = null, bool $isActive = true)
    {
        $type = ChargeRuler::TYPE_SUBSCRIPTION;
        return collect([
            self::newRule($platformId, $type, 1, $isActive, 5, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED),
            self::newRule($platformId, $type, 2, $isActive, 12, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED),
            self::newRule($platformId, $type, 3, $isActive, 20, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL),
            //self::newRule($platformId, $type, 4, $isActive, 30,
            //    Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL),
        ]);
    }

    public static function defaultChargesForNolimit(?string $platformId = null, bool $isActive = true)
    {
        $type = ChargeRuler::TYPE_NOLIMIT;
        return collect([
            self::newRule($platformId, $type, 1, $isActive, 5, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED),
            self::newRule($platformId, $type, 2, $isActive, 12, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_RETRY_FAILED),
            self::newRule($platformId, $type, 3, $isActive, 20, Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL),
            //self::newRule($platformId, $type, 4, $isActive, 30,
            //    Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL),
        ]);
    }

    public static function defaultNotificationsForAccess(?string $platformId = null, bool $isActive = true)
    {
        $type = ChargeRuler::TYPE_ACCESS;
        return collect([
            self::newRule($platformId, $type, 1, $isActive, 2, Email::CONSTANT_EMAIL_NEVER_ACCESSED),
            self::newRule($platformId, $type, 2, $isActive, 4, Email::CONSTANT_EMAIL_NEVER_ACCESSED),
            self::newRule($platformId, $type, 3, $isActive, 6, Email::CONSTANT_EMAIL_NEVER_ACCESSED),
            self::newRule($platformId, $type, 4, $isActive, 10, Email::CONSTANT_EMAIL_NEVER_ACCESSED),
        ]);
    }

    /**
     * Check if a mail template is expected to also cancel subscription
     *
     * @param  int  $emailConstant
     * @return bool
     */
    public static function isCancelRequired(?int $emailConstant = null): bool
    {
        if (is_null($emailConstant)) {
            return false;
        }

        $includesCancel = [
            Email::CONSTANT_EMAIL_RECURRENCE_PAYMENT_FAILED_SUBSCRIPTION_CANCEL,
        ];

        return in_array($emailConstant, $includesCancel);
    }


    /**
     * Fill a new ChargeRuler model without saving it on database
     *
     * @param  string  $platformId
     * @param  string  $type
     * @param  int  $position
     * @param  bool  $active
     * @param  int  $interval
     * @param  int  $emailId
     * @return \App\ChargeRuler
     */
    private static function newRule(
        ?string $platformId,
        string $type,
        int $position,
        bool $active,
        int $interval,
        int $emailId
    ) {
        $fill = [
            'platform_id' => $platformId,
            'type' => $type,
            'position' => $position,
            'active' => $active,
            'interval' => $interval,
            'email_id' => $emailId,
        ];

        return new ChargeRuler($fill);
    }
}
