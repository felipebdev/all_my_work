<?php

namespace App\Repositories\Dashboard;

use App\Payment;
use App\Product;
use App\Repositories\BaseRepository;
use App\Repositories\Contracts\ProductSaleRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use stdClass;
use http\Exception;


class ProductSaleRepository extends BaseRepository implements ProductSaleRepositoryInterface
{

    public function model()
    {
        return Payment::class;
    }

    public function getData($start, $end, $product_id): array
    {

        $period = new ManagePeriod($start, $end, Auth::user()->platform_id);

        $data['label'] = $period->getLabel();

        $datasets[0] = new stdClass();
        $datasets[0]->label = "Total de vendas";

        if($product_id != 0){
            $product = Product::find($product_id);
            $datasets[0]->label = $product->name;
        }

        $datasets[0]->data = $period->getSalesProduct($product_id);
        $datasets[0]->backgroundColor = 'rgba(173, 255, 47, .9)';
        $datasets[0]->borderColor = 'rgba(173, 255, 47, .2)';

        $data['datasets'] = $datasets;


        return $data;
    }

    

}
