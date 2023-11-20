CREATE OR REPLACE
VIEW view_recurrences_with_active_charges AS
SELECT
	subscribers.id AS id_aluno,
	subscribers.name AS aluno,
	subscribers.email AS email_aluno,
	subscribers.last_acess AS ultimo_acesso_aluno,
	platforms.id AS id_plataforma,
	platforms.name AS nome_plataforma,
	clients.first_name AS primeiro_nome_cliente,
	clients.last_name AS ultimo_nome_cliente,
	clients.company_name AS razao_social_cliente,
	clients.cnpj as cnpj_cliente,
	clients.cpf AS cpf_cliente,
	clients.email  AS email_cliente,
	subscriptions.order_number AS codigo_compra,
	recurrences.payment_method AS metodo_pagamento_recorrencia,
	recurrences.recurrence AS intervalo_recorrencia,
	recurrences.current_charge,
	plans.id AS plan_id,
	plans.name AS plan_name,
	plans.price AS plan_price,
	plans.charge_until,
	CASE
		plans.charge_until
	WHEN 0 THEN 99999
		ELSE plans.charge_until - recurrences.current_charge
	END AS cobrancas_restantes,
	recurrences.total_charges AS cobrancas_total,
	recurrences.last_payment AS ultimo_pagamento ,
	recurrences.last_invoice AS ultima_fatura,
	recurrences.last_payment + INTERVAL recurrences.recurrence DAY AS proximo_pagamento
FROM
	recurrences
INNER JOIN subscribers ON
	subscribers.id = recurrences.subscriber_id
INNER JOIN platforms ON
	platforms.id = subscribers.platform_id
INNER JOIN subscriptions ON
	subscriptions.subscriber_id = subscribers.id
	AND recurrences.plan_id = subscriptions.plan_id
	AND subscriptions.status = 'active'
INNER JOIN plans ON
	recurrences.plan_id = plans.id
INNER JOIN clients ON
	clients.id = platforms.customer_id;


CREATE OR REPLACE
VIEW view_recurrences_with_pending_charges AS

SELECT
	*,
	DATEDIFF(CURDATE(), date(ultimo_pagamento)) AS dias_desde_ultimo_pagamento,
	ROUND(DATEDIFF(CURDATE(), date(ultimo_pagamento)) / intervalo_recorrencia) AS mensalidades_atrasadas,
	ROUND(DATEDIFF(CURDATE(), date(ultimo_pagamento)) / intervalo_recorrencia) * plan_price AS missing_values
FROM
	view_recurrences_with_active_charges
WHERE
	proximo_pagamento < CURDATE()
	AND cobrancas_restantes > 0
	AND NOT EXISTS (
		SELECT * FROM payments
		JOIN payment_plan ON payment_plan.payment_id = payments.id
		WHERE payments.payment_date = view_recurrences_with_active_charges.proximo_pagamento
		AND payments.subscriber_id = view_recurrences_with_active_charges.id_aluno
		AND payment_plan.plan_id = view_recurrences_with_active_charges.plan_id
	);
