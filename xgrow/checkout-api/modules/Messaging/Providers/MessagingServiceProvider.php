<?php

namespace Modules\Messaging\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class MessagingServiceProvider extends ServiceProvider
{

    const MODULE = 'messaging';

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/messaging-config.php', self::MODULE);
    }

    public function register()
    {
        $this->app->singleton(
            \Modules\Messaging\Contracts\ProducerQueueInterface::class,
            \Modules\Messaging\Drivers\BullMQ\BullMQProducer::class
        );

        // Register Google PubSub as concrete implementation
        $this->app->singleton(
            \Modules\Messaging\Contracts\PubSubInterface::class,
            \Modules\Messaging\Drivers\GooglePubSub\GooglePubSub::class
        );

        // Create \PubSub alias for ease of use
        $loader = AliasLoader::getInstance();
        $loader->alias('PubSub', \Modules\Messaging\Facade\PubSub::class);
    }
}
