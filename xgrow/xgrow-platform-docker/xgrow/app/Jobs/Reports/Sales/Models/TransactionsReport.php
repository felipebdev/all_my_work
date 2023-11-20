<?php

namespace App\Jobs\Reports\Sales\Models;

use App\Coupon;
use App\Payment;
use App\Plan;
use App\Services\Objects\SaleReportFilter;

class TransactionsReport extends BaseReport
{
    public function name()
    {
        return 'transactions';
    }

    public function query(string $platformId, SaleReportFilter $filters)
    {
        return $this->paymentRepository->reportTransactionSale($platformId, $filters);
    }

    public function header()
    {
        return [
            'Pedido', 'ID Produto', 'Produto', 'Plano', 'Tipo de pagamento', 'Aluno', 'E-mail',
            'Nº parcelas', 'Status', 'Dt. Adesão', 'Dt. Pagamento', 'Dt. Cancelamento',
            'Método pgto.', 'Cupom', 'Valor Cupom', 'Valor Produto',
            'Taxa Xgrow', 'Valor líquido', 'Minha Comissão', 'Documento', 'Nº do Documento', 'Telefone',
            'CEP', 'Rua', 'Número', 'Complemento', 'Bairro', 'Cidade', 'Estado', 'País',
            'Transação', 'Tipo de Teste', 'Duração Período de Teste',
        ];
    }

    public function rows()
    {
        return [
            'payment_order_number' => 'payment_order_number',
            'product_id' => 'product_id',
            'product_name' => 'product_name',
            'plans_name' => 'plans_name',
            'payment_type' => function ($data, $row) {
                return Payment::listTypes()[$data] ?? '-';
            },
            'subscribers_name' => 'subscribers_name',
            'subscribers_email' => 'subscribers_email',
            'installments' => 'installments',
            'payments_status' => function ($data) {
                return $this->changeStatus($data);
            },
            'subscription_created_at' => function ($data, $row) {
                $date = $row->subscription_created_at ?? $row->payment_payment_date;
                return date('d/m/Y H:i:s', strtotime($date));
            },
            'payment_payment_date' => function ($data, $row) {
                return date('d/m/Y', strtotime($data));
            },
            'payment_updated_at' => function ($data, $row) {
                return ($row->payments_status === 'Cancelado') ? date('d/m/Y', strtotime($data)) : '-';
            },
            'type_payment' => function ($data, $row) {
                $ids = ($row->payment_multiple_cards_id) ? explode(',', $row->payment_multiple_cards_id) : [$row->payment_id];
                $info = (count($ids) > 1) ? '(Múltiplos cartões)' : '';
                return $this->paymentMethod($data) . ' ' . $info;
            },
            'coupon_code' => function ($data, $row) {
                $data = ($row->payment_plan_coupon_code) ? $row->payment_plan_coupon_code : $data;
                return $this->isOrderBump($row) ?
                    '' : ($this->isMultipleCards($row) ? '' : $data);
            },
            'coupon_value' => function ($data, $row) {
                $data = ($row->payment_plan_coupon_value) ? $row->payment_plan_coupon_value : (($row->coupon_type === 'P') ? $row->plans_price * ($data / 100) : $data); //percent : coin

                $value = $this->isOrderBump($row) ? 0 : $data;
                return ($this->isMultipleCards($row) ?
                    '' : (!empty($value) ? formatCoin($value, 'BRL', false) : ''));
            },
            'plans_price' => function ($data, $row) {
                if ($row->payment_plan_plan_value) {
                    $data = $row->payment_plan_plan_value;
                    return formatCoin($data, 'BRL', false);
                }

                $couponValue = ($row->payment_plan_plan_value) ? 0 : $row->coupon_value;
                $value = $this->isOrderBump($row) ? $data : ($data - parseFloat($couponValue));
                return (!empty($value) ? formatCoin($value, 'BRL', false) : '');
            },
            'tax_value' => function ($data, $row) {
                if ($row->payment_plan_tax_value) {
                    $value = $row->payment_plan_tax_value;
                    return (formatCoin($value, 'BRL', false));
                }

                $value = (parseFloat($row->plans_price) * $row->client_tax);

                if ($this->isMultipleCards($row)) {
                    $coupon = 0;
                    if ($row->payment_plan_coupon_value) {
                        $coupon = parseFloat($row->payment_plan_coupon_value);
                    } elseif ($row->coupon_value != '') {
                        $couponValue = parseFloat($row->coupon_value);

                        if ($row->coupon_type == Coupon::TYPE_PERCENT) {
                            $coupon = $row->plans_price * ($couponValue / 100);
                        } else {
                            $coupon = $couponValue;
                        }
                    }

                    $tax = parseFloat($row->tax_value)
                        - parseFloat($row->tax_value) * ($coupon / parseFloat($row->plans_price));

                    $value = $tax;
                }

                return (!empty($value) ? formatCoin($value, 'BRL', false) : '');
            },
            'customer_value' => function ($data, $row) {
                if ($row->payment_plan_customer_value) {
                    $value = $row->payment_plan_customer_value;
                    return (formatCoin($value, 'BRL', false));
                }

                $value = (parseFloat($row->plans_price) - parseFloat($row->tax_value));

                if ($this->isMultipleCards($row)) {
                    $coupon = 0;
                    if ($row->payment_plan_coupon_value) {
                        $coupon = parseFloat($row->payment_plan_coupon_value);
                    } elseif ($row->coupon_value != '') {
                        $couponValue = parseFloat($row->coupon_value);

                        if ($row->coupon_type == Coupon::TYPE_PERCENT) {
                            $coupon = $row->plans_price * ($couponValue / 100);
                        } else {
                            $coupon = $couponValue;
                        }
                    }

                    $tax = parseFloat($row->tax_value)
                        - parseFloat($row->tax_value) * ($coupon / parseFloat($row->plans_price));

                    $netValue = $row->price - parseFloat($coupon) - $tax;

                    $value = $netValue;
                }

                return (!empty($value) ? formatCoin($value, 'BRL', false) : '');
            },
            'commission' => function ($data, $row) {
                if ($row->commission) {
                    $value = $row->commission;
                    return (formatCoin($value, 'BRL', false));
                }
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
            'charge_code' => 'charge_code',
            'freedays_type' => 'freedays_type',
            'freedays' => 'freedays',
        ];
    }

    private function isOrderBump($row)
    {
        return (($row->payment_plan_type === 'order_bump') ||
            (!empty($row->order_bump) && str_contains($row->order_bump, $row->plans_id))
        );
    }

    private function isMultipleCards($row)
    {
        $ids = ($row->payment_multiple_cards_id) ? explode(',', $row->payment_multiple_cards_id) : [$row->payment_id];
        return (intval($ids[0]) !== intval($row->payment_id));
    }
}
