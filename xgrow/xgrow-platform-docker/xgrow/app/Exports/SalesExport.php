<?php

namespace App\Exports;

use App\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;


class SalesExport implements FromQuery, WithCustomQuerySize
{
    use Exportable;

    public function querySize(): int
    {
        return 500;
    }

    public function query()
    {
        return Payment::select(DB::raw('
                subscribers.id AS subscribers_id,
                subscribers.name AS subscribers_name,
                subscribers.email AS subscribers_email,
                subscribers.document_type,
                subscribers.document_number,
                subscribers.cel_phone,
                subscribers.address_street,
                subscribers.address_number,
                subscribers.address_comp,
                subscribers.address_district,
                subscribers.address_zipcode,
                subscribers.address_city,
                subscribers.address_state,
                subscribers.address_country,
                payments.installments,
                payments.customer_value,
                payments.service_value,
                payments.price,
                payments.plans_value,
                payments.tax_value,
                payments.payment_date,
                payments.status AS payments_status,
                payments.order_code AS transactions_id,
                payments.type_payment,
                payments.charge_code,
                payments.id AS payment_id,
                plans.id as plans_id,
                plans.name as plans_name',
        ))
            ->leftJoin('subscribers', 'subscribers.id', '=', 'payments.subscriber_id')
            ->leftJoin('payment_plan', 'payment_plan.payment_id', '=', 'payments.id')
            ->leftJoin('plans', 'plans.id', '=', 'payment_plan.plan_id')
            ->where('payments.type', 'P')
            ->where('payments.platform_id', '=', '7658c4c7-92eb-4d59-8001-a4dd638d2e57')
            ->orderBy('payments.payment_date', 'DESC');
    }
}
