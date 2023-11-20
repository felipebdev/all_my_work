<?php

return [
    'integrations' => [
        'events' => [
            'onCreateLead' => 'Lead gerado',
            'onAbandonedCart' => 'Carrinho abandonado',
            'onCreateBankSlip' => 'Boleto gerado',
            'onCreatePix' => 'Pix gerado',
            'onApprovePayment' => 'Compra aprovada',
            'onRefusePayment' => 'Compra recusada',
            'onRefundPayment' => 'Compra estornada',
            'onChargebackPayment' => 'Compra com chargeback',
            'onCancelSubscription' => 'Inscrição cancelada',
            'onExpirePayment' => 'Compra expirada',
            'onNeverAccessed' => 'Nunca Acessou',
        ],
        'actions' => [
            'bindInsertContact' => 'Inserir',
            'bindRemoveContact' => 'Remover',
            'bindGrantAccess' => 'Conceder acesso',
            'bindRevokeAccess' => 'Revogar acesso',
            'bindGenerateInvoice' => 'Gerar nota fiscal',
            'bindCancelInvoice' => 'Cancelar nota fiscal',
            'bindInsertContactTag' => 'Inserir contato com a tag',
            'bindRemoveContactTag' => 'Remover tag do contato',
            'bindTriggerWebhook' => 'Disparar webhook'
        ]
    ]
];
