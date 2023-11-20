<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBankSlipExpirationEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = Email::find(13);
        if (!$exists) {
            $message = [
                'Olá ##NOME_ASSINANTE##,',
                '<br /><br />',
                'Passando pra lembrar que o boleto expira hoje. Não perca essa chance. Link abaixo.',
                '<br /><br />',
                'Link para pagamento: ##BOLETO_URL##'
            ];

            Email::create([
                'id' => 13,
                'subject' => 'Vencimento de boleto',
                'message' => join('', $message),
                'from' => 'naoresponda@xgrow.com.br'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Email::find(13)->delete();
    }
}
