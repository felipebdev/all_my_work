<?php

namespace Tests\Feature\Repositories;

use App\Client;
use App\Platform;
use App\Repositories\PlatformRepository;
use App\Services\Objects\PeriodFilter;
use App\Services\Objects\PlatformFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\Traits\PlatformTrait;
use Tests\TestCase;

class PlatformRepositoryTest extends TestCase
{
    use PlatformTrait;
    protected PlatformFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new PlatformFilter();
        $this->repository = new PlatformRepository();
        parent::setUp();
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        Client::factory()->create();
        Platform::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_create()
    {
        $client = Client::factory()->create();
        $data = [
            'name' => 'México',
            'url' => 'http://www.mexico.com',
            'slug' => 'mexico slug',
            'customer_id' => $client->id,
            'restrict_ips' => '1',
            'ips_available' => 'http://127.0.0.1',
            'name_slug' => 'teste mexico',
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('platforms', [
            'name' => $data['name'],
        ]);
    }

    public function test_update()
    {
        Client::factory()->create();
        Platform::factory()->create();

        $platform = Platform::first();

        $data = [
            'name' => 'Changed name',
        ];

        $response = $this->repository->update($platform->id, $data);

        $this->assertNotNull($response);
        $this->assertDatabaseHas('platforms', [
            'name' => $data['name'],
        ]);
    }

    public function test_soft_delete()
    {
        Client::factory()->create();
        Platform::factory()->create();
        $platform = Platform::first();

        $this->repository->delete($platform->id);

        $this->assertNull(Platform::find($platform->id));
        $this->assertDatabaseHas('platforms', [
            'id' => $platform->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }

    public function test_find_by_id()
    {
        Client::factory()->create();
        Platform::factory()->create();
        $platform = Platform::first();
        $response = $this->repository->findById($platform->id);
        $this->assertEquals($platform->name, $response['name']);
    }

    public function test_find_by_id_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(0);
    }

    public function test_filter_by_search()
    {
        $client = Client::factory()->create();
        $this->createPlatforms($client->id);

        //filter by name
        $this->filter->setSearch('First');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }

    public function test_filter_by_client_Id()
    {
        $clientOne = Client::factory()->create();
        $this->createPlatforms($clientOne->id);

        $clientTwo = Client::factory()->create();
        Platform::factory(
            ['customer_id' => $clientTwo]
        )->count(5)->create();

        //Test get all
        $response = $this->repository->listAll()->get();
        $this->assertCount(8, $response);

        //filter by clientId
        $this->filter->setClientId($clientTwo->id);
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(5, $response);

    }

    public function test_filter_by_created_period()
    {
        $client = Client::factory()->create();
        $this->createPlatforms($client->id);
        $period = new PeriodFilter("2022-05-01", "2022-05-10");

        $this->filter->setCreatedPeriod($period);
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(2, $response);
    }
}
