<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmailLinkToCallcenterRemoveCharactersSpecial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('emails')->where('id', 10)->update(
        array(
            'message' => '<div style=\"box-sizing: border-box; font-family: &quot;Segoe UI&quot;, system-ui, &quot;Apple Color Emoji&quot;, &quot;Segoe UI Emoji&quot;, sans-serif; font-size: 14px; font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-weight: 400; letter-spacing: normal; orphans: 2; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 2; word-spacing: 0px;\"><div>Olá ##NOME_ATENDENTE##,</div><div>&nbsp;</div><div>Seus dados de acesso são os abaixo:</div><div>&nbsp;</div><div>Login: ##EMAIL_ATENDENTE##</div><div>Link de acesso: ##LINK_CALLCENTER##</div></div>'
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
        
    }
}
