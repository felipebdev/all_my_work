<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRecurrencesMailMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $message = [
            'Olá, ##NOME_ASSINANTE##,',
            '<br /><br />',
            'O pagamento do produto ##PRODUTOS## com vencimento em ##DATA_COBRANCA##, ',
            'não foi processado devido a problemas com o meio de pagamento.',
            '<br /><br />',
            'Faremos uma nova tentativa de cobrança dentro de 5 dias.'.'<br />',
            'Caso deseje alterar a forma de pagamento, como por exemplo, trocar seu cartão de crédito, ',
            'basta acessar a plataforma e informar os novos dados de pagamento.',
            '<br /><br />',
            'Para acessar a plataforma utilize esse link: ##LINK_PLATAFORMA##',
            '<br /><br />',
            'Caso o pagamento não seja efetuado com sucesso, o seu acesso ao produto será cancelado até a regularização do pagamento.'.'<br />',
            'Caso tenha alguma dúvida, entre em contato com nossa Equipe de Suporte pelo site https://suporte.xgrow.com.',
            '<br /><br />',
            'Atenciosamente,'.'<br />',
            'Equipe Xgrow.com',
        ];

        Email::updateOrCreate([
            'id' => 14,
        ], [
            'subject' => 'Problemas no pagamento.',
            'message' => join(' ', $message),
            'from' => 'renovacao@xgrow.com'
        ]);

        $message = [
            'Olá, ##NOME_ASSINANTE##',
            '<br /><br />',
            'O pagamento do produto ##PRODUTOS## foi realizado com sucesso.',
            '<br /><br />',
            'Nome do produto: ##PRODUTOS##'.'<br />',
            'ID da Transação: ##ID_TRANSACAO##'.'<br />',
            'Data transação : ##DATA_COBRANCA##'.'<br />',
            'Valor da Transação: ##VALOR_PAGO##'.'<br />',
            '##PARCELAS##',
            '<br /><br />',
            'Caso tenha alguma dúvida, entre em contato com nossa Equipe de Suporte pelo site https://suporte.xgrow.com.',
            '<br /><br />',
            'Atenciosamente,'.'<br />',
            'Equipe Xgrow.com',
        ];

        Email::updateOrCreate([
            'id' => 17,
        ], [
            'subject' => 'Pagamento realizado.',
            'message' => join(' ', $message),
            'from' => 'renovacao@xgrow.com'
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
