<?php

namespace App\Services\Subscriber;

use App\Repositories\Contracts\SubscriberRepositoryInterface;
use Exception;

class SubscriberProductService{


    protected SubscriberRepositoryInterface $subscriber;

    public function __construct(SubscriberRepositoryInterface $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    /**
     * @param $inputs
     * @return mixed
     * @throws Exception
     */
    public function getProducts($id, $inputs){

        $id = $id ?? null;
        $search = $inputs['search'] ?? null;
        $status = $inputs['status'] ?? null;
        //criar filtro do status no SubscriberFilter

        //  $filter = (new SubscriberFilter())
        //      ->setSearch($search);
        //      ->setStatus($status);

        dd($this->subscriber);
        $products = $this->subscriber->get();

        // return $products
        //                 ->select('image_product', 'product_name', 'platform_name', 'created_at', 'status', 'product_id')
        //                 ->get();
        return $products;
    }

}
