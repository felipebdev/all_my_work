<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangeCardEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('emails')->where('id',9)->exists();
        if( !$exists ) {
            DB::table('emails')->insert(
                array(
                    'id' => 9,
                    'subject' => 'Troca de cartão',
                    'message' => 'Olá ##NOME_ASSINANTE##,


Segue abaixo o link para troca do cartão.


Link de acesso: ##LINK_CARTAO##',
                    'from' => 'naoresponda@xgrow.com.br'
                )
            );
        }
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
