<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBankSlipExpirationMailMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $message = [
            'Olá ##NOME_ASSINANTE##',
            '<br /><br />',
            'Passando para lembrar que o boleto emitido em ##DATA_EMISSAO## vence em breve. Não perca essa chance.',
            '<br /><br />',
            'Link para pagamento: ##BOLETO_URL##',
            '<br /><br />',
            'Informações da compra',
            '<br /><br />',
            '##PRODUTOS##',
            '<br />',
            'Valor total: ##VALOR_PAGO##',
            '<br /><br />',
            'Caso já tenha efetuado o pagamento, por favor ignore essa mensagem.',
            '<br /><br />',
            '##MENSAGEM_SUPORTE##',
            '<br />',
        ];

        Email::updateOrCreate([
            'id' => 13,
        ], [
            'subject' => 'Vencimento de boleto',
            'message' => join('', $message),
            'from' => 'naoresponda@xgrow.com.br'
        ]);
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
