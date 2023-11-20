<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;

class ChangeSuccessRecurrencePaymentMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $message = [
            'Olá ##NOME_ASSINANTE##,<br />',
            '<br />',
            'O pagamento do(s) produto(s) ##PRODUTOS## foi efetuado com sucesso.<br />',
            '<br />',
            'Segue abaixo os dados de confirmação.<br />',
            '<br />',
            'Valor Pago: ##VALOR_PAGO## ##PARCELAS##<br />',
            'Data do pagamento: ##DATA_COBRANCA##<br />',
            '<br />',
            'Link de acesso: ##LINK_PLATAFORMA##<br />',
        ];

        Email::updateOrCreate([
            'id' => 17,
        ], [
            'subject' => 'Pagamento efetuado com sucesso',
            'message' => join(' ', $message),
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
