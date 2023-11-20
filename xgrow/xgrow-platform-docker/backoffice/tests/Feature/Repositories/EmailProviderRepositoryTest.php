<?php

namespace Tests\Feature\Repositories;

use App\EmailProvider;
use App\Repositories\EmailProviderRepository;
use App\Services\Objects\EmailProviderFilter;
use Tests\Feature\Traits\EmailProviderTrait;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class EmailProviderRepositoryTest extends TestCase
{
    use EmailProviderTrait;
    protected EmailProviderFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new EmailProviderFilter();
        $this->repository = new EmailProviderRepository();
        parent::setUp();
    }

    public function test_find_all_empty()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(0, $response);
    }

    public function test_find_all()
    {
        EmailProvider::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_find_by_id()
    {
        $emailProvider = EmailProvider::factory()->create();
        $response = $this->repository->findById($emailProvider->id);
        $this->assertEquals($emailProvider->name, $response['name']);
    }

    public function test_find_by_id_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->findById(0);
    }

    public function test_create()
    {
        $data = [
            'settings' => json_encode([
                'key' => '123456',
                'secret' => 'your-ses-secret',
                'region' => 'ses-region (e.g. us-east-1)'
            ]),
            'name' => 'teste',
            'from_name' => 'Teste Email Provider',
            'from_address' => 'address@xgrow.com',
            'driver' => array_random(EmailProvider::DRIVERS),
            'service_tags' => json_encode(['provider, email']),
            'description' => 'Email Provider Description'
        ];

        $response = $this->repository->create($data);

        $this->assertNotNull($response);
        $this->assertIsObject($response);
        $this->assertDatabaseHas('email_providers', [
            'from_address' => $data['from_address'],
        ]);
    }

    public function test_update()
    {
        $data['from_name'] = 'original@xgrow.com';
        $emailProvider = EmailProvider::factory($data)->create();
        $this->assertEquals($data['from_name'], $emailProvider->from_name);

        $data['from_name'] = 'editado@xgrow.com';
        $response = $this->repository->update($emailProvider->id, $data);
        $this->assertNotNull($response);
        $this->assertTrue($response);
        $this->assertDatabaseHas('email_providers', [
            'from_name' => $data['from_name'],
        ]);
    }

    public function test_delete()
    {
        $emailProvider = EmailProvider::factory()->create();

        $deleted = $this->repository->delete($emailProvider->id);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('email_providers', [
            'id' => $emailProvider->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }
}
