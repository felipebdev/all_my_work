<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CorrelationIdHeaderMiddleware;
use App\Lead;
use App\Payment;
use App\Services\Finances\Objects\OrderInfo;
use App\Subscriber;
use Carbon\Carbon;

class LeadService
{

    /**
     * Create and save a new Lead based on a Subscriber
     *
     * @param  \App\Subscriber  $subscriber
     * @param  string  $type  Type of order: product, order_bump, upsell
     * @return \App\Lead
     */
    public function createLeadFromSubscriber(Subscriber $subscriber, string $type = Lead::TYPE_PRODUCT): Lead
    {
        $lead = new Lead();
        $lead->name = $subscriber->name;
        $lead->email = $subscriber->email;
        $lead->cel_phone = $subscriber->cel_phone;
        $lead->document_type = $subscriber->document_type;
        $lead->document_number = $subscriber->document_number;
        $lead->address_zipcode = $subscriber->address_zipcode;
        $lead->address_street = $subscriber->address_street;
        $lead->address_number = $subscriber->address_number;
        $lead->address_comp = $subscriber->address_comp;
        $lead->address_district = $subscriber->address_district;
        $lead->address_city = $subscriber->address_city;
        $lead->address_state = $subscriber->address_state;
        $lead->address_country = $subscriber->address_country;
        $lead->platform_id = $subscriber->platform_id;
        $lead->subscriber_id = $subscriber->id;
        $lead->plan_id = $subscriber->plan_id;
        $lead->type = $type;
        $lead->payment_method = null;
        $lead->save();

        return $lead;
    }

    /**
     * Change cart status to "ordered".
     *
     * An "ordered" cart is a cart where the user has clicked in "Buy" button.
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     */
    public function leadOrdered(OrderInfo $orderInfo): void
    {
        Lead::where('subscriber_id', $orderInfo->getSubscriberId())
            ->whereIn('plan_id', $orderInfo->getAllPlanIds())
            ->update([
                'cart_status' => Lead::CART_STATUS_ORDERED,
                'cart_status_updated_at' => Carbon::now(),
                'payment_method' => $orderInfo->getPaymentMethod(),
                'order_correlation_id' => CorrelationIdHeaderMiddleware::get(),
            ]);
    }

    public function getPendindOrderedCreditCard(OrderInfo $orderInfo): ?Lead
    {
        $ordered = Lead::where('subscriber_id', $orderInfo->getSubscriberId())
            ->whereIn('plan_id', $orderInfo->getAllPlanIds())
            ->where('cart_status', Lead::CART_STATUS_ORDERED)
            ->where('payment_method', Payment::TYPE_PAYMENT_CREDIT_CARD)
            ->latest('created_at')
            ->first();

        if ($ordered) {
            $minutesLimit = Carbon::now()->subHours(5);

            if ($ordered->cart_status_updated_at < $minutesLimit) {
                return null;
            }

            return $ordered;
        }

        return null;
    }

    public function getLeadHistory(?OrderInfo $orderInfo)
    {
        $history = Lead::query()
            ->where('subscriber_id', $orderInfo->getSubscriberId())
            ->whereIn('plan_id', $orderInfo->getAllPlanIds())
            ->get();

        return $history;
    }

    /**
     * Change cart status to "denied"
     *
     * A "denied" cart is a cart where the payment was executed on Payment Service but not approved
     * (eg: insufficient funds).
     *
     * @param  \App\Services\Finances\Objects\OrderInfo  $orderInfo
     */
    public function leadDenied(OrderInfo $orderInfo): void
    {
        Lead::where('subscriber_id', $orderInfo->getSubscriberId())
            ->whereIn('plan_id', $orderInfo->getAllPlanIds())
            ->update([
                'cart_status' => Lead::CART_STATUS_DENIED,
                'cart_status_updated_at' => Carbon::now(),
                'payment_method' => $orderInfo->getPaymentMethod(),
            ]);
    }

    /**
     * Change cart status to "confirmed"
     *
     * A "confirmed" cart is a cart where the payment was received by Payment Service.
     *
     * @param  string  $subscriberId
     * @param  array  $planIds
     */
    public function leadConfirmed(string $subscriberId, array $planIds): void
    {
        Lead::where('subscriber_id', $subscriberId)
            ->whereIn('plan_id', $planIds)
            ->update([
                'cart_status' => Lead::CART_STATUS_CONFIRMED,
                'cart_status_updated_at' => Carbon::now(),
            ]);
    }
}
