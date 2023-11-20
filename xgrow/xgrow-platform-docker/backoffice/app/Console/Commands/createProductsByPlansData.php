<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class createProductsByPlansData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create products by plans data';

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
        $plans = DB::table('plans')->where('product_id', null)->get();
        foreach($plans as $plan){
            $product_id = DB::table('products')->insertGetId(
                [
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'type' => $plan->type_plan,
                    'platform_id' => $plan->platform_id,
                    'category_id' => $plan->category_id,
                    'image_id' => $plan->image_id,
                    'checkout_whatsapp' => $plan->checkout_whatsapp,
                    'checkout_email' => $plan->checkout_email,
                    'checkout_support' => $plan->checkout_support,
                    'checkout_google_tag' => $plan->checkout_google_tag,
                    'checkout_url_terms' => $plan->checkout_url_terms,
                    'checkout_support_platform' => $plan->checkout_support_platform,
                    'checkout_layout' => $plan->checkout_layout,
                    'checkout_support_platform' => $plan->checkout_support_platform,
                    'checkout_layout' => $plan->checkout_layout,
                    'checkout_address' => $plan->checkout_address,
                    'analysis_status' => $plan->analysis_status
                ]
            );
            DB::table('plans')->where('id', $plan->id)->update(['product_id' => $product_id]);
        }
    }
}
