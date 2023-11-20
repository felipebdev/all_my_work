<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLinkPedingEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('emails')->where('id', 11)->update(
        array(
            'subject' => 'Link solicitado',
            'message' => 'Olá ##NOME_ASSINANTE##,
<br />
<br />
Segue abaixo o link para pagamento.
<br />
<br />
Link de acesso: ##LINK_PENDING##'
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
        //
    }
}
