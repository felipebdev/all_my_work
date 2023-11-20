<?php

namespace App\Services\Report;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportTransactionService
{

    /**
     * @var string
     */
    public const STATUS =
        [
            'paid' => 'pago',
            'pending' => 'pendente',
            'canceled' => 'cancelado',
            'failed' => 'falhou',
            'chargeback' => 'charge back',
            'expired' => 'expirado'
        ];

    /**
     * Get transactions
     * @param Request $request
     * @return Builder
     */
    public function getTransactions(Request $request): Builder
    {
        set_time_limit(0);

        $queryString = $request->query();
        $order_columns = $queryString['order_columns'] ?? null;
        $order_dirs = $queryString['order_dirs'] ?? null;
        $subscriber_name = $queryString['subscriber_name'] ?? '';
        $subscriber_email = $queryString['subscriber_email'] ?? '';
        $subscriber_document_number = $queryString['subscriber_document_number'] ?? '';
        $subscriber_last_access = $queryString['subscriber_last_access'] ?? '';
        $subscriber_credit_cards_last_four_digits = (string)($queryString['subscriber_credit_cards_last_four_digits'] ?? '');

        $client_cpf = $queryString['client_cpf'] ?? '';
        $client_name = $queryString['client_name'] ?? '';
        $client_cnpj = $queryString['client_cnpj'] ?? '';
        $client_ids = $queryString['client_ids'] ?? [];
        $client_platform = $queryString['client_platform'] ?? '';
        $client_platform_ids = $queryString['client_platform_ids'] ?? [];
        $client_product_ids = $queryString['client_product_ids'] ?? [];
        $client_plan_ids = $queryString['client_plan_ids'] ?? [];

        $payment_status = $queryString['payment_status'] ?? [];
        $payment_date = $queryString['payment_date'] ?? '';
        $payment_value = $queryString['payment_value'] ?? '';

        DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $query = DB::table('payments')
            ->join('subscribers', 'payments.subscriber_id', '=', 'subscribers.id')
            ->join('platforms', 'payments.platform_id', '=', 'platforms.id')
            ->join('clients', 'platforms.customer_id', '=', 'clients.id')
            ->leftJoin('payment_plan', 'payments.id', '=', 'payment_plan.payment_id')
            ->leftJoin('plans', 'payment_plan.plan_id', '=', 'plans.id')
            ->leftJoin('products', 'plans.product_id', '=', 'products.id')
            ->leftJoin('credit_cards', 'payments.subscriber_id', '=', 'credit_cards.subscriber_id')
            ->groupBy('payments.id')
            ->select
            (
                'payments.id as payment_id',
                'subscribers.name as subscriber_name',
                'subscribers.email as subscriber_email',
                'subscribers.document_type as subscriber_document_type',
                'subscribers.document_number as subscriber_document_number',
                'subscribers.last_acess as subscriber_last_access',
                'credit_cards.last_four_digits as subscriber_credit_cards_last_four_digits',

                'clients.id as client_id',
                'clients.cpf as client_cpf',
                'clients.cnpj as client_cnpj',
                DB::raw('CONCAT(clients.first_name, \' \', clients.last_name) as client_full_name'),
                'clients.first_name as client_first_name',
                'clients.last_name as client_last_name',
                'platforms.id as client_platform_id',
                'platforms.name as client_platform',
                DB::raw('GROUP_CONCAT(plans.id) as client_plan_ids'),
                DB::raw("GROUP_CONCAT(plans.name SEPARATOR ', ') as `client_plan`"),
                DB::raw('GROUP_CONCAT(products.id) as client_product_ids'),
                DB::raw("GROUP_CONCAT(products.name SEPARATOR ', ') as `client_product`"),
                'payments.status as payment_status',
                'payments.payment_date as payment_date',
                'payments.subscriber_id as payments_subscriber_id',
                DB::raw('COALESCE(payment_plan.plan_price, payments.price) as payment_value'),
                DB::raw('COALESCE(payment_plan.tax_value, payments.tax_value) as payment_xgrow_value'),
                DB::raw('COALESCE(payment_plan.customer_value, payments.customer_value) as payment_liquid_value'),
                DB::raw('100 - clients.percent_split as client_tax_percentage'),
                'clients.tax_transaction as client_tax_transaction',
                'payments.installments as payments_installments'
            );

        //DB::raw("GROUP_CONCAT(plans.name SEPARATOR '-') as `client_product`"),

        if ($subscriber_name) $query = $query->where('subscribers.name', 'like', "%{$subscriber_name}%");

        if ($subscriber_email) $query = $query->where('subscribers.email', 'like', "%{$subscriber_email}%");

        if ($subscriber_document_number) $query = $query->where('subscribers.document_number', 'like', "%{$subscriber_document_number}%");

        if ($subscriber_last_access) $query = $query->where('subscribers.last_acess', 'like', "%{$subscriber_last_access}%");

        if ($subscriber_credit_cards_last_four_digits) $query = $query->where('credit_cards.last_four_digits', '=', $subscriber_credit_cards_last_four_digits);

        if ($client_name) $query->where(DB::raw('CONCAT_WS(" ", clients.first_name, clients.last_name)'), 'like', "%{$client_name}%");

        if ($client_cpf) $query = $query->where('clients.cpf', 'like', "%{$client_cpf}%");

        if ($client_cnpj) $query = $query->where('clients.cnpj', 'like', "%{$client_cnpj}%");

        if (count($client_ids)) $query = $query->whereIn('clients.id', $client_ids);

        if ($client_platform) $query = $query->where('platforms.name', 'like', "%" . $client_platform . "%");

        if (count($client_platform_ids)) $query = $query->whereIn('platforms.id', $client_platform_ids);

        if (count($client_plan_ids)) $query = $query->whereIn('plans.id', $client_plan_ids);

        if (count($client_product_ids)) $query = $query->whereIn('plans.product_id', $client_product_ids);

        if (count($payment_status)) $query = $query->whereIn('payments.status', $payment_status);

        if ($payment_date) $query = $query->where('payments.payment_date', 'like', "%{$payment_date}%");

        if ($payment_value) $query = $query->where('payments.price', 'like', "%" . $payment_value . "%");


        if(isset($order_columns) and is_array($order_columns)){
            foreach ($order_columns as $key => $order_column){
                $order_dir = $order_dirs[$key] ?? 'asc';
                $query = $query->orderBy($order_column, $order_dir);
            }
        }

        /*
        if (isset($order[0]['column'], $order[0]['dir']))
            foreach ($order as $columnOrder) $query = $query->orderBy($columnOrder['column'], $columnOrder['dir']);
        */

        return $query;
    }

}
