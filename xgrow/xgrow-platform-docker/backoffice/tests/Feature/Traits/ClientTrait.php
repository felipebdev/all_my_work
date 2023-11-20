<?php

namespace Tests\Feature\Traits;

use App\Client;

trait ClientTrait{

    /**
     * Generate clients with specific values
     * @return void
     */
    private function createClients()
    {
        Client::factory()->create(
            [
                'first_name' => 'First',
                'last_name' => 'Xgrow',
                'email' => 'first_client@xgrow.com',
                'password' => bcrypt('12345678'),
                'created_at' => '2022-05-01 10:04:01'
            ]
        );
        Client::factory()->create(
            [
                'first_name' => 'Second',
                'last_name' => 'Xgrow',
                'email' => 'second_client@xgrow.com',
                'password' => bcrypt('12345678'),
                'created_at' => '2022-05-08 12:00:01'
            ]
        );
        Client::factory()->create(
            [
                'first_name' => 'Third',
                'last_name' => 'Last',
                'email' => 'third_client@xgrow.com',
                'password' => bcrypt('12345678'),
                'created_at' => '2022-06-09 12:54:01'
            ]
        );
    }


}
