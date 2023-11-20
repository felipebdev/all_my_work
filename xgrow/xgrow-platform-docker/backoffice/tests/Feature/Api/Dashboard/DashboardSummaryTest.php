<?php

namespace Tests\Feature\Api\Dashboard;

use App\Author;
use App\Content;
use App\Course;
use App\Platform;
use App\Product;
use App\Subscriber;
use App\User;
use App\Client;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DashboardSummaryTest  extends TestCase
{
    private string $endpoint = '/api/dashboard';
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->token = JWTAuth::fromUser($user);
    }

    public function test_empty_summary_data(){
        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => ['summary']
            ]);

        $summary = $response['response']['summary'];
        $client = $summary['client'];
        $this->assertEquals(0, $client['active']);
        $this->assertEquals(0, $client['inactive']);
        $this->assertEquals(0, $client['total']);
        $this->assertEquals(0, $summary['platform']);
        $this->assertEquals(0, $summary['product']);
        $this->assertEquals(0, $summary['subscriber']);
        $this->assertEquals(0, $summary['content']);
        $this->assertEquals(0, $summary['course']);
        $this->assertEquals(0, $summary['author']);
    }

    public function test_summary_data(){
        Client::factory()->count(2)->create(['verified' => true]);
        Client::factory()->create(['verified' => false]);
        Platform::factory()->count(2)->create();
        Product::factory()->count(3)->create(['status' => true]);
        Subscriber::factory()->count(3)->create(['status' => 'active']);
        Subscriber::factory()->count(2)->create(['status' => 'lead']);
        Author::factory()->count(2)->create();
        Content::factory()->count(4)->create(['published' => true]);
        Course::factory()->create(['plan_id' => 0, 'active' => true]);

        $response = $this->getJson("{$this->endpoint}?token={$this->token}");
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'error', 'message', 'response' => ['summary']
            ]);

        $summary = $response['response']['summary'];
        $client = $summary['client'];
        $this->assertEquals(2, $client['active']);
        $this->assertEquals(1, $client['inactive']);
        $this->assertEquals(3, $client['total']);
        $this->assertEquals(2, $summary['platform']);
        $this->assertEquals(3, $summary['product']);
        $this->assertEquals(3, $summary['subscriber']);
        $this->assertEquals(4, $summary['content']);
        $this->assertEquals(1, $summary['course']);
        $this->assertEquals(2, $summary['author']);

    }

}
