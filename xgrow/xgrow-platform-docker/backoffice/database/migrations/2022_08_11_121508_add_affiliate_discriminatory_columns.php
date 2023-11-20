<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAffiliateDiscriminatoryColumns extends Migration
{
    public function up()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->string('type', 1)->default('P')->after('platform_user_id')->comment('P: producer, A: affilate');
        });
    }
    
    public function down()
    {
        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
