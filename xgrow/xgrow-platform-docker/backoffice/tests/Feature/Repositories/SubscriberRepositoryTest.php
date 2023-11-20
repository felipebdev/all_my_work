<?php

namespace Tests\Feature\Repositories;

use App\Subscriber;
use App\Platform;
use App\Client;
use App\Repositories\SubscriberRepository;
use App\Services\Objects\SubscriberFilter;
use App\Services\Objects\PeriodFilter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Feature\Traits\SubscriberTrait;
use Tests\TestCase;

class SubscriberRepositoryTest extends TestCase
{
    use SubscriberTrait;
    protected SubscriberFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new SubscriberFilter();
        $this->repository = new SubscriberRepository();
        parent::setUp();
    }

    public function test_list_all_subscriber_empty()
    {
        $response = $this->repository->listAll()->get();
        $this->assertCount(0, $response);
    }

    public function test_list_all_subscriber()
    {
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->count(10)->create();
        $response = $this->repository->listAll()->get();
        $this->assertCount(10, $response);
    }

    public function test_list_all_subscriber_with_client_empty()
    {
        $response = $this->repository->listSubscriberClient()->get();
        $this->assertCount(0, $response);
    }

    public function test_list_all_subscriber_with_client()
    {
        Client::factory()->create();
        Platform::factory()->create();
        Subscriber::factory()->count(10)->create();
        $response = $this->repository->listSubscriberClient()->get();
        $this->assertCount(10, $response);
    }
    
    public function test_change_subscriber_status()
    {
        Client::factory()->create();
        Platform::factory()->create();
        $subscriber = Subscriber::factory()->create();
        $status = ['active', 'canceled'];
        $response = $this->repository->changeStatus($subscriber->id, $status[0]);
        $this->assertEquals($status[0], $response->status);
    }

    public function test_soft_delete()
    {
        Client::factory()->create();
        Platform::factory()->create();
        $subscriber = Subscriber::factory()->create();

        $deleted = $this->repository->delete($subscriber->id);
        
        $this->assertSoftDeleted('subscribers', [
            'id' => $subscriber->id
        ]);
    }

    public function test_delete_not_found()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->repository->delete(0);
    }

    public function test_filter_by_search()
    {
        $this->createSubscribers();

        //filter by first name
        $this->filter->setSearch('Joao');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

        //filter by email
        $this->filter->setSearch('maria@xgrow.com');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

        //filter by document number
        $this->filter->setSearch('182.650.247-54');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(1, $response);

    }
    
    public function test_filter_by_status()
    {
        $this->createSubscribers();

        $this->filter->setStatus('active');
        $response = $this->repository->listAll($this->filter)->get();
        $this->assertCount(2, $response);

    }
    
    public function test_filter_by_created_period()
    {
        $this->createSubscribers();
        $period = new PeriodFilter("2021-05-01", "2021-06-30");

        $this->filter->setCreatedPeriod($period);
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(2, $response);
    }

    public function test_filter_by_subscriber_id()
    {
        $this->createSubscribers();

        $this->filter->setSubscribersId([1,3]);
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(2, $response);
    }

    public function test_filter_by_email()
    {
        $this->createSubscribers();

        $this->filter->setEmails(['joao@xgrow.com','maria@xgrow.com']);
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(2, $response);
    }

    public function test_filter_by_document_number()
    {
        $this->createSubscribers();

        $this->filter->setDocumentNumber('761.437.588-24');
        $response = $this->repository->listAll($this->filter)->get();

        $this->assertCount(1, $response);
    }

    public function test_filter_by_client_id()
    {
        $client = Client::factory()->create();
        Platform::factory()->create();
        $subscriber = Subscriber::factory()->count(5)->create();

        $this->filter->setClientId($client->id);
        $response = $this->repository->listSubscriberClient($this->filter)->get();

        $this->assertCount(5, $response);
    }


}
