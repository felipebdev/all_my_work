<?php

namespace Modules\Messaging\Drivers\GooglePubSub;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Messaging\Contracts\PubSubInterface;
use Modules\Messaging\Drivers\GooglePubSub\Exceptions\BadConfigurationGooglePubSubException;
use Modules\Messaging\Objects\PublishResponse;

class GooglePubSub implements PubSubInterface
{
    public function publishMessage(string $topic, string $message, array $attributes = []): PublishResponse
    {
        try {
            $pubSubClient = GooglePubSubClient::createInstance($topic);

            $publish = ['data' => $message];

            if ($attributes) {
                $publish = array_merge($publish, ['attributes' => $attributes]);
            }

            $published = $pubSubClient
                ->topic($topic)
                ->publish($publish);

            return PublishResponse::ok($published);
        } catch (BadConfigurationGooglePubSubException $exception) {
            // Log and report only

            Log::warning('Bad Configuration Google Pub/Sub', ['message' => $exception->getMessage()]);

            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }

            return PublishResponse::failed(['message' => $exception->getMessage()]);
        } catch (Exception $exception) {
            // Ops, something wrong happened, log, report, but keep running

            Log::info('TriggerNotification Pub/Sub error', [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);

            if (app()->bound('sentry')) {
                app('sentry')->captureException($exception);
            }

            return PublishResponse::failed([
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]);
        }
    }

}
