<?php

namespace Tests\Feature\Traits;

use App\Client;
use App\Platform;
use App\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

trait SubscriberTrait{

    /**
     * Generate clients with specific values
     * @return void
     */
    private function createSubscribers()
    {
        
        Client::factory()->create();
        Platform::factory()->create();
        $platforms = Platform::pluck('id');

        Subscriber::factory()->create(
            [
                'id' => 1,
                'name' => 'Joao',
                'email' => 'joao@xgrow.com',
                'document_type' => 'CPF',
                'document_number' => '761.437.588-24',
                'status' => 'active',
                'platform_id' => $platforms[0],
                'created_at' => '2021-05-29 12:21:28',
                'updated_at' => '2022-05-29 12:21:28',
            ]
        );
        Subscriber::factory()->create(
            [
                'id' => 2,
                'name' => 'Maria',
                'email' => 'maria@xgrow.com',
                'document_type' => 'CPF',
                'document_number' => '891.146.466-09',
                'status' => 'active',
                'platform_id' => $platforms[0],
                'created_at' => '2021-06-29 12:21:28',
                'updated_at' => '2022-06-29 12:21:28',
            ]
        );
        Subscriber::factory()->create(
            [
                'id' => 3,
                'name' => 'Jose',
                'email' => 'jose@xgrow.com',
                'document_type' => 'CPF',
                'document_number' => '182.650.247-54',
                'status' => 'canceled',
                'platform_id' => $platforms[0],
                'created_at' => '2021-07-29 12:21:28',
                'updated_at' => '2022-07-29 12:21:28',
            ]
        );
    }


}
