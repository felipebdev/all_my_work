<?php

use Illuminate\Database\Seeder;
use \App\Author;
use DB;
class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('authors')->delete();
        Author::create([
            'author_email'=>'emailTesteAutor@email.com',
        ]);

       
    }
}
