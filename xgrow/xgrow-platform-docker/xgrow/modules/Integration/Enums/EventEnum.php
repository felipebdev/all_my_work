<?php

namespace Modules\Integration\Enums;

use App\Enums\BasicEnum;

final class EventEnum extends BasicEnum
{
    const LEAD_CREATED = 'onCreateLead'; //lead gerado
    const CART_ABANDONED = 'onAbandonedCart'; // carrinho abandonado
    const BANK_SLIP_CREATED = 'onCreateBankSlip'; //boleto gerado
    const PIX_CREATED = 'onCreatePix'; // Pix gerado
    const PAYMENT_APPROVED = 'onApprovePayment'; //compra aprovada
    const PAYMENT_REFUSED = 'onRefusePayment'; //compra recusada
    const PAYMENT_REFUND = 'onRefundPayment'; //compra estornada
    const PAYMENT_CHARGEBACK = 'onChargebackPayment'; //compra com chargeback
    const SUBSCRIPTION_CANCELED = 'onCancelSubscription'; //compra cancelada
    const NEVER_ACCESSED = 'onNeverAccessed'; // nunca acessou
    // const PAYMENT_EXPIRED = 'onExpirePayment'; //TODO: compra expirada
    // const MEMBER_AREA_ACCESSED = 'onAccessMemberArea'; //TODO: area de membros acessada

    /**
     * Return correct event by category
     * @param string $category
     * @return array|string[][]
     */
    public static function returnEventsByCategory(string $category): array
    {
        if ($category === 'notes') {
            return [
                ['event' => self::PAYMENT_APPROVED, 'name' => 'Compra Aprovada'],
                ['event' => self::PAYMENT_REFUND, 'name' => 'Compra Estornada'],
            ];
        }

        return [];
    }
}
