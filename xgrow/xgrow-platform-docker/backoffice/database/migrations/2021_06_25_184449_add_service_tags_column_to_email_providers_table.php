<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceTagsColumnToEmailProvidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('email_providers', function (Blueprint $table) {
            $table->json('service_tags')
                ->nullable()
                ->after('from_address')
                ->comment('JSON array with service tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('email_providers', function (Blueprint $table) {
            $table->dropColumn('service_tags');
        });
    }
}
