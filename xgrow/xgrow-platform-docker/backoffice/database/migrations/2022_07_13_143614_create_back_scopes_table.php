<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackScopesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('back_scopes', function (Blueprint $table) {
            $table->foreignId('back_permission_id')->constrained();
            $table->foreignId('back_role_id')->constrained();
            $table->enum('type_access', ['full', 'restrict'])->default('full');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('back_scopes');
    }
}
