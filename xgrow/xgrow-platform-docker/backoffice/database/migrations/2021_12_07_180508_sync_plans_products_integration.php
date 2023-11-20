<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Plan;

class SyncPlansProductsIntegration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $plans = DB::table('app_action_products')->select('plan_id')->whereNull('product_id')->get();
        foreach($plans as $plan){
            $product_id = Plan::find($plan->plan_id)->product_id;
            DB::table('app_action_products')
                        ->where('plan_id', $plan->plan_id)
                        ->update(['product_id' => $product_id]);
        }

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
