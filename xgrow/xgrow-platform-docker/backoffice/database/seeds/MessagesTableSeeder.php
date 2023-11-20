<?php

use App\Message;
use Illuminate\Database\Seeder;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Message::class)->create([
            'user_id'       => 2,
            'recipient_id'  => 1,
        ]);

        factory(Message::class)->create([
            'user_id'       => 3,
            'recipient_id'  => 1,
        ]);


        factory(Message::class, 10)->create([
            'user_id'       => 2,
            'recipient_id'  => 1,
        ]);
    }
}
