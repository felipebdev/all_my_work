<?php

use App\Comment;
use Illuminate\Database\Seeder;
use DB;
class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comments')->delete();
        factory(Comment::class, 15)->create([
            'contents_id'  => 1,
        ]);
    }
}