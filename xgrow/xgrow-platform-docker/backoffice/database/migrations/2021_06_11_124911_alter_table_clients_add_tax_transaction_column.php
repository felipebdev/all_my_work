<?php

use App\Client;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableClientsAddTaxTransactionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->float('tax_transaction')->default(1.5)->nullable()->after('percent_split');
        });

        // old clients will remain zero rate
        Client::withTrashed()->where('id', '>', 0)
            ->update(['tax_transaction' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('tax_transaction');
        });
    }
}
