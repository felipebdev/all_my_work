<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrencePaymentFailedCancelSubscriptionEmail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $exists = Email::find(15);
        if (!$exists) {
            $message = [
                'Olá ##NOME_ASSINANTE##,',
                '<br /><br />',
                'O que aconteceu?',
                '<br />',
                'Infelizmente o pagamento do(s) produto(s) ##PRODUTOS## com data de cobrança em ##DATA_COBRANCA## mesmo após algumas tentativas de cobrança, não foi realizado dentro do prazo. Por esse motivo, o seu acesso será cancelado.',
                '<br /><br />',
                'Caso tenha alguma dúvida, entre em contato com o suporte: ##LINK_SUPORTE##'
            ];

            Email::create([
                'id' => 15,
                'subject' => 'Aviso de cancelamento de produto',
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
        Email::find(15)->delete();
    }
}
