<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableRecurrencesAddAffiliateIdColumn extends Migration
{

    public function up()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->unsignedBigInteger('affiliate_id')->nullable()->after('default_installments')->comment('Original affiliate');

            $table->foreign('affiliate_id')->references('id')->on('producers');
        });
    }

    public function down()
    {
        Schema::table('recurrences', function (Blueprint $table) {
            $table->dropForeign(['affiliate_id']);
            $table->dropColumn('affiliate_id');
        });
    }
}
