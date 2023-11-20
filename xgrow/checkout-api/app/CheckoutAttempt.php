<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CheckoutAttempt extends Model
{
    const LIMIT_ATTEMPTS = 5;

    protected $table = 'checkoutattempts';

    protected $fillable = ['email', 'ip', 'subscriber_id', 'platform_id'];

    public static function generateToken(Request $request, $subscriber_id)
    {
        $checkoutAttempt = new CheckoutAttempt();
        $checkoutAttempt->email = $request->email;
        $checkoutAttempt->ip = $request->user_ip;
        $checkoutAttempt->subscriber_id = $subscriber_id;
        $checkoutAttempt->platform_id = $request->platform_id;
        $checkoutAttempt->save();

        $encrypt = ['date' => Carbon::now(),
                    'token_id' => $checkoutAttempt->id,
                    'platform_id' => $request->platform_id,
                    'subscriber_id' => $subscriber_id,
                    'subscriber_ip' => $request->user_ip,
                    'plan_id' => $request->plan_id];

        return Crypt::encrypt($encrypt);
    }

    public static function check($token)
    {
        try {
            $decrypted = Crypt::decrypt($token);

            if (is_array($decrypted)) {
                $checkoutAttempt = self::where('id', '=', $decrypted['token_id'])->first();
                if ($checkoutAttempt) {
                    if ($checkoutAttempt->attempts >= 0 && $checkoutAttempt->attempts < env('CHECKOUT_LIMIT_ATTEMPTS', self::LIMIT_ATTEMPTS)) {
                        $checkoutAttempt->attempts = $checkoutAttempt->attempts + 1;
                        $checkoutAttempt->save();
                        return true;
                    }
                }
            }
        } catch (DecryptException $e) {
            return false;
        }
        return false;
    }
}
