<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPaymentsPlansValue extends Migration
{
    public function up()
    {
        DB::statement('UPDATE payments SET plans_value = customer_value + tax_value  WHERE price = plans_value AND installments > 1');
    }

    public function down()
    {
        //
    }
}
