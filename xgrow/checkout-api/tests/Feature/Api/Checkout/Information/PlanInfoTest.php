<?php

namespace Tests\Feature\Api\Checkout\Information;

use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

/**
 * Test /api/checkout/platforms/{platform_id}/plans/{plan_id}
 *
 * route('checkout.plan.get')
 */
class PlanInfoTest extends TestCase
{

    use LocalDatabaseIds;

    public function test_getPlan_info_payload()
    {
        $this->withoutMiddleware();

        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}");

        //dump($response->json());

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'status',
            'payment_method_free',
            'currency',
            'price',
            'original_price',
            'freedays',
            'freedays_type',
            'charge_until',
            'installment',
            'description',
            'message_success_checkout',
            'url_checkout_confirm',
            'type_plan',
            'payment_method_credit_card',
            'payment_method_boleto',
            'payment_method_pix',
            'payment_method_multiple_cards',
            'use_promotional_price',
            'recurrence_description',
            'promotional_periods',
            'promotional_price',
            'hasCoupons',
            //'client' => [
            //    'name',
            //    'email',
            //],
            'product' => [
                'name',
                'description',
                'type',
                'support_email',
                'keywords',
                'analysis_status',
                'checkout_whatsapp',
                'checkout_email',
                'checkout_support',
                'checkout_url_terms',
                'checkout_support_platform',
                'checkout_layout',
                'checkout_address',
                'double_email',
                'learning_area_type',
                'checkout_facebook_pixel',
                'checkout_facebook_pixel_test_event_code',
                'checkout_facebook_pixel_options',
                //'checkout_facebook_pixel_options' => [
                //    'fb_checkout_visit',
                //    'fb_sales_conversion',
                //    'fb_all_payment_methods',
                //],
                'checkout_google_tag',
                'checkout_google_tag_conversion_label',
                'image_id',
                'image_url',
            ],
            'order_bump' => [
                [
                    'id',
                    'name',
                    'message',
                    'payment_method_free',
                    'recurrence',
                    'currency',
                    'price',
                    'original_price',
                    'discount',
                    'freedays',
                    'freedays_type',
                    'charge_until',
                    'type_plan',
                    'installment',
                    'description',
                    'image_id',
                    'image_url',
                    'message_success_checkout',
                    'learning_area_type',
                    'use_promotional_price',
                    'recurrence_description',
                    'promotional_periods',
                    'promotional_price',
                ],
            ],
            'upsell' => [
                [
                    'id',
                    'name',
                    'message',
                    'payment_method_free',
                    'recurrence',
                    'currency',
                    'price',
                    'original_price',
                    'discount',
                    'freedays',
                    'freedays_type',
                    'charge_until',
                    'type_plan',
                    'installment',
                    'description',
                    'video_url',
                    'accept_event',
                    'accept_url',
                    'decline_event',
                    'decline_url',
                    'image_id',
                    'image_url',
                    'payment_method_credit_card',
                    'payment_method_boleto',
                    'payment_method_pix',
                    'payment_method_multiple_cards',
                    'message_success_checkout',
                    'learning_area_type',
                    'use_promotional_price',
                    'recurrence_description',
                    'promotional_periods',
                    'promotional_price',
                ],
            ],
        ]);
    }
}
