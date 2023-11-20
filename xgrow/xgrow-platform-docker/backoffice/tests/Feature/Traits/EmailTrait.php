<?php

namespace Tests\Feature\Traits;

use App\Email;

trait EmailTrait{

    /**
     * Generate clients with specific values
     * @return void
     */
    private function createEmails()
    {
        Email::factory()->create(
            [
                'id' => 1,
                'area' => '1',
                'subject' => 'Opção ABC',
                'message' => 'Esta é a opção messageABC',
                'from' => 'messageABC@xgrow.com',
                'created_at' => '2021-01-08 12:00:01',
                'updated_at' => '2022-01-08 12:00:01'
            ]
        );
        Email::factory()->create(
            [
                'id' => 2,
                'area' => '2',
                'subject' => 'Opção DEF',
                'message' => 'Esta é a opção messageDEF',
                'from' => 'messageDEF@xgrow.com',
                'created_at' => '2021-02-08 12:00:01',
                'updated_at' => '2022-02-08 12:00:01'
            ]
        );
        Email::factory()->create(
            [
                'id' => 3,
                'area' => '3',
                'subject' => 'Opção GHI',
                'message' => 'Esta é a opção messageGHI',
                'from' => 'messageGHI@xgrow.com',
                'created_at' => '2022-03-08 12:00:01',
                'updated_at' => '2022-03-08 12:00:01'
            ]
        );
    }


}
