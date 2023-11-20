<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsusersAddColumnPermissionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            $table->bigInteger('permission_id')->nullable()->unsigned();
            $table->foreign('permission_id')->references('id')->on('permissions');
        }
      );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('platforms_users', function (Blueprint $table) {
            //
        });
    }
}
