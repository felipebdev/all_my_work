<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class linkToPlatformSiteConfigs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'platform:link_to_platformsiteconfigs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link Platforms to paltform_site_configs table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $platforms = DB::table('platforms')->get();

        foreach($platforms as $platform){
           $PSC = DB::table('platform_site_configs')->where('platform_id', $platform->id)->first();
           if(!$PSC){
               DB::table('platform_site_configs')->insert(
                   [
                       'platform_id' => $platform->id,
                   ]
               );
           }
        }
    }
}
