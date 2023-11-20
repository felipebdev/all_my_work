<?php

namespace Tests\Feature\Traits;

use App\User;

trait UserTrait
{
    private function createUsers()
    {
        User::factory()->create(
            [
                'name' => 'First User',
                'email' => 'first_user@xgrow.com',
                'password' => bcrypt('12345678')
            ]
        );
        User::factory()->create(
            [
                'name' => 'Second User',
                'email' => 'second_user@xgrow.com',
                'password' => bcrypt('12345678')
            ]
        );
        User::factory()->create(
            [
                'name' => 'Third User',
                'email' => 'third_user@xgrow.com',
                'password' => bcrypt('12345678'),
                'active' => 0
            ]
        );
    }
}
