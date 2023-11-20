<?php

use App\Email;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPixEmail extends Migration
{
    private $message = <<< 'EOT'
Olá ##NOME_ASSINANTE##,
<br /><br />
Segue abaixo dados para renovação via PIX.
<br /><br />
Link para pagamento: ##PIX_URL##
<br />
QR Code: ##PIX_QRCODE##
EOT;

    public function up()
    {
        if (!Email::find(18)) {
            Email::create([
                'id' => 18,
                'subject' => 'Dados para pagamento da assinatura via PIX',
                'message' => $this->message,
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
        Email::find(18)->delete();
    }
}
