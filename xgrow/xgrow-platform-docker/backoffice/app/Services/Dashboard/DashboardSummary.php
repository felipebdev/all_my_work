<?php

namespace App\Services\Dashboard;

use App\Author;
use App\Client;
use App\Content;
use App\Course;
use App\Platform;
use App\Product;
use App\Subscriber;

class DashboardSummary
{

    /**
     * @var Product
     */
    private Product $product;

    /**
     * @var Subscriber
     */
    private Subscriber $subscriber;

    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var Platform
     */
    private Platform $platform;

    /**
     * @var Content
     */
    private Content $content;

    /**
     * @var Author
     */
    private Author $author;

    /**
     * @var Course
     */
    private Course $course;


    /**
     * @param Product $product
     * @param Subscriber $subscriber
     * @param Client $client
     * @param Platform $platform
     * @param Content $content
     * @param Course $course
     * @param Author $author
     */
    public function __construct(
        Product $product,
        Subscriber $subscriber,
        Client $client,
        Platform $platform,
        Content $content,
        Course $course,
        Author $author
    )
    {
        $this->product = $product;
        $this->subscriber = $subscriber;
        $this->client = $client;
        $this->platform = $platform;
        $this->content = $content;
        $this->author = $author;
        $this->course = $course;
    }

    /**
     * Get Summary Data
     * @return array
     */
    public function getInfo(){

        $client['active'] = $this->client
                ->where('verified', true)
                ->count();
        $client['inactive'] = $this->client
                ->where('verified', false)
                ->count();
        $client['total'] = $client['active'] + $client['inactive'];
        $subscriber = $this->subscriber
                            ->where('status', '!=', 'lead')
                            ->count();
        $lead = $this->subscriber
            ->where('status', 'lead')
            ->count();
        $product = $this->product
                        ->where('status', true)
                        ->count();
        $platform = $this->platform->count();
        $content = $this->content->count();
        $course = $this->course->count();
        $author = $this->author
            ->where('status', true)
            ->count();

        return [
            'client' => $client,
            'platform' => $platform,
            'product' => $product,
            'subscriber' => $subscriber,
            'lead' => $lead,
            'content' => $content,
            'course' => $course,
            'author' => $author
        ];
    }
}
