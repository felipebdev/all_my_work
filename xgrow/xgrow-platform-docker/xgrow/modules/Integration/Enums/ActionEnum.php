<?php

namespace Modules\Integration\Enums;

use App\Enums\BasicEnum;

final class ActionEnum extends BasicEnum
{
    const INSERT_CONTACT = 'bindInsertContact';
    const REMOVE_CONTACT = 'bindRemoveContact';
    const GRANT_ACCESS = 'bindGrantAccess';
    const REVOKE_ACCESS = 'bindRevokeAccess';
    const GENERATE_INVOICE = 'bindGenerateInvoice';
    const CANCEL_INVOICE = 'bindCancelInvoice';
    const INSERT_CONTACT_TAG = 'bindInsertContactTag';
    const REMOVE_CONTACT_TAG = 'bindRemoveContactTag';
    const TRIGGER_WEBHOOK = 'bindTriggerWebhook';

    /**
     * Return correct actions by category
     * @param string $action
     * @return array|string[][]
     */
    public static function returnActionsByCategory(string $action): array
    {
        if ($action === 'notes') {
            return [
                ['action' => self::GENERATE_INVOICE, 'name' => 'Gerar Nota'],
                ['action' => self::CANCEL_INVOICE, 'name' => 'Cancelar Nota'],
            ];
        }
        if ($action === 'member-area') {
            return [
                ['action' => self::INSERT_CONTACT, 'name' => 'Inserir Contatos'],
                ['action' => self::REMOVE_CONTACT, 'name' => 'Remover Contatos'],
            ];
        }

        return [];
    }
}
