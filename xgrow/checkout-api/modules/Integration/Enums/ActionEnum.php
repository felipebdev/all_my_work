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
    const INSERT_CONTACT_TAG= 'bindInsertContactTag';
    const REMOVE_CONTACT_TAG = 'bindRemoveContactTag';
    const TRIGGER_WEBHOOK = 'bindTriggerWebhook';
    const TRIGGER_EXPO = 'bindPushNotification';
}
