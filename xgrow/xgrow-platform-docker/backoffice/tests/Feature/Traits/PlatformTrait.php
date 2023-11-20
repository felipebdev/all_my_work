<?php

namespace Tests\Feature\Traits;

use App\Platform;

trait PlatformTrait{

    /**
     * Generate platforms with specific values
     * @param int $clientId
     * @return void
     */
    private function createPlatforms(int $clientId)
    {
        Platform::factory()->create(
            [
                'id' => 'abcde',
                'name' => 'First',
                'url' => 'https://www.first.com',
                'customer_id' => $clientId,
                'created_at' => '2022-05-01 10:04:01'
            ]
        );
        Platform::factory()->create(
            [
                'id' => 'fghij',
                'name' => 'Second',
                'url' => 'https://www.second.com',
                'customer_id' => $clientId,
                'created_at' => '2022-05-08 12:00:01'
            ]
        );
        Platform::factory()->create(
            [
                'id' => 'klmnop',
                'name' => 'Third',
                'url' => 'https://www.third.com',
                'customer_id' => $clientId,
                'created_at' => '2022-06-09 12:54:01'
            ]
        );
    }

}
