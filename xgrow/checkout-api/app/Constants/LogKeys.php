<?php

namespace App\Constants;

final class LogKeys
{
    //  Private

    private const CHARGE_REGULAR_RECURRENCES = 'charge:regular:recurrences'; // subscription + no-limit + legacy
    private const CHARGE_REGULAR_SUBSCRIPTION = 'charge:regular:subscription';
    private const CHARGE_REGULAR_LEGACY = 'charge:regular:legacy';
    private const CHARGE_REGULAR_NOLIMIT = 'charge:regular:no-limit';

    private const CHARGE_RULER_SUBSCRIPTION = 'charge:ruler:subscription';
    private const CHARGE_RULER_NOLIMIT = 'charge:ruler:no-limit';

    private const LASTSTART = ':laststart';
    private const LASTTRACE = ':lasttrace';
    private const FOUND = ':found';
    private const GROUPED = ':grouped';
    private const AFFECTED = ':affected';
    private const TOTAL = ':total';
    private const LASTEND = ':lastend';
    private const EXCEPTION = ':exception';

    //// public

    // regular charges

    public const CHARGE_REGULAR_SUBSCRIPTION_LASTSTART = self::CHARGE_REGULAR_SUBSCRIPTION.self::LASTSTART;
    public const CHARGE_REGULAR_SUBSCRIPTION_LASTTRACE = self::CHARGE_REGULAR_SUBSCRIPTION.self::LASTTRACE;
    public const CHARGE_REGULAR_SUBSCRIPTION_FOUND = self::CHARGE_REGULAR_SUBSCRIPTION.self::FOUND;
    public const CHARGE_REGULAR_SUBSCRIPTION_AFFECTED = self::CHARGE_REGULAR_SUBSCRIPTION.self::AFFECTED;
    public const CHARGE_REGULAR_SUBSCRIPTION_TOTAL = self::CHARGE_REGULAR_SUBSCRIPTION.self::TOTAL;
    public const CHARGE_REGULAR_SUBSCRIPTION_LASTEND = self::CHARGE_REGULAR_SUBSCRIPTION.self::LASTEND;

    public const CHARGE_REGULAR_LEGACY_LASTSTART = self::CHARGE_REGULAR_LEGACY.self::LASTSTART;
    public const CHARGE_REGULAR_LEGACY_LASTTRACE = self::CHARGE_REGULAR_LEGACY.self::LASTTRACE;
    public const CHARGE_REGULAR_LEGACY_FOUND = self::CHARGE_REGULAR_LEGACY.self::FOUND;
    public const CHARGE_REGULAR_LEGACY_AFFECTED = self::CHARGE_REGULAR_LEGACY.self::AFFECTED;
    public const CHARGE_REGULAR_LEGACY_TOTAL = self::CHARGE_REGULAR_LEGACY.self::TOTAL;
    public const CHARGE_REGULAR_LEGACY_LASTEND = self::CHARGE_REGULAR_LEGACY.self::LASTEND;

    public const CHARGE_REGULAR_NOLIMIT_LASTSTART = self::CHARGE_REGULAR_NOLIMIT.self::LASTSTART;
    public const CHARGE_REGULAR_NOLIMIT_LASTTRACE = self::CHARGE_REGULAR_NOLIMIT.self::LASTTRACE;
    public const CHARGE_REGULAR_NOLIMIT_FOUND = self::CHARGE_REGULAR_NOLIMIT.self::FOUND;
    public const CHARGE_REGULAR_NOLIMIT_GROUPED = self::CHARGE_REGULAR_NOLIMIT.self::GROUPED;
    public const CHARGE_REGULAR_NOLIMIT_AFFECTED = self::CHARGE_REGULAR_NOLIMIT.self::AFFECTED;
    public const CHARGE_REGULAR_NOLIMIT_TOTAL = self::CHARGE_REGULAR_NOLIMIT.self::TOTAL;
    public const CHARGE_REGULAR_NOLIMIT_LASTEND = self::CHARGE_REGULAR_NOLIMIT.self::LASTEND;

    public const CHARGE_REGULAR_RECURRENCES_LASTSTART = self::CHARGE_REGULAR_RECURRENCES.self::LASTSTART;
    public const CHARGE_REGULAR_RECURRENCES_LASTEND = self::CHARGE_REGULAR_RECURRENCES.self::LASTEND;

    // charge ruler

    public const CHARGE_RULER_SUBSCRIPTION_LASTSTART = self::CHARGE_RULER_SUBSCRIPTION.self::LASTSTART;
    public const CHARGE_RULER_SUBSCRIPTION_LASTTRACE = self::CHARGE_RULER_SUBSCRIPTION.self::LASTTRACE;
    public const CHARGE_RULER_SUBSCRIPTION_FOUND = self::CHARGE_RULER_SUBSCRIPTION.self::FOUND;
    public const CHARGE_RULER_SUBSCRIPTION_AFFECTED = self::CHARGE_RULER_SUBSCRIPTION.self::AFFECTED;
    public const CHARGE_RULER_SUBSCRIPTION_TOTAL = self::CHARGE_RULER_SUBSCRIPTION.self::TOTAL;
    public const CHARGE_RULER_SUBSCRIPTION_LASTEND = self::CHARGE_RULER_SUBSCRIPTION.self::LASTEND;
    public const CHARGE_RULER_SUBSCRIPTION_EXCEPTION = self::CHARGE_RULER_SUBSCRIPTION.self::EXCEPTION;

    public const CHARGE_RULER_NOLIMIT_LASTSTART = self::CHARGE_RULER_NOLIMIT.self::LASTSTART;
    public const CHARGE_RULER_NOLIMIT_LASTTRACE = self::CHARGE_RULER_NOLIMIT.self::LASTTRACE;
    public const CHARGE_RULER_NOLIMIT_FOUND = self::CHARGE_RULER_NOLIMIT.self::FOUND;
    public const CHARGE_RULER_NOLIMIT_AFFECTED = self::CHARGE_RULER_NOLIMIT.self::AFFECTED;
    public const CHARGE_RULER_NOLIMIT_TOTAL = self::CHARGE_RULER_NOLIMIT.self::TOTAL;
    public const CHARGE_RULER_NOLIMIT_LASTEND = self::CHARGE_RULER_NOLIMIT.self::LASTEND;
    public const CHARGE_RULER_NOLIMIT_EXCEPTION = self::CHARGE_RULER_NOLIMIT.self::EXCEPTION;

    // Cron

    public const CRON_RUN_FIVE_DAYS_CANCEL_SUBSCRIPTIONS_LAST_RECURRENCE_ID_TODAY =
        'cron:run_five_days_cancel_subscriptions:last_recurrence_id_today';

    public const CRON_NOTIFY_UPCOMING_BOLETO_PIX_LAST_RECURRENCE_ID_TODAY =
        'cron:notify_upcoming_boleto_pix:last_recurrence_id_today';

    public const CRON_NOTIFY_EXPIRED_BOLETO_PIX_LAST_RECURRENCE_ID_TODAY =
        'cron:notify_expired_boleto_pix:last_recurrence_id_today';

    public const CRON_NOTIFY_UPCOMING_SUBSCRIPTION_LAST_RECURRENCE_ID_TODAY =
        'cron:notify_upcoming_subscription:last_recurrence_id_today';

    public const CRON_NOTIFY_UPCOMING_NOLIMIT_LAST_PAYMENT_ID_TODAY =
        'cron:notify_upcoming_nolimit:last_payment_id_today';

}
