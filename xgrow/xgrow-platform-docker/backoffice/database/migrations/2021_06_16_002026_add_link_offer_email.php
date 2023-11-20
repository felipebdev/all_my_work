<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkOfferEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('emails')->where('id',12)->exists();
        if( !$exists ) {
            DB::table('emails')->insert(
                array(
                    'id' => 12,
                    'subject' => 'Link produto oferecido',
                    'message' => 'Olá ##NOME_ASSINANTE##,
<br />
<br />
Segue abaixo o link para pagamento de produto oferecido.
<br />
<br />
Link de acesso: ##LINK_OFFER##',
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
