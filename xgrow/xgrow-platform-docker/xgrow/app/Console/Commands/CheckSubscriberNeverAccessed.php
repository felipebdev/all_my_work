<?php

namespace App\Console\Commands;

use App\Subscriber;
use Illuminate\Console\Command;

class CheckSubscriberNeverAccessed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribers:never-accessed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica quais assinantes nunca acessaram a plataforma e cadastra na tabela app_actions_never_accessed';

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
        Subscriber::neverAccessed();
    }
}
