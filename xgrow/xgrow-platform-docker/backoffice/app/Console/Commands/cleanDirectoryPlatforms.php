<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;

class cleanDirectoryPlatforms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:platforms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes directories from excluded platforms';

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
        $cutStart = date('Y-m-d', strtotime('-16 days', strtotime(now())));
        $cutFinish = date('Y-m-d', strtotime('-14 days', strtotime(now())));
        $platforms = Platform::whereBetween('deleted_at', [$cutStart, $cutFinish])->onlyTrashed()->get();
        foreach ($platforms as $platform)
        {
            if (PHP_OS === 'WINNT') {
                exec("rd /s /q c:\\teste-folder\\{$platform->name_slug}");
            } else {
                exec("rm -rf /var/clients-sites/{$platform->name_slug}");
            }
        }
    }
}
