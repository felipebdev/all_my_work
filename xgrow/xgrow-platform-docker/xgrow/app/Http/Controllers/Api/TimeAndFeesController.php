<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\MyData\MyDataRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class TimeAndFeesController extends Controller
{

    private MyDataRepository $myDataRepository;

    /**
     * @param MyDataRepository $myDataRepository
     */
    public function __construct(MyDataRepository $myDataRepository)
    {
        $this->myDataRepository = $myDataRepository;
    }

    /**
     * @return array
     */
    public function information(): array
    {

        $email = Auth::user()->email;
        $data = $this->myDataRepository->getFees($email);

        if($data['percent_split'] < 100 or $data['tax_transaction'] > 0){

            $fees = "A Xgrow cobra ";
            $percent_split = number_format(100 - $data['percent_split'], 2, ',', '.');
            $tax_transaction = number_format($data['tax_transaction'], 2, ',', '.');

            if($data['percent_split'] < 100 xor $data['tax_transaction'] > 0){
                $fees .= $data['percent_split'] < 100 ? $percent_split . "%" : "R$ " . $tax_transaction;
            }
            else{
                $fees .= "{$percent_split}% + R$ {$tax_transaction}";
            }

            $fees .= " apenas sobre as vendas aprovadas, o cliente só paga se vender.";
        }

        return [
            'fees' => $fees,
            'deadlines_for_receipt' => [
                'cards' => '30 dias',
                'boletos' => '1 dia',
                'pix' => 'instantâneo',
            ]
        ];
    }
}
