<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableBackPermissionroleActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('back_permission_role_actions');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('back_permission_role_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('back_permission_role_id')->constrained();
            $table->foreignId('back_action_id')->constrained();
            $table->timestamps();
        });
    }
}
