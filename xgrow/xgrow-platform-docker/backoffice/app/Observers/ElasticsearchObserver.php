<?php

namespace App\Observers;

use Carbon\Carbon;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Config;

class ElasticsearchObserver
{
    private Client $elasticsearch;

    public function __construct(ClientBuilder $elasticsearch)
    {
        $credentials = [[
            'scheme' => Config::get('audit.drive.host.scheme'),
            'port' => Config::get('audit.drive.host.port'),
            'host' => Config::get('audit.drive.host.host'),
            'user' => Config::get('audit.drive.host.user'),
            'pass' => Config::get('audit.drive.host.pass'),
        ]];
        $this->elasticsearch = $elasticsearch::create()->setHosts($credentials)->build();
    }

    public function created($model)
    {
        $extra = [
            'event' => 'created',
            'model_audit' => get_class($model),
            'old_values' => [],
            'new_values' => [
                $model->toSearchJson(),
            ],
            'created_at' => Carbon::now()
        ];

        $data = [
            'index' => Config::get('audit.drive.index'),
            'type' => 'audits',
            'body' => array_merge($extra, $model->getExtraData()),
        ];

        $this->elasticsearch->index($data);
    }

    public function updated($model)
    {
        //
    }

    public function deleted($model)
    {
        //
    }

    public function restored($model)
    {
        //
    }

    public function forceDeleted($model)
    {
        //
    }
}
