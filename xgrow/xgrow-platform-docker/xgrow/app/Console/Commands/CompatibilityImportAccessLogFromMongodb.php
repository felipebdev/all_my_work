<?php

namespace App\Console\Commands;

use App\Subscriber;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class CompatibilityImportAccessLogFromMongodb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:last-access-date-from-mongodb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import last_access date from mongodb';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connectionString = env('MONGODB_INTEGRATION_CONNECTION_STRING');

        $lastRun = Cache::store('redis')->rememberForever("MONGODB_INTEGRATION_LAST_RUN", function () {
            Log::channel('mongodb_integration')->debug('Starting integration from begining');
            return '0';
        });

        Log::channel('mongodb_integration')->debug("Running integration, last run: {$lastRun}");

        $client = new Client($connectionString);
        $collection = $client->selectCollection('platformConfig', 'logs');
        $options = [
            'sort' => ['createdAt' => 1],
        ];
        $documents = $collection->find([
            'createdAt' => [
                '$gt' => new UTCDateTime($lastRun),
            ],
            'actionType' => 'logIn',
            'platformId' => '362a8a3f-b232-49a3-9b79-eb38c54177cb',
        ], $options);

        foreach ($documents as $document) {

            Log::channel('mongodb_integration')->debug("Document found", [
                'document' => $document
            ]);

            $localTimezone = new DateTimeZone('America/Sao_Paulo');

            $utc = $document['createdAt'];
            $date = $utc->toDateTime();
            $date->setTimezone($localTimezone);

            // update subscriber status without touching timestamps
            $id = $document['userId'] ?? 'undefined';
            Log::channel('mongodb_integration')->debug("Updating subscriber {$id}");
            $result = Subscriber::where('id', $id)
                ->update([
                    'last_acess' => $date
                ],
                [
                    'timestamps' => false
                ]);

            Log::channel('mongodb_integration')->debug("Document updating ended.", [
                'result' => $result
            ]);

            $lastDate = $date->format('U');
            $value = $lastDate.'000';

            Log::channel('mongodb_integration')->debug("Updating last date.", ['value' => $value]);

            Cache::store('redis')->put('MONGODB_INTEGRATION_LAST_RUN', $value);
        }

        Log::channel('mongodb_integration')->debug("Ending loop, thanks.");
    }
}
