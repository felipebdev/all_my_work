<?php

use Illuminate\Database\Seeder;
use \App\Content;
use DB;
class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contents')->delete();
        
        Content::create([
            'title'=>'Nome teste Conteudo',
            'section_id'=>1,
            'author_id'=>1
        ]);

       
    }
}
