<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class populateFavoritePlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:favorite_plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill favorite plan from products table';

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
        $products = DB::table('products')->whereNull('favorite_plan')->get();

        foreach($products as $product){
           $plan = DB::table('plans')->where('product_id', $product->id)->first();
           if($plan){
             DB::table('products')->where('id', $product->id)
                                  ->update(['favorite_plan' => $plan->id]);
           }
        }
    }
}
