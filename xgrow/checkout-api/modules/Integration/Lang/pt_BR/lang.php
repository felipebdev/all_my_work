<?php

return [
    'integrations' => [
        'events' => [
            'onCreateLead' => 'Lead gerado',
            'onCreateBankSlip' => 'Boleto gerado',
            'onApprovePayment' => 'Compra aprovada',
            'onRefusePayment' => 'Compra recusada',
            'onRefundPayment' => 'Compra estornada',
            'onChargebackPayment' => 'Compra com chargeback',
            'onCancelSubscription' => 'Compra cancelada',
            'onExpirePayment' => 'Compra expirada',
        ],
        'actions' => [
            'bindInsertContact' => 'Inserir',
            'bindRemoveContact' => 'Remover',
            'bindGrantAccess' => 'Conceder acesso',
            'bindRevokeAccess' => 'Revogar acesso',
            'bindGenerateInvoice' => 'Gerar nota fiscal',
            'bindInsertContactTag' => 'Inserir contato com a tag',
            'bindRemoveContactTag' => 'Remover tag do contato',
            'bindTriggerWebhook' => 'Disparar webhook'
        ]
    ]
];
