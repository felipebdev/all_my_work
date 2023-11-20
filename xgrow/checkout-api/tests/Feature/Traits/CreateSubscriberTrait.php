<?php

namespace Tests\Feature\Traits;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Crypt;

use function dump;

trait CreateSubscriberTrait
{

    use WithFaker;

    /**
     * Faker locale when creating subscriber, override it for custom locale
     *
     * @var string Faker locale, pt_BR as default
     */
    protected string $fakerLocale = 'pt_BR';

    /**
     * Stores the last subscriber request
     *
     * @var array|null
     */
    public static ?array $lastSubscriberRequest = null;

    protected function createSubscriber(string $platformId, int $planId): int
    {
        $faker = $this->makeFaker($this->fakerLocale);

        $cpf = $faker->cpf($formatted = false);

        // dump("# Creating subscriber with CPF {$cpf}");

        self::$lastSubscriberRequest = [
            'platform_id' => $platformId,
            'plan_id' => $planId,
            'email' => "checkout-api-test-{$cpf}@xgrow.com",
            'name' => "{$this->faker->firstName()} {$faker->lastName()} ({$cpf})",
            'phone_country_code' => '55',
            'phone_area_code' => '10',
            'phone_number' => $faker->cellphone($formatted = false, $includes9 = true),
            'user_ip' => $faker->ipv4(), // deprecated
            'document_number' => $cpf,
            'document_type' => 'cpf',
            'country' => 'br',
            'client_ip_address' => $this->faker->ipv4,
            'client_user_agent' => $this->faker->userAgent,
        ];

        $response = $this->postJson('/api/checkout/subscriber', self::$lastSubscriberRequest);

        if (!is_string($response->json())) {
            dump($response);
            throw new \Exception("Can't create subscriber (maybe a Middleware problem)");
        }

        $decrypted = Crypt::decrypt($response->json());

        $subscriberId = $decrypted['subscriber_id'];

        // dump("# Subscriber created: {$subscriberId} ");

        return $subscriberId;
    }

    protected static function lastSubscriberRequest(): ?array
    {
        return self::$lastSubscriberRequest;
    }
}
