<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(MessagesTableSeeder::class);
        $this->call(NotificationsTableSeeder::class);
        $this->call(ClientsTableSeeder::class);
        // $this->call(TemplateSeeder::class);
        $this->call(PlatformsTableSeeder::class);
        $this->call(PlatformUsersTableSeeder::class);
        $this->call(SubscribersTableSeeder::class);
        // $this->call('SectionsSeeder');
        // $this->call('AuthorSeeder');
        // $this->call('ContentSeeder');

	//$this->call('CommentsTableSeeder');
    }
}