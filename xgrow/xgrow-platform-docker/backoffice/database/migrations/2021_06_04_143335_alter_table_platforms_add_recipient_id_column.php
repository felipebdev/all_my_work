<?php

use App\Client;
use App\Platform;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AlterTablePlatformsAddRecipientIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('recipient_id')->nullable();
        });

        $clients = Client::withTrashed()->get();

        foreach ($clients as $client) {
            $platforms = Platform::where('customer_id', intval($client->id))->get();
            $countPlatforms = count($platforms);
            print "Cliente: $client->id | Plataformas: $countPlatforms \r\n";

            if ($platforms) {
                try {
                    foreach ($platforms as $platform) {
                        $p = Platform::find($platform->id);
                        if ($p) {
                            $p->recipient_id = $client->recipient_id;
                            $p->save();
                        }
                    }
                } catch (Exception $e) {
                    Log::error('Erro ao atualizar platform. Error ' . $e->getMessage());
                }
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
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
        });
    }
}
