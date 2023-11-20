<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Auth;
use LaunchDarkly\LDClient;
use LaunchDarkly\LDUser;
use LaunchDarkly\LDUserBuilder;

class FeatureFlagHelper
{
    public function check($flagKey, $default = false) {
        if(env("LAUNCH_DARKLY_SDK_KEY")) {
            //Check user is authenticated
            $user = new LDUser("unlogged", "Usuário não autenticado");
            if (Auth::check()) {
                $platformUser = Auth::user();
                $user = (new LDUserBuilder($platformUser->id))
                    ->name($platformUser->name)
                    ->email($platformUser->email)
                    ->build();
            }

            $config = [
                "base_uri" => env("LAUNCH_DARKLY_BASE_URI"),
                "events_uri" => env("LAUNCH_DARKLY_EVENTS_URI"),
            ];

            if(env("LAUNCH_DARKLY_REDIS_HOST") and env("LAUNCH_DARKLY_REDIS_PORT")) {
                $fr = \LaunchDarkly\Integrations\Redis::featureRequester([
                    'redis_host' => env('LAUNCH_DARKLY_REDIS_HOST'),
                    'redis_port' => env('LAUNCH_DARKLY_REDIS_PORT'),
                    'redis_prefix' => 'launchdarkly'
                ]);
                $config['feature_requester'] = $fr;
            }

            $client = new LDClient(env("LAUNCH_DARKLY_SDK_KEY"), $config);

            return $client->variation($flagKey, $user, $default);
        }
        return $default;
    }
}
