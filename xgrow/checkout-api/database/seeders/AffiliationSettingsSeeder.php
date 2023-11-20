<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AffiliationSettingsSeeder extends Seeder
{
    public function run()
    {
        // sale product
        DB::table('products')->where('id', 1)->update([
            'affiliation_enabled' => 1,
        ]);

        DB::table('affiliation_settings')->insert([
            //"id" => 1,
            "product_id" => 1,
            "approve_request_manually" => 0,
            "receive_email_notifications" => 0,
            "buyers_data_access_allowed" => 0,
            "support_email" => "affiliation-sale@xgrow.com",
            "instructions" => "Affiliation Sale Instructions",
            "commission" => 10.00,
            "cookie_duration" => "0",
            "assignment" => "last_click",
            "invite_link" => "https:\/\/www.xgrow.com\/invitelink_sale",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);

        // subscription product
        DB::table('products')->where('id', 2)->update([
            'affiliation_enabled' => 1,
        ]);

        DB::table('affiliation_settings')->insert([
            //"id" => 1,
            "product_id" => 2,
            "approve_request_manually" => 0,
            "receive_email_notifications" => 0,
            "buyers_data_access_allowed" => 0,
            "support_email" => "affiliation-subscription@xgrow.com",
            "instructions" => "Affiliation Subscription Instructions",
            "commission" => 10.00,
            "cookie_duration" => "0",
            "assignment" => "last_click",
            "invite_link" => "https:\/\/www.xgrow.com\/invitelink_subscription",
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);
    }
}
