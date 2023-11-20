<?php

namespace Tests\Regression;

use Illuminate\Support\Facades\DB;
use Tests\Feature\Helper\MundipaggToken;
use Tests\Feature\Traits\CreateSubscriberTrait;
use Tests\Feature\Traits\Json\ValidatesCreditCardJsonPayload;
use Tests\Feature\Traits\LocalDatabaseIds;
use Tests\TestCase;

use function env;

/**
 * https://xgrow.atlassian.net/browse/XP-2095
 *
 * Arredondamento em vendas sem limite
 *
 * https://xgrow.atlassian.net/wiki/spaces/XGROW/pages/12681229/Algoritmo+de+c+lculo+de+parcelas+do+Sem-Limite
 *
 */
class Xp2095NoLimitRoundingTest extends TestCase
{

    use CreateSubscriberTrait;
    use LocalDatabaseIds;
    use ValidatesCreditCardJsonPayload;

    //use RefreshDatabase;

    public const DELTA = 0.0000000000001;

    protected function setUp(): void
    {
        parent::setUp();

        if (!env('MUNDIPAGG_SECRET_KEY')) {
            $this->markTestSkipped('Mundipagg secret key is not set');
        }
    }

    public function test_simple_no_limit_sale()
    {
        // WARNING: this test requires a fresh database
        $this->artisan('migrate:fresh --seed');

        $this->withoutMiddleware();

        $subscriberId = $this->createSubscriber($this->platformId, $this->salePlanId);

        $token = MundipaggToken::insufficientBalance($this->faker->creditCardNumber('MasterCard'));

        $response = $this->postJson("/api/checkout/{$this->platformId}/{$this->salePlanId}", [
            "payment_method" => "credit_card",
            //"cupom" => "10REAIS",
            "cc_info" => [
                [
                    "token" => "$token",
                    "installment" => 12,
                    "value" => "100"
                ]
            ],
            "subscriber_id" => $subscriberId,
            "platform_id" => $this->platformId,
            "plan_id" => $this->salePlanId,
            //"order_bump" => $this->orderBumps,
        ]);

        if ($response->getStatusCode() == 409) {
            $this->markTestSkipped('Gateway problem');
        }

        $response->assertStatus(200);

        $this->assertJsonPayload($response);

        $this->assertDatabaseCount('payments', 12); // 12 installments

        // O operador Σ representa a soma de todas as linhas referentes a determinada venda
        //payments table (p.)
        //Σ p.plans_value : a soma dos valores é o valor original da compra
        $data = DB::selectOne("select sum(plans_value) as total from payments");
        $this->assertEqualsWithDelta(100, $data->total, self::DELTA);

        //Σ p.price : a soma dos “prices” é o valor total com juros
        $data = DB::selectOne("select sum(price) as total from payments");
        $this->assertEquals(120, $data->total);

        //p.customer_value + p.tax_value = p.plan_value : a parcela sem juros é composta por valor líquido mais taxas
        $data = DB::selectOne("select count(*) as count from payments p where p.customer_value + p.tax_value = p.plans_value");
        $this->assertEquals(12, $data->count);

        //Σ p.customer_value + Σ p.tax_value = Σ p.plan_value : o total sem juros é a soma

        //p.customer_value + p.service_value = p.price : a parcela com juros é composta pelo valor líquido mais os valor de serviço
        $data = DB::selectOne("select count(*) as count from payments p where p.customer_value + p.service_value = p.price");
        $this->assertEquals(12, $data->count);

        //Σ p.customer_value + Σ p.service_value = Σ p.price : o total com juros é a soma
        $data = DB::select("select
sum(p.customer_value) as total_customer,
sum(p.service_value) as total_service,
sum(p.price) as total_price
from payments p
having total_customer + total_service = total_price;");
        $this->assertEquals(1, count($data));

        //payment_plan table (pp.)
        //As somas entre payment_plan e payments devem bater

        //Σ pp.tax_value = Σ p.tax_value
        $data = DB::select("select
sum(p.tax_value) as p_total,
sum(pp.tax_value) as pp_total
from payments p
left join payment_plan pp on pp.payment_id = p.id
having p_total = pp_total;");
        $this->assertEquals(1, count($data));

        //Σ pp.plan_value = Σ p.plans_value
        $data = DB::selectOne("select sum(plan_value) as total from payment_plan");
        $this->assertEqualsWithDelta(100, $data->total, self::DELTA);

        //Σ pp.plan_price = Σ p.price
        $data = DB::selectOne("select sum(plan_price) as total from payment_plan");
        $this->assertEquals(120, $data->total);

        //Σ pp.customer_value = Σ p.customer_value
        $data = DB::select("select
sum(p.customer_value) as p_total,
sum(pp.customer_value) as pp_total
from payments p
left join payment_plan pp on pp.payment_id = p.id
having p_total = pp_total;");
        $this->assertEquals(1, count($data));

        //pp.customer_value + pp.tax_value = pp.plan_value
        $data = DB::selectOne("select count(*) as count from payment_plan where customer_value + tax_value = plan_value");
        $this->assertEquals(12, $data->count);
    }
}
