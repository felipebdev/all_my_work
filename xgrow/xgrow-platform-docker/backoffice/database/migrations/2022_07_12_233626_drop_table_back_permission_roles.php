<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableBackPermissionRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('back_permission_roles');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('back_permission_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('back_permission_id')->constrained();
            $table->foreignId('back_role_id')->constrained();
            $table->enum('type_access', ['full', 'restrict']);
            $table->timestamps();
        });
    }
}
