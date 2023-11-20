<?php

namespace App\Jobs\Reports\Sales\Models;

use App\Services\Objects\SaleReportFilter;

class SubscriptionSaleReport extends BaseReport
{
    public function name() 
    {
        return 'assinatura';
    }

    public function query(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportSubscriberSale($platformId, $filters);
    }

    public function header()
    {
        return [
            "ID Produto", "Produto", "Plano", "Aluno", "E-mail", "Periodicidade",
            "Nº parcelas", "Status", "Dt. Adesão", "Dt. de Cancelamento", "Dt. Cobrança",
            "Método pgto.", "Cupom", "Valor do Cupom", "Valor da Assinatura",
            "Taxa Xgrow", "Valor líquido", "Documento", "Nº do Documento",
            "Telefone", "CEP", "Rua", "Número", "Complemento", "Bairro",
            "Cidade", "Estado", "Pais", "Transação", 'Tipo de Teste', 'Duração Período de Teste'
        ];
    }

    public function rows()
    {
        return [
            'product_id' => 'product_id',
            'product_name' => 'product_name',
            'plans_name' => 'plans_name',
            'subscribers_name' => 'subscribers_name',
            'subscribers_email' => 'subscribers_email',
            'recurrence' => function ($data, $row) {
                return $this->recurrenceLabel($data);
            },
            'totalInstallments' => 'totalInstallments',
            'subscription_status' => function ($data, $row) {
                return $this->subscriptionStatus($data);
            },
            'subscription_date' => function ($data, $row) {
                return $data ? date('d/m/Y H:i:s', strtotime($data)) : ' - ';
            },
            'cancellation_date' => function ($data, $row) {
                return $data ? date('d/m/Y H:i:s', strtotime($data)) : ' - ';
            },
            'payment_date' => function ($data, $row) {
                return $data ? date('d/m/Y', strtotime($data)) : ' - ';
            },
            'type_payment' => function ($data, $row) {
                return $this->paymentMethod($data);
            },
            'coupon_code' => 'coupon_code',
            'coupon_value' => 'coupon_value',
            'price' => function ($data, $row) {
                return floatval($data);
            },
            'tax_value' => 'tax_value',
            'customer_value' => function ($data, $row) {
                return floatval($data);
            },
            'document_type' => 'document_type',
            'document_number' => 'document_number',
            'cel_phone' => 'cel_phone',
            'address_zipcode' => 'address_zipcode',
            'address_street' => 'address_street',
            'address_number' => 'address_number',
            'address_comp' => 'address_comp',
            'address_district' => 'address_district',
            'address_city' => 'address_city',
            'address_state' => 'address_state',
            'address_country' => 'address_country',
            'transactions_id' => 'transactions_id',
            'freedays_type' => 'freedays_type',
            'freedays' => 'freedays',
        ];
    }

}
