<?php

namespace Tests\Feature\Api\Dashboard;

use App\Client;
use App\Payment;
use App\Platform;
use App\Subscriber;
use App\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardSalesSummaryTest  extends TestCase
{
    private string $endpoint = '/api/dashboard/sales-summary';
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_empty_sales_summary_data(){
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => ['sales']
            ]);

        $sales = $response['response']['sales'];
        $this->assertEquals('0.00', $sales['paid']);
        $this->assertEquals('0.00', $sales['canceled']);
        $this->assertEquals('0.00', $sales['chargeback']);
        $this->assertEquals('0.00', $sales['pending']);
    }

    public function test_summary_data(){
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->create();

        Payment::factory()->count(4)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_PAID
        ]);
        Payment::factory()->count(3)->create([
            'customer_value' => 100.50,
            'status' => Payment::STATUS_CANCELED
        ]);
        Payment::factory()->count(2)->create([
            'customer_value' => 70.30,
            'status' => Payment::STATUS_PENDING
        ]);
        Payment::factory()->count(1)->create([
            'customer_value' => 80.50,
            'status' => Payment::STATUS_CHARGEBACK
        ]);
        Payment::factory()->count(1)->create([
            'customer_value' => 50.40,
            'status' => Payment::STATUS_REFUNDED
        ]);

        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => ['sales']
            ]);

        $sales = $response['response']['sales'];
        $this->assertEquals('402.00', $sales['paid']);
        $this->assertEquals('301.50', $sales['canceled']);
        $this->assertEquals('130.90', $sales['chargeback']);
        $this->assertEquals('140.60', $sales['pending']);
    }

}
