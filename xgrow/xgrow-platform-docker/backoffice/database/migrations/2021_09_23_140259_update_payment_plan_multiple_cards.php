<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePaymentPlanMultipleCards extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
$query = <<< EOT
# optimizer_switch changed due to
# "You can't specify target table 'payment_plan' for update in FROM clause"

SET SESSION optimizer_switch = 'derived_merge=off';

BEGIN;

UPDATE payment_plan,
    (
        SELECT payments.subscriber_id,
               payments.order_number,
               payments.order_code,
               payments.price,
               payments.plans_value,
               payments.customer_value,
               payments.installments,
               payment_plan.id,
               payment_plan.plan_id,
               payment_plan.plan_value,
               payment_plan.customer_value as customer_value_plan
        FROM payments
                 LEFT JOIN payment_plan ON payment_plan.payment_id = payments.id
                 INNER JOIN subscribers ON subscribers.id = payments.subscriber_id
        WHERE payments.multiple_means = true
          AND (
            SELECT CASE
                       WHEN count(*) = 1 THEN TRUE
                       ELSE FALSE
                       END
            FROM payment_plan x
            WHERE x.payment_id = payments.id
        )
        ORDER BY payments.order_number
    ) AS consulta
SET payment_plan.plan_value     = consulta.plans_value,
    payment_plan.plan_price     = consulta.price,
    payment_plan.customer_value = consulta.customer_value
WHERE payment_plan.id = consulta.id;

COMMIT;

SET SESSION optimizer_switch = 'derived_merge=default';

EOT;

DB::unprepared($query);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
