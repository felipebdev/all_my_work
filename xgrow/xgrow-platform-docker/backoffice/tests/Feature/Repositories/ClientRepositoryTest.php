<?php

namespace Tests\Feature\Repositories;

use App\Client;
use App\Platform;
use App\Repositories\ClientRepository;
use App\Services\Objects\ClientFilter;
use App\Services\Objects\PeriodFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\Traits\ClientTrait;
use Tests\TestCase;

class ClientRepositoryTest extends TestCase
{
    use ClientTrait;
    protected ClientFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new ClientFilter();
        $this->repository = new ClientRepository();
        parent::setUp();
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        Client::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_create()
    {
        $data = [
            'first_name' => 'First',
            'last_name' => 'Last',
            'email' => 'first_client@xgrow.com',
            'password' => bcrypt('12345678'),
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('clients', [
            'email' => $data['email'],
        ]);
    }

    public function test_update()
    {
        $client = Client::factory()->create();

        $data = [
            'first_name' => 'New name',
        ];

        $response = $this->repository->update($client->id, $data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('clients', [
            'first_name' => $data['first_name'],
        ]);
    }

    public function test_force_delete()
    {
        $client = Client::factory()->create();

        $deleted = $this->repository->delete($client->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('clients', [
            'id' => $client->id
        ]);
    }

    public function test_soft_delete()
    {
        $client = Client::factory()->create();
        Platform::factory()->create();

        $deleted = $this->repository->delete($client->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseHas('clients', [
            'id' => $client->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }

    public function test_find_by_id()
    {
        $client = Client::factory()->create();
        $response = $this->repository->findById($client->id);
        $this->assertEquals($client->email, $response['email']);
    }

    public function test_find_by_id_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(0);
    }

    public function test_filter_by_search()
    {
        $this->createClients();

        //filter by first name
        $this->filter->setSearch('First');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

        //filter by last name
        $this->filter->setSearch('Xgrow');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(3, $response);

        //filter by email
        $this->filter->setSearch('first_client@xgrow.com');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }

    public function test_filter_by_created_period()
    {
        $this->createClients();
        $period = new PeriodFilter("2022-05-01", "2022-05-10");

        $this->filter->setCreatedPeriod($period);
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(2, $response);
    }


}
