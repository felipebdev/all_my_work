<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldOnProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('status')->default(false);
        });
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $plan = DB::table('plans')
                ->where(['product_id' => $product->id])->first();
            if ($plan) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['status' => $plan->status]);
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
