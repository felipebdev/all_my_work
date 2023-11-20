<?php

namespace App\Jobs\Reports\Sales\Models;

use App\Plan;
use App\Services\Objects\SaleReportFilter;
use App\Subscription;

class NoLimitSaleReport extends BaseReport
{
    public function name() 
    {
        return 'sem_limite';
    }

    public function query(string $platformId, SaleReportFilter $filters) 
    {
        return $this->paymentRepository->reportNoLimitSale($platformId, $filters);
    }

    public function header() 
    {
        return [
            'ID Produto', 'Produto', 'Plano', 'Aluno', 'E-mail', 'Nº parcelas', 'Parcela',
            'Status do produto', 'Dt. Adesão', 'Dt. Cobrança', 'Dt. Cancelamento',
            'Método pgto.', 'Cupom', 'Valor Cupom', 'Valor Produto',
            'Valor parcela s/j', 'Taxa Xgrow', 'Valor líquido', 'Documento', 
            'Nº do Documento', 'Telefone', 'CEP', 'Rua', 'Número', 'Complemento', 
            'Bairro', 'Cidade', 'Estado', 'País', 'Transação', 'Tipo de Teste', 
            'Duração Período de Teste',
        ];
    }

    public function rows()
    {
        return [
            'product_id' => 'product_id',
            'product_name' => 'product_name',
            'plan_name' => 'plan_name',
            'subscriber_name' => 'subscriber_name',
            'subscriber_email' => 'subscriber_email',
            'payment_installments' => 'payment_installments',
            'payment_installment_number' => 'payment_installment_number',
            'subscription_status' => function ($data) {
                return Subscription::listStatus()[$data] ?? '-';
            },
            'payment_created_at' => function ($data, $row) {
                return date('d/m/Y H:i:s', strtotime($data));
            },
            'payment_date' => function ($data, $row) {
                return date('d/m/Y', strtotime($data));
            },
            'payment_updated_at' => function ($data, $row) {
                return ($row->payment_status === 'Cancelado') ? date('d/m/Y', strtotime($data)) : '';
            },
            'payment_type' => function ($data, $row) {
                return $this->paymentMethod($data);
            },
            'coupon_code' => function ($data, $row) {
                $data = ($row->payment_plan_coupon_code) ? $row->payment_plan_coupon_code : $data;
                return ($this->isOrderBump($row)) ?
                    '' :
                    ($this->isNotFirstInstallment($row) ? '' : $data);
            },
            'coupon_value' => function ($data, $row) {
                $data = ($row->payment_plan_coupon_value) ? 
                    $row->payment_plan_coupon_value * $row->payment_installments : 
                    (($row->coupon_type === 'P') ? $row->plan_price * ($data/100) : $data //percent : coin
                ); 

                $value = ($this->isOrderBump($row)) ?
                    0 :
                    ($this->isNotFirstInstallment($row) ? 0 : $data);

                return (!empty($value)) ? formatCoin($value, 'BRL', false) : '';
            },
            'plan_price' => function ($data, $row) { //valor produto
                $value = 0;
                if (!$row->payment_plan_plan_value) {
                    $coupon = ($row->coupon_value !== '') ? parseFloat($row->coupon_value) : 0;
                    $value = ($this->isNotFirstInstallment($row)) ? 
                        0 : 
                        ($this->isOrderBump($row) ? $data : $data - $coupon);
                }
                else {
                    $value = ($this->isNotFirstInstallment($row)) ? 
                        0 : 
                        ($this->isOrderBump($row) ? 
                            $row->payment_plan_plan_value : 
                            $row->payment_plan_plan_value * $row->payment_installments
                        );
                }

                return (!empty($value)) ? formatCoin($value, 'BRL', false) : '';
            },
            // 'payment_price' => function ($data, $row) { //parcela c/juros
            //     $value = ($this->isOrderBump($row)) ? 0 : $data;
            //     return (!empty($value)) ? formatCoin($value, 'BRL', false) : '';
            // },
            'payment_plan_value' => function ($data, $row) { //parcela s/juros
                $value = 0;
                if (!$row->payment_plan_plan_value) {
                    $coupon = (empty($row->coupon_original_value)) ?
                    0 :
                    (($row->coupon_type === 'P') ? 
                        $row->plan_original_price * ($row->coupon_original_value/100) : 
                        $row->coupon_original_value
                    );

                    $price = ($this->isOrderBump($row)) ? 
                        parseFloat($row->plan_original_price) : 
                        parseFloat($row->plan_original_price - $coupon);

                    $value = $price / $row->payment_installments;
                }
                else {
                    $value = ($this->isOrderBump($row) ? 
                        $row->payment_plan_plan_value / $row->payment_installments : 
                        $row->payment_plan_plan_value
                    );
                }
                
                return formatCoin($value, 'BRL', false);
            },
            'payment_tax_value' => function ($data, $row) { //valor taxa
                $value = ($row->payment_plan_tax_value) ? $row->payment_plan_tax_value :
                    (parseFloat($row->payment_plan_value) * parseFloat($row->client_tax));

                return formatCoin($value, 'BRL', false);
            },
            'payment_customer_value' => function ($data, $row) { //valor liquido
                $value = ($row->payment_plan_customer_value) ? $row->payment_plan_customer_value : 
                    (parseFloat($row->payment_plan_value) - parseFloat($row->payment_tax_value));

                return formatCoin($value, 'BRL', false);
            },
            'subscriber_document_type' => 'subscriber_document_type',
            'subscriber_document_number' => 'subscriber_document_number',
            'subscriber_cellphone' => 'subscriber_cellphone',
            'subscriber_zipcode' => 'subscriber_zipcode',
            'subscriber_street' => 'subscriber_street',
            'subscriber_number' => 'subscriber_number',
            'subscriber_comp' => 'subscriber_comp',
            'subscriber_district' => 'subscriber_district',
            'subscriber_city' => 'subscriber_city',
            'subscriber_state' => 'subscriber_state',
            'subscriber_country' => 'subscriber_country',
            'payment_charge_code' => 'payment_charge_code',
            'plan_freedays_type' => 'plan_freedays_type',
            'plan_freedays' => 'plan_freedays',
        ];
    }

    private function isOrderBump($row) {
        return (($row->payment_plan_type === 'order_bump') || 
            (!empty($row->payment_order_bump) && str_contains($row->payment_order_bump, $row->plan_id))
        ); 
    }

    private function isNotFirstInstallment($row) {
        return $row->payment_installment_number != 1;
    }
}
