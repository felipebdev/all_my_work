<?php

namespace App\Observers;

use App\Mail\SendMailPlatformUser;
use App\PlatformUser;
use App\PlatformUserAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 *
 */
class PlatformUserObserver
{
    /**
     * Handle the platform user "creating" event.
     *
     * @param  \App\PlatformUser  $platformUser
     * @return void
     */
    public function creating(PlatformUser $platformUser)
    {
        if (request()->route()->uri === 'producers') {

            $password = self::generatePassword();

            $platformUser->password = Hash::make($password);

            $accessData = [
                'platform_id' => Auth::user()->platform_id,
                'name' => $platformUser->name,
                'email' => $platformUser->email,
                'password' => $password,
            ];

            Mail::to($platformUser->email)->send(new SendMailPlatformUser($accessData));
        }
    }

    /**
     * Handle the platform user "created" event.
     *
     * @param  \App\PlatformUser  $platformUser
     * @return void
     */
    public function created(PlatformUser $platformUser)
    {
        if (request()->route()->uri === 'producers') {

            $platformUserAccess = new PlatformUserAccess();
            $platformUserAccess->platform_id = $platformUser->platform_id;
            $platformUserAccess->platforms_users_id = $platformUser->id;
            $platformUserAccess->type_access = 'restrict';
            $platformUserAccess->save();
        }
    }

    /**
     * @return false|string
     */
    static function generatePassword()
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvxywzABCDEFGHIJKLMNOPQRSTUVXYWZ0123456789!@#$%&*()-.+'), 18, 8);
    }
}
