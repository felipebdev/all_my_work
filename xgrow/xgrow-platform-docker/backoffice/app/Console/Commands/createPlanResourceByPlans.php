<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class createPlanResourceByPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:plan_resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create plan_resources by plans';

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
        $plans = DB::table('plans')->whereNotNull('product_id')->get();
        foreach($plans as $plan){

            $plan_resources = DB::table('plan_resources')->whereProductId($plan->product_id);

            if($plan->upsell_plan_id){
                $data = [
                        'product_id' => $plan->product_id,
                        'product_plan_id' => $plan->id,
                        'plan_id' => $plan->upsell_plan_id,
                        'platform_id' => $plan->platform_id,
                        'type' => 'U',
                        'discount' => $plan->upsell_discount,
                        'message' => $plan->upsell_message,
                        'image_id' => $plan->upsell_image_id,
                        'video_url' => $plan->upsell_video_url
                    ];

                $plan_resources_U = $plan_resources->whereType('U');

                if($plan_resources_U->count() > 0)
                    $plan_resources_U->update($data);
                else
                    DB::table('plan_resources')->insert($data);
            }

            if($plan->order_bump_plan_id){
                $data = [
                        'product_id' => $plan->product_id,
                        'product_plan_id' => $plan->id,
                        'plan_id' => $plan->order_bump_plan_id,
                        'platform_id' => $plan->platform_id,
                        'type' => 'O',
                        'discount' => $plan->order_bump_discount,
                        'message' => $plan->order_bump_message,
                        'image_id' => $plan->order_bump_image_id
                    ];

                $plan_resources_O = $plan_resources->whereType('O');

                if($plan_resources_O->count() > 0)
                    $plan_resources_O->update($data);
                else
                    DB::table('plan_resources')->insert($data);
            }

        }
    }
}
