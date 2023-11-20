<script>
    const integrations = {
        'activecampaign': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {  list = '', tags = [] } = metadata;
            $('#activecampaign-is_active').prop('checked', Boolean(is_active));
            $('#activecampaign-description').val(description).addClass('mui--is-not-empty');
            $('#activecampaign-event').val(event).change();
            $('#activecampaign-action').val(action).change();
            $('#activecampaign-list').val(list).change();

            tags.forEach(tag => {
                $('#activecampaign-tags').val(tag).change();
            });

            $('#modal-action-activecampaign form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-activecampaign form');
        },
        'cademi': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            $('#cademi-is_active').prop('checked', Boolean(is_active));
            $('#cademi-description').val(description).addClass('mui--is-not-empty');
            $('#cademi-event').val(event).change();
            $('#cademi-action').val(action).change();
            $('#modal-action-cademi form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-cademi form');
        },
        'infusion': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {  tags = [] } = metadata;
            $('#infusion-is_active').prop('checked', Boolean(is_active));
            $('#infusion-description').val(description).addClass('mui--is-not-empty');
            $('#infusion-event').val(event).change();
            $('#infusion-action').val(action).change();

            tags.forEach(tag => {
                $('#infusion-tags').val(tag).change();
            });

            $('#modal-action-infusion form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-infusion form');
        },
        'kajabi': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const { product_webhook = '' } = metadata;
            $('#kajabi-is_active').prop('checked', Boolean(is_active));
            $('#kajabi-description').val(description).addClass('mui--is-not-empty');
            $('#kajabi-product_webhook').val(product_webhook).addClass('mui--is-not-empty');
            $('#kajabi-event').val(event).change();
            $('#kajabi-action').val(action).change();
            $('#modal-action-kajabi form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-kajabi form');
        },
        'mailchimp': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {  list = '', tags = [] } = metadata;
            $('#mailchimp-is_active').prop('checked', Boolean(is_active));
            $('#mailchimp-description').val(description).addClass('mui--is-not-empty');
            $('#mailchimp-event').val(event).change();
            $('#mailchimp-action').val(action).change();
            $('#mailchimp-list').val(list).change();

            tags.forEach(tag => {
                $('#mailchimp-tags').val(tag).change();
            });

            $('#modal-action-mailchimp form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-mailchimp form');
        },
        'octadesk': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            $('#octadesk-is_active').prop('checked', Boolean(is_active));
            $('#octadesk-description').val(description).addClass('mui--is-not-empty');
            $('#octadesk-event').val(event).change();
            $('#octadesk-action').val(action).change();
            $('#modal-action-octadesk form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-octadesk form');
        },
        'smartnotas': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            $('#smartnotas-is_active').prop('checked', Boolean(is_active));
            $('#smartnotas-description').val(description).addClass('mui--is-not-empty');
            $('#smartnotas-event').val(event).change();
            $('#smartnotas-action').val(action).change();
            $('#modal-action-smartnotas form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-smartnotas form');
        },
        'webhook': ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            $('#webhook-is_active').prop('checked', Boolean(is_active));
            $('#webhook-description').val(description).addClass('mui--is-not-empty');
            $('#webhook-event').val(event).change();
            $('#webhook-action').val(action).change();
            $('#modal-action-webhook form').attr('action', `/apps/integrations/${app_id}/actions/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-action-webhook form');
        }
    };

    const metadataApps = {
        'activecampaign': () => {
            activecampaignLists();
            activecampaignTags();
        },
        'infusion': () => {
            infusionTags();
        },
        'mailchimp': () => {
            mailchimpLists();
        }
    };

    const eventsLang = {
        'onCreateLead': 'Lead gerado',
        'onCreateBankSlip': 'Boleto gerado',
        'onApprovePayment': 'Compra aprovada',
        'onRefusePayment': 'Compra recusada',
        'onRefundPayment': 'Compra estornada',
        'onChargebackPayment': 'Compra com chargeback',
        'onCancelSubscription': 'Compra cancelada',
        'onExpirePayment': 'Compra expirada',
    };
</script>