<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrencePaymentFailedEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = Email::find(14);
        if (!$exists) {
            $message = [
                'Olá ##NOME_ASSINANTE##,',
                '<br /><br />',
                'Estou passando aqui pra avisar que o pagamento do(s) produto(s) ##PRODUTOS## com data de cobrança em ##DATA_COBRANCA## por algum motivo falhou. ',
                'Mas fique tranquilo, vamos fazer uma nova tentativa amanhã ##DATA_NOVA_COBRANCA##.',
                '<br /><br />',
                'Caso tenha algum problema com o cartão cadastrado, entre em contato com o suporte: ##LINK_SUPORTE##'
            ];

            Email::create([
                'id' => 14,
                'subject' => 'Aviso de tentativa de cobrança falha',
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
        Email::find(14)->delete();
    }
}
