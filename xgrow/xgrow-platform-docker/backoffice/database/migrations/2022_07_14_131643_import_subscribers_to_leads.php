<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportSubscribersToLeads extends Migration
{
    public function up()
    {
        $lazySubscribers = DB::table('subscribers')
            ->where('status', 'lead')
            ->orderBy('id')
            ->lazy();

        foreach ($lazySubscribers as $subscriber) {
            if (!$subscriber->plan_id) {
                // ignore subscribers without plan
                continue;
            }

            DB::table('leads')->insert([
                'created_at' => $subscriber->created_at,
                'updated_at' => $subscriber->updated_at,
                'name' => $subscriber->name,
                'email' => $subscriber->email,
                'cel_phone' => $subscriber->cel_phone,
                'document_type' => $subscriber->document_type,
                'document_number' => $subscriber->document_number,
                'address_zipcode' => $subscriber->address_zipcode,
                'address_street' => $subscriber->address_street,
                'address_number' => $subscriber->address_number,
                'address_comp' => $subscriber->address_comp,
                'address_district' => $subscriber->address_district,
                'address_city' => $subscriber->address_city,
                'address_state' => $subscriber->address_state,
                'address_country' => $subscriber->address_country,
                'platform_id' => $subscriber->platform_id,
                'subscriber_id' => $subscriber->id,
                'plan_id' => $subscriber->plan_id,
                //'cart_status' => ,
                //'cart_status_updated_at' => $subscriber,
                'type' => 'product',
                'payment_method' => null,
            ]);
        }
    }

    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
}
