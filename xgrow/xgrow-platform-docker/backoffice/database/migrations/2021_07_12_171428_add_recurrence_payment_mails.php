<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurrencePaymentMails extends Migration
{


    private $retryFailed = <<< 'EOT'
Olá ##NOME_ASSINANTE##,
<br /><br />
Fizemos em uma nova tentativa de pagamento do(s) produto(s) ##PRODUTOS## em ##DATA_COBRANCA## mas por algum motivo houve falha.
<br /><br />
Caso tenha algum problema com o cartão cadastrado, entre em contato com o suporte: ##LINK_SUPORTE##
EOT;

    private $recurrenceSuccess = <<< 'EOT'
Olá ##NOME_ASSINANTE##,
<br /><br />
O pagamento do(s) produto(s) ##PRODUTOS## foi efetuado com sucesso.
<br /><br />
Segue abaixo os dados de confirmação.
<br /><br />
Valor Pago: ##VALOR_PAGO## ##PARCELAS##
<br />
Data do pagamento: ##DATA_COBRANCA##
<br /><br />
Link de acesso: ##LINK_PLATAFORMA##
<br /><br />
Caso tenha algum problema com o cartão cadastrado, entre em contato com o suporte: ##LINK_SUPORTE##
EOT;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Email::find(16)) {
            Email::create([
                'id' => 16,
                'subject' => 'Aviso de nova tentativa de cobrança falha',
                'message' => $this->retryFailed,
                'from' => 'naoresponda@xgrow.com.br'
            ]);
        }

        if (!Email::find(17)) {
            Email::create([
                'id' => 17,
                'subject' => 'Pagamento efetuado com sucesso',
                'message' => $this->recurrenceSuccess,
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
        Email::find(16)->delete();
        Email::find(17)->delete();
    }
}
