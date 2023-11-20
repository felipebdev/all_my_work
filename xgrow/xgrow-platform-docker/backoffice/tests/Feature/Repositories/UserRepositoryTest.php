<?php

namespace Tests\Feature\Repositories;

use App\Repositories\UserRepository;
use App\Services\Objects\PeriodFilter;
use App\Services\Objects\UserFilter;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\Traits\UserTrait;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use UserTrait;

    protected UserFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new UserFilter();
        $this->repository = new UserRepository();
        parent::setUp();
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        User::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_create()
    {
        $data = [
            'name' => 'Fulano de Tal',
            'email' => 'fulano@xgrow.com',
            'password' => bcrypt('12345678'),
            'active' => 1,
            'two_factor_enabled' => 1,
            'type_access' => 'full'
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $data = [
            'name' => "{$user->name} changed",
        ];

        $response = $this->repository->update($user->id, $data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
        ]);
    }

    public function test_soft_delete()
    {
        $user = User::factory()->create();
        $this->repository->delete($user->id);
        $this->assertSoftDeleted('users', [
            'id' => $user->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }

    public function test_find_by_id()
    {
        $user = User::factory()->create();
        $response = $this->repository->findById($user->id);
        $this->assertEquals($user->email, $response['email']);
    }

    public function test_find_by_id_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(0);
    }

    public function test_filter_by_search()
    {
        $this->createUsers();

        //filter by first name
        $this->filter->setSearch('First');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

        //filter by email
        $this->filter->setSearch('first_user@xgrow.com');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }

    public function test_filter_by_status()
    {
        $this->createUsers();

        //filter by active
        $this->filter->setStatus(1);
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(2, $response);

        //filter by inactive
        $this->filter->setStatus(0);
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }


}
