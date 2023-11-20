<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewEmailModelToLinkCallcenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('emails')->insert(
        array(
            'id' => 10,
            'subject' => 'Link Call Center',
            'message' => '<div style=\"box-sizing: border-box; font-family: &quot;Segoe UI&quot;, system-ui, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;\"><div>Olá ##NOME_ATENDENTE##,</div>\r\n\r\n<div>&nbsp;</div>\r\n\r\n<div>Seus dados de acesso são os abaixo:</div>\r\n\r\n<div>&nbsp;</div>\r\n\r\n<div>Login: ##EMAIL_ATENDENTE##</div>\r\n\r\n<div>Link de acesso: ##LINK_CALLCENTER##</div></div>',
            'from' => 'naoresponda@fandone.com.br'
            )
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::disableForeignKeyConstraints();
       DB::table('emails')->where('id', 10)->delete();
       Schema::enableForeignKeyConstraints();
    }
}
