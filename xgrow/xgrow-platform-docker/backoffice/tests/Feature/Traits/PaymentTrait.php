<?php
namespace Tests\Feature\Traits;

use App\Client;
use App\Payment;
use App\Platform;
use App\Subscriber;
use Carbon\Carbon;

trait PaymentTrait{

    /**
     * Generate payments with specific values
     * @return void
     */
    private function createPayments()
    {
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->create();
        $dateNow = Carbon::now()->format('Y-m-d');

        $sub50Days = Carbon::createFromDate($dateNow)->subDays(50)->format('Y-m-d');
        Payment::factory()->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub50Days
        ]); //70.50

        $sub31Days = Carbon::createFromDate($dateNow)->subDays(31)->format('Y-m-d');
        Payment::factory()->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub31Days
        ]); //70.50

        $sub30Days = Carbon::createFromDate($dateNow)->subDays(30)->format('Y-m-d');
        Payment::factory()->count(2)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub30Days
        ]); //201.00
        Payment::factory()->count(3)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_CANCELED,
            'payment_date' => $sub30Days
        ]);
        Payment::factory()->count(2)->create([
            'customer_value' => 70.30,
            'status' => Payment::STATUS_PENDING,
            'payment_date' => $sub30Days
        ]);

        $sub29Days = Carbon::createFromDate($dateNow)->subDays(29)->format('Y-m-d');
        Payment::factory()->count(1)->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub29Days
        ]);//70.50

        $sub23Days = Carbon::createFromDate($dateNow)->subDays(23)->format('Y-m-d');
        Payment::factory()->count(3)->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub23Days
        ]);//211.50
        Payment::factory()->count(2)->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PENDING,
            'payment_date' => $sub23Days
        ]);

        $sub16Days = Carbon::createFromDate($dateNow)->subDays(16)->format('Y-m-d');
        Payment::factory()->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub16Days
        ]);//70.50
        Payment::factory()->count(2)->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_CANCELED,
            'payment_date' => $sub16Days
        ]);

        $sub15Days = Carbon::createFromDate($dateNow)->subDays(15)->format('Y-m-d');
        Payment::factory()->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub15Days
        ]);
        Payment::factory()->count(2)->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub15Days
        ]);//241.50

        $sub7Days = Carbon::createFromDate($dateNow)->subDays(7)->format('Y-m-d');
        Payment::factory()->count(2)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $sub7Days
        ]);
        //201.00

        //TODAY
        Payment::factory()->count(2)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $dateNow
        ]);
        Payment::factory()->create([
            'customer_value' => 70.50,
            'status' => Payment::STATUS_PAID,
            'payment_date' => $dateNow
        ]);
        //271.50
    }

    private function createSummaryData(){
        Client::factory()->create(
            ['created_at' => '2022-05-01']
        );
        Platform::factory()->create();
        Subscriber::factory()->create();

        Payment::factory()->create([
            'customer_value' => 100.50,
            'payment_date' => '2022-05-01',
            'status' => Payment::STATUS_PAID
        ]);
        Payment::factory()->create([
            'customer_value' => 135.70,
            'payment_date' => '2022-05-02',
            'status' => Payment::STATUS_CANCELED,
            'tax_value' => 31.70
        ]);
        Payment::factory()->create([
            'customer_value' => 145.30,
            'payment_date' => '2022-05-05',
            'status' => Payment::STATUS_PAID
        ]);
        Payment::factory()->create([
            'customer_value' => 100.30,
            'payment_date' => '2022-05-06',
            'status' => Payment::STATUS_PAID,
            'tax_value' => 20.50
        ]);
    }

}
