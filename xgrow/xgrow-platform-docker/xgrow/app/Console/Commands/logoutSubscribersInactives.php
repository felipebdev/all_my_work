<?php

namespace App\Console\Commands;

use DB;
use App\AccessLog;
use App\Subscriber;
use Illuminate\Console\Command;

class logoutSubscribersInactives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscribers:logout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Efetua logout de assinantes sem atividade por mais de 70 minutos';

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
        $subscribers = AccessLog::where('user_type', 'subscribers')
            ->where('created_at', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL -70 MINUTE)'))
            ->where(DB::raw('SUBSTRING(created_at,1,10)'), '<=', DB::raw('SUBSTRING(NOW(),1,10)'))
            ->groupBy('user_id')
            ->pluck('user_id')
            ->toArray();

        if(count($subscribers) > 0) {

            foreach ($subscribers as $sub) {
                $subscriber = Subscriber::find($sub);
                if ($subscriber !== null) {

                    $logout = AccessLog::select('type')
                        ->where('user_id', $subscriber->id)
                        ->where('user_type', 'subscribers')
                        ->orderBy('created_at', 'DESC')
                        ->first();

                    if ($logout !== null) {

                        if ($logout->type === 'LOGIN') {

                            $query = 'SELECT MAX(created_at) as last_iteration
                                  FROM content_logs a
                                  WHERE user_type = "subscribers"
                                  AND user_id = ' . $subscriber->id;

                            $result = DB::select($query);

                            if (count($result) > 0) {

                                $accessLog = AccessLog::create([
                                    'user_id' => $subscriber->id,
                                    'user_type' => $subscriber->getTable(),
                                    'type' => 'LOGOUT',
                                    'description' => 'Usuario ' . $subscriber->email . ' saiu do site [via api - auto*]',
                                    'platform_id' => $subscriber->platform_id,
                                    'ip' => 'SERVER',
                                    'browser_type' => 'SERVER',
                                    'device_type' => 'SERVER'
                                ]);
                                $accessLog->created_at = $result[0]->last_iteration;
                                $accessLog->save();

                                auth()->setUser($subscriber);

                                auth()->logout(true);

                            }
                        }
                    }
                }
            }
        }
    }
}
