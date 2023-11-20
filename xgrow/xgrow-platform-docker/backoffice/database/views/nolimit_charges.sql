CREATE OR REPLACE
VIEW view_nolimit_with_active_charges AS
SELECT
	subscribers.id AS id_aluno,
	subscribers.name AS aluno,
	subscribers.status AS status_aluno,
	subscribers.updated_at AS updated_at_aluno,
	subscribers.email AS email_aluno,
	COALESCE(subscribers.login, subscribers.last_acess) AS ultimo_acesso_aluno,
	platforms.id AS id_plataforma,
	platforms.name AS nome_plataforma,
	clients.first_name AS primeiro_nome_cliente,
	clients.last_name AS ultimo_nome_cliente,
	clients.company_name AS razao_social_cliente,
	clients.cnpj AS cnpj_cliente,
	clients.cpf AS cpf_cliente,
	clients.email AS email_cliente,
	subscriptions.order_number AS codigo_compra,
	plans.id AS plan_id,
	plans.name AS plan_name,
	plans.price AS plan_price,
	payments.payment_date,
	CONCAT(payments.installment_number, '/', payments.installments) AS parcela,
	subscriptions.status AS status_subscription,
	subscriptions.canceled_at AS subscription_canceled_at,
	payments.status AS payment_status
FROM
	subscribers
INNER JOIN subscriptions ON
	subscriptions.subscriber_id = subscribers.id
INNER JOIN payment_plan ON
	payment_plan.plan_id = subscriptions.plan_id
INNER JOIN plans ON
	plans.id = subscriptions.plan_id
INNER JOIN payments ON
	payments.id = payment_plan.payment_id
	AND payments.order_number = subscriptions.order_number
INNER JOIN platforms ON
	platforms.id = subscribers.platform_id
INNER JOIN clients ON
	clients.id = platforms.customer_id
WHERE
	payments.`type` = 'U'
	AND subscriptions.status = 'active'
ORDER BY
	payments.payment_date DESC;

CREATE OR REPLACE
VIEW view_nolimit_with_pending_charges AS
SELECT
	*
FROM
	view_nolimit_with_active_charges
WHERE
	payment_status = 'pending'
	AND payment_date <= CURDATE();
