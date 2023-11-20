<?php

namespace Tests\Feature\Api\Checkout\Endpoints;

use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

class GetPlanTestCase extends TestCase
{

    use LocalDatabaseIds;

    public function test_getplan()
    {
        $this->withoutMiddleware();

        $response = $this->get("/api/checkout/platforms/{$this->platformId}/plans/{$this->salePlanId}");

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'name',
            'status',
            'currency',
            'price',
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
            'original_price',
            'hasCoupons',
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
                'checkout_google_tag',
                'checkout_google_tag_conversion_label',
                'image_id',
                'image_url',
            ],
            'client' => [
                'name',
                'email',
            ],
            'order_bump' => [
                '*' => [
                    'id',
                    'name',
                    'message',
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
                '*' => [
                    'id',
                    'name',
                    'message',
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
            'recipients' => [
                'client',
                'client_errors',
                'producers',
                'producers_errors',
                //'affiliates',
                //'affiliates_errors',
            ],
        ]);
    }

}
