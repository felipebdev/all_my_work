<?php

use App\Services\MundipaggService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTablePaymentsUpdateStatusMundipagg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->float('customer_value')->nullable();
            $table->float('service_value')->nullable();
        });

        foreach (DB::table('payments')->where('platform_id', '=', '48f0f40d-3d03-47bf-8754-4e759ec9c470')->get() as $cod => $payment) {
            if (strlen($payment->platform_id) > 0 && strlen($payment->charge_id) > 0) {
                try {
                    $mundipaggService = new MundipaggService($payment->platform_id);
                    $result = $mundipaggService->getClient()->getCharges()->getCharge($payment->charge_id);
                    if (isset($result->lastTransaction)) {
                        $payment = \App\Payment::findOrFail($payment->id);
                        if( $result->lastTransaction->split ) {
                            foreach ($result->lastTransaction->split as $c => $split) {
                                if ($split->options->chargeProcessingFee == true) { //Xgrow
                                    $payment->service_value = $split->amount / 100;
                                } else { //Customer
                                    $payment->customer_value = $split->amount / 100;
                                }
                            }
                        }
                        $payment->status = $result->status;
                        $payment->save();
                    }
                } catch (\MundiAPILib\APIException $e) {
                    if (!in_array($e->getCode(), [401, 404])) {
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
            $table->dropColumn('customer_value');
            $table->dropColumn('service_value');
        });
    }
}
