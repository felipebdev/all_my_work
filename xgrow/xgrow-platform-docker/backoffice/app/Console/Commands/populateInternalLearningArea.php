<?php

namespace App\Console\Commands;

use App\Platform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class populateInternalLearningArea extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:internalla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate internal learning area';

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

        DB::table('products')->where('external_learning_area', false)
                            ->where('internal_learning_area', false)
                            ->where('only_sell', false)
                            ->update(['internal_learning_area' => true]);


         $products = DB::table('products')->where('internal_learning_area', true)->get();

         foreach($products as $product){
            $has_section = DB::table('section_product')->where('product_id', $product->id)->first();

            if(!$has_section){
                DB::insert("INSERT INTO section_product(section_id, product_id) SELECT s.id,? FROM sections s WHERE s.active='1'",[$product->id]);
            }

            $has_course = DB::table('course_product')->where('product_id', $product->id)->first();

            if(!$has_course){
                DB::insert("INSERT INTO course_product(course_id, product_id) SELECT s.id,? FROM courses s WHERE s.active='1'",[$product->id]);
            }

         }

        /*
        $plans = DB::table('plans')->whereNotNull('product_id')->get();
        foreach($plans as $plan){

            $sections = DB::table('section_plan')->wherePlanId($plan->id);

            foreach($sections->get() as $section){
               $section_product = DB::table('section_product')->whereSectionId($section->section_id)->whereProductId($plan->product_id);

               if($section_product->count() == 0)
                    DB::table('section_product')->insert(
                        [
                            'section_id' => $section->section_id,
                            'product_id' => $plan->product_id,
                        ]
                    );

            }

            $courses = DB::table('course_plan')->wherePlanId($plan->id);

            foreach($courses->get() as $course){
               $course_product = DB::table('course_product')->whereCourseId($course->course_id)->whereProductId($plan->product_id);

               if($course_product->count() == 0)
                    DB::table('course_product')->insert(
                        [
                            'course_id' => $course->course_id,
                            'product_id' => $plan->product_id,
                        ]
                    );

            }

            $unlimited_delivery = ($sections->count() > 0 or $courses->count() > 0 ) ? 0 : 1;

            DB::table('products')->whereId($plan->product_id)->update(['unlimited_delivery' => $unlimited_delivery]);

        }
        */
    }
}
