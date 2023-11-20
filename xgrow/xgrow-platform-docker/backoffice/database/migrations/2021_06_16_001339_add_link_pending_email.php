<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLinkPendingEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = DB::table('emails')->where('id',11)->exists();
        if( !$exists ) {
            DB::table('emails')->insert(
                array(
                    'id' => 11,
                    'subject' => 'Pagamento produto pendente',
                    'message' => 'Olá ##NOME_ASSINANTE##,
<br />
<br />
Segue abaixo o link para pagamento do produto pendente.
<br />
<br />
Link de acesso: ##LINK_PENDING##',
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
