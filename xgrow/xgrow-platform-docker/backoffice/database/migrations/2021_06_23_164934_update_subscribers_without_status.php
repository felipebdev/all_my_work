<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateSubscribersWithoutStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

         $subscribers = DB::table('subscribers')->where('status', null)->get();
          foreach($subscribers as $subscriber){
            $now = Carbon::now();
            $result = DB::select('select `plan_id` from `subscriptions` where `subscriptions`.`subscriber_id` = :subscriber_id and `subscriptions`.`subscriber_id` is not null and `payment_pendent` is null and (`canceled_at` is null or `canceled_at` > :now)', [
                ':subscriber_id' => $subscriber->id,
                ':now' => $now,
            ]);

            $status = (sizeof($result) > 0)  ? 'active' : 'lead';

            DB::table('subscribers')->where('id', $subscriber->id)->update(['status' => $status]);
            
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
