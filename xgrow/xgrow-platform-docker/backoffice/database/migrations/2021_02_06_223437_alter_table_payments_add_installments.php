<?php

use App\Services\MundipaggService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsAddInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('installments')->default(0);
        });

        foreach( DB::table('payments')->where('platform_id', '=', '48f0f40d-3d03-47bf-8754-4e759ec9c470')->get() as $cod=>$payment) {

            if (strlen($payment->platform_id) > 0 && strlen($payment->charge_id) > 0) {
                try {
                    $mundipaggService = new MundipaggService($payment->platform_id);
                    $result = $mundipaggService->getClient()->getCharges()->getCharge($payment->charge_id);
                    if (isset($result->lastTransaction)) {
                        if ($result->paymentMethod == 'credit_card') {
                            $installments = $result->lastTransaction->installments;
                        } else {
                            $installments = 1;
                        }
                        $payment = \App\Payment::findOrFail($payment->id);
                        $payment->installments = $installments;
                        $payment->save();
                    }
                }
                catch (\MundiAPILib\APIException $e) {
                    if( !in_array($e->getCode(), [401,404]) ) {
                        dump($e->getCode(), $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('installments');
        });
    }
}
