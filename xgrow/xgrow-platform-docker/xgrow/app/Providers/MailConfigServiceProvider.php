<?php


namespace App\Providers;

use App\Mail\Plugins\CustomMailHeaderPlugin;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Mail\MailServiceProvider;

class MailConfigServiceProvider extends MailServiceProvider
{

    public function register()
    {
        $this->loadConfig();

        $this->registerSwiftMailer();
        $this->registerIlluminateMailer();
        $this->registerMarkdownRenderer();
    }

    private function loadConfig()
    {
        $mailProviderName = $this->rememberDefaultProvider();

        if (!$mailProviderName) {
            return; // keep config
        }

        $config = $this->rememberProviderConfig($mailProviderName);

        if (!$config) {
            return; // keep config
        }

        // update config
        Config::set('mail', $config);

        $this->config = $config;
    }

    /**
     * Register a personalized Swift Mailer instance.
     *
     * @return void
     */
    public function registerSwiftMailer()
    {
        $this->registerSwiftTransport();

        // Once we have the transporter registered, we will register the actual Swift
        // mailer instance, passing in the transport instances, which allows us to
        // override this transporter instances during app start-up if necessary.
        $this->app->singleton('swift.mailer', function ($app) {

            if ($domain = $app->make('config')->get('mail.domain')) {
                \Swift_DependencyContainer::getInstance()
                    ->register('mime.idgenerator.idright')
                    ->asValue($domain);
            }

            $config = Config::get('mail');

            $mailer = new \Swift_Mailer($app['swift.transport']->driver());
            $mailer->registerPlugin(new CustomMailHeaderPlugin($config));

            return $mailer;
        });
    }
    /**
     * Register a personalized Illuminate mailer instance.
     *
     * @return void
     */
    public function registerIlluminateMailer()
    {
        $this->app->singleton('mailer', function ($app) {
            $config = Config::get('mail');

            $mailer = new Mailer($this->app['view'], $this->app['swift.mailer'], $this->app['events']);
            $mailer->alwaysFrom($config['from']['address'], $config['from']['name']);
            $mailer->setQueue($app['queue']);
            return $mailer;
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
        return Cache::driver('redis')->rememberForever('MAIL_PROVIDER_NAME', function () {
            $entry = DB::table('cache_entries')->where('name', '=', 'MAIL_PROVIDER_NAME')->first();

            // Force cache re-creation
            Cache::driver('redis')->forget('MAIL_PROVIDER_CONFIG');

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
        return Cache::driver('redis')->rememberForever('MAIL_PROVIDER_CONFIG', function () use ($providerName) {
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
