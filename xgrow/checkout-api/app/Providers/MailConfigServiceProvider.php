<?php

namespace App\Providers;

use App\Mail\Plugins\CustomMailHeaderPlugin;
use Exception;
use Illuminate\Mail\MailManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MailConfigServiceProvider extends MailServiceProvider
{

    public function register()
    {
        $this->loadConfig();

        $this->registerIlluminateMailer();
        $this->registerMarkdownRenderer();
    }

    private function loadConfig(): void
    {
        if ($this->app->runningUnitTests()) {
            return; // use phpunit settings
        }

        try {
            $mailProviderName = $this->rememberDefaultProvider();
        } catch (Exception $e) {
            return; // no connections available, use default config
        }

        if (!$mailProviderName) {
            return; // keep config
        }

        $config = $this->rememberProviderConfig($mailProviderName);
        if (!$config) {
            return; // keep config
        }

        $tag = Config::get('mail.tag');
        if ($tag) {
            $config['tag'] = $tag; // add tag to config if set
        }

        // update config
        Config::set('mail', $config);
    }

    /**
     * Register a personalized Illuminate mailer instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer(): void
    {
        $this->app->singleton('mail.manager', function ($app) {
            $config = Config::get('mail');

            $mailManager = new MailManager($app);
            $mailManager->alwaysFrom($config['from']['address'], $config['from']['name']);
            $mailManager->setQueue($app['queue']);

            $mailer = $mailManager->getSwiftMailer();
            $mailer->registerPlugin(new CustomMailHeaderPlugin($config));

            return $mailManager;
        });

        $this->app->bind('mailer', function ($app) {
            return $app->make('mail.manager')->mailer();
        });
    }

    /**
     * Get default provider from cache (if necessary looks at database and updates cache),
     * returns null if default provider is not set.
     *
     * @return string|null Provider name, null if not set
     */
    private function rememberDefaultProvider(): ?string
    {
        return Cache::rememberForever('MAIL_PROVIDER_NAME', function () {
            $entry = DB::table('cache_entries')->where('name', '=', 'MAIL_PROVIDER_NAME')->first();

            // Force cache re-creation
            Cache::forget('MAIL_PROVIDER_CONFIG');

            // Get default mail provider
            return $entry->default_value ?? null;
        });
    }

    /**
     * Get Config from cache (if necessary looks at database and updates cache),
     * returns null if provider config is not found.
     *
     * @param  string  $providerName
     * @return array|null
     */
    private function rememberProviderConfig(string $providerName): ?array
    {
        return Cache::rememberForever('MAIL_PROVIDER_CONFIG', function () use ($providerName) {
            $entry = DB::table('email_providers')->where('name', '=', $providerName)->first();

            if (!$entry) {
                return null;
            }

            $data = json_decode($entry->settings, $associative = true);

            $config = array_merge([
                'driver' => $entry->driver,
                'from' => [
                    'address' => $entry->from_address,
                    'name' => $entry->from_name
                ],
            ], $data);

            return $config;
        });
    }
}
