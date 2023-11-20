<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableSubscribersAddDefaultCreditCard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscribers', function (Blueprint $table) {
            $table->unsignedBigInteger('credit_card_id')->nullable();
            $table->foreign('credit_card_id')->references('id')->on('credit_cards');
        });

        //Update plan_id column
        foreach (DB::table('subscribers')->get() as $cod=>$subscriber) {
            $creditcard = DB::table('credit_cards')->where('subscriber_id', $subscriber->id)->first();
            if( isset($creditcard->id) ) {
                DB::table('subscribers')->where('id', $subscriber->id)->update(['credit_card_id'=>$creditcard->id]);
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
        Schema::table('subscribers', function (Blueprint $table) {
            $table->dropForeign('credit_card_id_foreign');
            $table->dropColumn('credit_card_id');
        });
    }
}
