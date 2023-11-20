<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $plans;

    public function __construct(Plan $plans)
    {
        $this->plans = $plans;
    }

    public function index()
    {
        $all_products = $this->plans
            ->select()
            ->with(
                [
                    'platform' => function ($q) {
                        $q->with('client');
                    }
                ]
            )
            ->get();

        $approved_products = $all_products->filter(function ($product) {
            return $product->analysis_status == 'approved';
        });
        
        $analysis_products = $all_products->filter(function ($product) {
            return $product->analysis_status == 'under_analysis';
        });
        
        $refused_products = $all_products->filter(function ($product) {
            return $product->analysis_status == 'refused';
        });
        
        $blocked_products = $all_products->filter(function ($product) {
            return $product->analysis_status == 'blocked';
        });
            
        $data['all_products'] = $all_products;
        $data['approved_products'] = $approved_products;
        $data['analysis_products'] = $analysis_products;
        $data['refused_products'] = $refused_products;
        $data['blocked_products'] = $blocked_products;
        return view('products.index', $data);
    }

    public function show($id)
    {
        $product = $this->plans
            ->select()
            ->with(
                [
                    'platform' => function ($q) {
                        $q->with('client');
                    },
                    'image',
                    'order_bump_image',
                    'upsell_image',
                ]
            )
            ->where('id', $id)
            ->first();
        $data['product'] = $product;

        return view('products.show', $data);
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $status = $request->analysis_status;

            if (!in_array($status, ['approved', 'refused', 'under_analysis', 'blocked'])) {
                return response()->json(['message' => 'Status inválido'], 400);
            }

            $product = $this->plans->select()->where('id', $id)->first();
            $product->analysis_status = $status;
            $product->save();

            return response()->json(['message' => 'Produto atualizado com sucesso'], 200);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
