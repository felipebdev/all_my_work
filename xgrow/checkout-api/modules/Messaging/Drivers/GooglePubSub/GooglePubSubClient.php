<?php

namespace Modules\Messaging\Drivers\GooglePubSub;

use ErrorException;
use Google\Cloud\Core\Exception\ConflictException;
use Google\Cloud\PubSub\PubSubClient;
use Illuminate\Support\Facades\Log;
use JsonException;
use Modules\Messaging\Drivers\GooglePubSub\Exceptions\BadConfigurationGooglePubSubException;

class GooglePubSubClient
{

    /**
     * @param  string  $topicName
     * @return \Google\Cloud\PubSub\PubSubClient
     * @throws \Modules\Messaging\Drivers\GooglePubSub\Exceptions\BadConfigurationGooglePubSubException
     */
    public static function createInstance(string $topicName): PubSubClient
    {
        $emulatorHost = env('PUBSUB_EMULATOR_HOST');
        if ($emulatorHost) {
            return self::createDevelopPubSubClient($topicName);
        }

        return self::createProductionPubSubClient();
    }

    private static function createDevelopPubSubClient(string $topicName): PubSubClient
    {
        $pubSubClient = new PubSubClient();

        try {
            // automatically creates topic in develop if not exists
            $pubSubClient->createTopic($topicName);
        } catch (ConflictException $e) {
            // ignore if topic already exists
        }

        return $pubSubClient;
    }

    private static function createProductionPubSubClient(): PubSubClient
    {
        $credentialsPath = config('messaging.google.pubsub');

        if (!$credentialsPath) {
            Log::warning('Google Pub/Sub credentials path not set');
            throw new BadConfigurationGooglePubSubException('Google Pub/Sub credentials path not set');
        }

        try {
            $contents = file_get_contents($credentialsPath);
        } catch (ErrorException $exception) {
            Log::warning('Google Pub/Sub credentials not found');
            throw new BadConfigurationGooglePubSubException('Google Pub/Sub credentials not found');
        }

        try {
            $json = json_decode($contents, $associative = true, $depth = 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::warning('Google Pub/Sub malformed credentials');
            throw new BadConfigurationGooglePubSubException('Google Pub/Sub malformed credentials');
        }

        return new PubSubClient([
            'keyFile' => $json,
        ]);
    }
}
