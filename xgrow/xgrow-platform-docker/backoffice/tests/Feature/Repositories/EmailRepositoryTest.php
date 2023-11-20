<?php

namespace Tests\Feature\Repositories;

use App\Email;
use App\Repositories\EmailRepository;
use App\Services\Objects\EmailFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\Traits\EmailTrait;
use Tests\TestCase;

class EmailRepositoryTest extends TestCase
{
    use EmailTrait;
    protected EmailFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new EmailFilter();
        $this->repository = new EmailRepository();
        parent::setUp();
    }

    public function test_find_ten_defaults_values()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_find_all_included_created_values()
    {
        Email::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(20, $response);
    }

    public function test_filter_by_search()
    {
        $this->createEmails();

        //filter by subject
        $this->filter->setSearch('Opção DEF');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

        //filter by email
        $this->filter->setSearch('messageGHI@xgrow.com');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }

    public function test_create()
    {
        $data = [
            'area' => '2',
            'subject' => 'Ação ABC',
            'message' => 'E-mail de resposta para a ação ABC.',
            'from' => 'acaoABC@xgrow.com',
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('emails', [
            'from' => $data['from'],
        ]);
    }

    public function test_update()
    {
        $email = Email::factory()->create();

        $data = [
            'from' => 'acaoXYZ@xgrow.com',
        ];

        $response = $this->repository->update($email->id, $data);

        $this->assertNotNull($response);
        $this->assertTrue($response);
        $this->assertDatabaseHas('emails', [
            'from' => $data['from'],
        ]);
    }
    
    public function test_delete()
    {
        $email = Email::factory()->create();

        $deleted = $this->repository->delete($email->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('emails', [
            'id' => $email->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }

}
