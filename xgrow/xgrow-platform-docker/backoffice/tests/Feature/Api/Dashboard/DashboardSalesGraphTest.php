<?php

namespace Tests\Feature\Api\Dashboard;

use App\Client;
use App\Payment;
use App\Platform;
use App\Subscriber;
use App\User;
use Carbon\Carbon;
use Tests\Feature\Traits\PaymentTrait;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardSalesGraphTest  extends TestCase
{
    use PaymentTrait;
    private string $endpoint = '/api/dashboard/sales-graph';
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_sales_graph_empty(){
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'data' => ['date', 'label', 'datasets']
                ]
            ]);

        $data = $response['response']['data']['datasets'][0]['data'];
        $this->assertEquals("0.00", $data[0]);
        $this->assertEquals("0.00", $data[1]);
        $this->assertEquals("0.00", $data[2]);
        $this->assertEquals("0.00", $data[3]);
        $this->assertEquals("0.00", $data[4]);
        $this->assertEquals("0.00", $data[5]);
        $this->assertEquals("0.00", $data[6]);
    }

    public function test_sales_graph_without_informed_perioder(){
        $this->createPayments();

        //returns data from the last month
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'data' => ['date', 'label', 'datasets']
                ]
            ]);

        $dateEnd = Carbon::now()->format('Y-m-d');
        $dateStart = Carbon::createFromDate($dateEnd)->subDays(30)->format('Y-m-d');

        $date = $response['response']['data']['date'];
        //first day
        $this->assertEquals($dateStart, $date[0][0]);

        //periods
        for($i = 1; $i < 6; $i++){
            $dateEndPeriod = Carbon::createFromDate($dateStart)
                ->addDays(5)->format('Y-m-d');
            $this->assertEquals($dateStart, $date[$i][0]);
            $this->assertEquals($dateEndPeriod, $date[$i][1]);
            $dateStart = $dateEndPeriod;
        }

        //last day
        $this->assertEquals($dateEnd, $date[6][1]);

        $data = $response['response']['data']['datasets'][0]['data'];
        $this->assertEquals("201.00", $data[0]);
        $this->assertEquals("70.50", $data[1]);
        $this->assertEquals("211.50", $data[2]);
        $this->assertEquals("312.00", $data[3]);
        $this->assertEquals("0.00", $data[4]);
        $this->assertEquals("201.00", $data[5]);
        $this->assertEquals("271.50", $data[6]);
    }

    /**
     * return values from the entered start date to the current date
     * @return void
     */
    public function test_sales_graph_by_date_start(){
        $this->createPayments();

        $dateEnd = Carbon::now()->format('Y-m-d');
        $dateStart = Carbon::createFromDate($dateEnd)->subDays(60)->format('Y-m-d');

        //returns data from the last month
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&date_start={$dateStart}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'data' => ['date', 'label', 'datasets']
                ]
            ]);

        $date = $response['response']['data']['date'];
        //first day
        $this->assertEquals($dateStart, $date[0][0]);

        //periods
        for($i = 1; $i < 6; $i++){
            $dateEndPeriod = Carbon::createFromDate($dateStart)
                ->addDays(10)->format('Y-m-d');
            $this->assertEquals($dateStart, $date[$i][0]);
            $this->assertEquals($dateEndPeriod, $date[$i][1]);
            $dateStart = $dateEndPeriod;
        }

        //last day
        $this->assertEquals($dateEnd, $date[6][1]);

        $data = $response['response']['data']['datasets'][0]['data'];
        $this->assertEquals("0.00", $data[0]);
        $this->assertEquals("70.50", $data[1]);
        $this->assertEquals("0.00", $data[2]);
        $this->assertEquals("271.50", $data[3]);
        $this->assertEquals("282.00", $data[4]);
        $this->assertEquals("312.00", $data[5]);
        $this->assertEquals("472.50", $data[6]);

    }

    /**
     * return values of the last 30 days according to the informed end date
     * @return void
     */
    public function test_sales_graph_by_date_end(){
        $this->createPayments();

        $dateEnd = Carbon::now()->subDays(10)->format('Y-m-d');
        $dateStart = Carbon::createFromDate($dateEnd)->subDays(30)->format('Y-m-d');

        //returns data from the last month
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&date_end={$dateEnd}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'data' => ['date', 'label', 'datasets']
                ]
            ]);

        $date = $response['response']['data']['date'];
        //first day
        $this->assertEquals($dateStart, $date[0][0]);

        //periods
        for($i = 1; $i < 6; $i++){
            $dateEndPeriod = Carbon::createFromDate($dateStart)
                ->addDays(5)->format('Y-m-d');
            $this->assertEquals($dateStart, $date[$i][0]);
            $this->assertEquals($dateEndPeriod, $date[$i][1]);
            $dateStart = $dateEndPeriod;
        }

        //last day
        $this->assertEquals($dateEnd, $date[6][1]);

        $data = $response['response']['data']['datasets'][0]['data'];
        $this->assertEquals("0.00", $data[0]);
        $this->assertEquals("0.00", $data[1]);
        $this->assertEquals("271.50", $data[2]);
        $this->assertEquals("70.50", $data[3]);
        $this->assertEquals("211.50", $data[4]);
        $this->assertEquals("312.00", $data[5]);
        $this->assertEquals("0.00", $data[6]);

    }

    public function test_sales_graph_by_period(){
        $this->createPayments();

        $dateEnd = Carbon::now()->subDays(15)->format('Y-m-d');
        $dateStart = Carbon::createFromDate($dateEnd)->subDays(30)->format('Y-m-d');

        //returns data from the last month
        $response = $this->getJson("{$this->endpoint}?token={$this->token}&date_start={$dateStart}&date_end={$dateEnd}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => [
                    'data' => ['date', 'label', 'datasets']
                ]
            ]);

        $date = $response['response']['data']['date'];
        //first day
        $this->assertEquals($dateStart, $date[0][0]);

        //periods
        for($i = 1; $i < 6; $i++){
            $dateEndPeriod = Carbon::createFromDate($dateStart)
                ->addDays(5)->format('Y-m-d');
            $this->assertEquals($dateStart, $date[$i][0]);
            $this->assertEquals($dateEndPeriod, $date[$i][1]);
            $dateStart = $dateEndPeriod;
        }

        //last day
        $this->assertEquals($dateEnd, $date[6][1]);

        $data = $response['response']['data']['datasets'][0]['data'];
        $this->assertEquals("0.00", $data[0]);
        $this->assertEquals("0.00", $data[1]);
        $this->assertEquals("0.00", $data[2]);
        $this->assertEquals("271.50", $data[3]);
        $this->assertEquals("70.50", $data[4]);
        $this->assertEquals("211.50", $data[5]);
        $this->assertEquals("312.00", $data[6]);
    }



}
