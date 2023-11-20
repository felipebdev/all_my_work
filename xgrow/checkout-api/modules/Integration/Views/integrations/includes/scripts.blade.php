<script>
    const integrations = {
        'activecampaign': ({
            id,
            is_active,
            description,
            api_key,
            api_webhook
        }) => {
            $('#activecampaign-is_active').prop('checked', Boolean(is_active));
            $('#activecampaign-description_integration').val(description).addClass('mui--is-not-empty');
            $('#activecampaign-api_webhook').val(api_webhook).addClass('mui--is-not-empty');
            $('#activecampaign-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#modal-activecampaign form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-activecampaign form');
        },
        'cademi': ({
            id,
            is_active,
            description,
            api_key,
            api_webhook
        }) => {
            $('#cademi-is_active').prop('checked', Boolean(is_active));
            $('#cademi-description_integration').val(description).addClass('mui--is-not-empty');
            $('#cademi-api_webhook').val(api_webhook).addClass('mui--is-not-empty');
            $('#cademi-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#modal-cademi form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-cademi form');
        },
        'digitalmanagerguru': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const { sourceToken = '', api_key = '' } = JSON.parse(source_token);
            $('#digitalmanagerguru-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass('mui--is-not-empty');
            $('#digitalmanagerguru-flag_enable').prop('checked', Boolean(flag_enable));
            $('#digitalmanagerguru-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#digitalmanagerguru-trigger_email').prop('checked', Boolean(trigger_email));
            $('#digitalmanagerguru-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#digitalmanagerguru-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
            $('#modal-digitalmanagerguru form').attr('action', `/integracao/update/${id}`);
        },
        'eduzz': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            $('#eduzz-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass('mui--is-not-empty');
            $('#eduzz-flag_enable').prop('checked', Boolean(flag_enable));
            $('#eduzz-source_token').val(source_token).addClass('mui--is-not-empty');
            $('#eduzz-trigger_email').prop('checked', Boolean(trigger_email));
            $('#eduzz-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#eduzz-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
            $('#modal-eduzz form').attr('action', `/integracao/update/${id}`);
        },
        'facebookpixel': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const sourceToken = JSON.parse(source_token);
            $('#fb-flag_enable').prop('checked', Boolean(flag_enable));
            $('#fb-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#fb-pixel_id').val(sourceToken.pixel_id || '').addClass('mui--is-not-empty');
            $('#fb-pixel_token').val(sourceToken.pixel_token || '').addClass('mui--is-not-empty');
            $('#fb-pixel_test_event_code').val(sourceToken.pixel_test_event_code || '').addClass('mui--is-not-empty');
            $('#fb-checkout_visit').prop('checked', Boolean(sourceToken.fb_checkout_visit));
            $('#fb-sales_conversion').prop('checked', Boolean(sourceToken.fb_sales_conversion));
            $('#fb-all_payment_methods').prop('checked', Boolean(sourceToken.fb_all_payment_methods));
            $('#fb-card_payment_methods').prop('checked', Boolean(sourceToken.fb_card_payment_methods));
            $('#modal-facebookpixel form').attr('action', `/integracao/update/${id}`);
        },
        'fandone': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const sourceToken = JSON.parse(source_token);
            $('#fandone-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass('mui--is-not-empty');
            $('#fandone-flag_enable').prop('checked', Boolean(flag_enable));
            $('#fandone-source_token').val(source_token).addClass('mui--is-not-empty');
            $('#fandone-trigger_email').prop('checked', Boolean(trigger_email));
            $('#fandone-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#fandone-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-prod_count_id').val(sourceToken.production.count_id || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-prod_public_key').val(sourceToken.production.public_key || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-prod_secret_key').val(sourceToken.production.secret_key || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-homol_count_id').val(sourceToken.local.count_id || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-homol_public_key').val(sourceToken.local.public_key || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#fandone-homol_secret_key').val(sourceToken.local.secret_key || '').removeClass('d-none').addClass('mui--is-not-empty');
            $('#modal-fandone form').attr('action', `/integracao/update/${id}`);
        },
        'googleads': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const sourceToken = JSON.parse(source_token);
            $('#google-flag_enable').prop('checked', Boolean(flag_enable));
            $('#google-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#google-ads_id').val(sourceToken.adwords_id || '').addClass('mui--is-not-empty');
            $('#google-ads_conversion_label').val(sourceToken.ads_conversion_label || '').addClass('mui--is-not-empty');
            $('#google-ads_checkout_visit').prop('checked', Boolean(sourceToken.ads_checkout_visit));
            $('#google-ads_sales_conversion').prop('checked', Boolean(sourceToken.ads_sales_conversion));
            $('#google-ads_all_payment_methods').prop('checked', Boolean(sourceToken.ads_all_payment_methods));
            $('#google-ads_sale_real_price').prop('checked', Boolean(sourceToken.ads_sale_real_price));
            $('#google-ads_sale_client_price').prop('checked', Boolean(sourceToken.ads_sale_client_price));
            $('#modal-googleads form').attr('action', `/integracao/update/${id}`);
        },
        'hotmart': ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            $('#hotmart-days_limit_payment_pendent').val(days_limit_payment_pendent).addClass('mui--is-not-empty');
            $('#hotmart-flag_enable').prop('checked', Boolean(flag_enable));
            $('#hotmart-source_token').val(source_token).addClass('mui--is-not-empty');
            $('#hotmart-trigger_email').prop('checked', Boolean(trigger_email));
            $('#hotmart-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#hotmart-url_webhook').val(url_webhook).removeClass('d-none').addClass('mui--is-not-empty');
            $('#modal-hotmart form').attr('action', `/integracao/update/${id}`);
        },
        'kajabi': ({
            id,
            is_active,
            description,
            api_account
        }) => {
            $('#kajabi-is_active').prop('checked', Boolean(is_active));
            $('#kajabi-description_integration').val(description).addClass('mui--is-not-empty');
            $('#kajabi-api_account').val(api_account).addClass('mui--is-not-empty');
            $('#modal-kajabi form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-kajabi form');
        },
        'mailchimp': ({
            id,
            is_active,
            description,
            api_key
        }) => {
            $('#mailchimp-is_active').prop('checked', Boolean(is_active));
            $('#mailchimp-description_integration').val(description).addClass('mui--is-not-empty');
            $('#mailchimp-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#modal-mailchimp form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-mailchimp form');
        },
        'octadesk': ({
            id,
            is_active,
            description,
            api_key,
            api_account
        }) => {
            $('#octadesk-is_active').prop('checked', Boolean(is_active));
            $('#octadesk-description_integration').val(description).addClass('mui--is-not-empty');
            $('#octadesk-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#octadesk-api_account').val(api_account).addClass('mui--is-not-empty');
            $('#modal-octadesk form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-octadesk form');
        },
        'pandavideo': ({
            id, 
            flag_enable, 
            source_token, 
            name_integration
        }) => {
            const { api_key = '' } = JSON.parse(source_token);
            $('#pandavideo-flag_enable').prop('checked', Boolean(flag_enable));
            $('#pandavideo-api_key').val(api_key).addClass('mui--is-not-empty');
            $('#pandavideo-name_integration').val(name_integration).addClass('mui--is-not-empty');
            $('#modal-pandavideo form').attr('action', `/integracao/update/${id}`);
        },
        'smartnotas': ({
            id,
            is_active,
            description,
            api_webhook,
            metadata: {
                process_after_days = 30
            } = {}
        }) => {
            $('#smartnotas-is_active').prop('checked', Boolean(is_active));
            $('#smartnotas-description_integration').val(description).addClass('mui--is-not-empty');
            $('#smartnotas-api_webhook').val(api_webhook).addClass('mui--is-not-empty');
            $('#smartnotas-process_after_days').val(process_after_days).addClass('mui--is-not-empty');
            $('#modal-smartnotas form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-smartnotas form');
        },
        'webhook': ({
            id,
            is_active,
            description,
            api_webhook
        }) => {
            $('#webhook-is_active').prop('checked', Boolean(is_active));
            $('#webhook-description_integration').val(description).addClass('mui--is-not-empty');
            $('#webhook-api_webhook').val(api_webhook).addClass('mui--is-not-empty');
            $('#modal-webhook form').attr('action', `/apps/integrations/${id}`);
            $('<input>').attr({ type: 'hidden', name: '_method', value: 'PUT' }).appendTo('#modal-webhook form');
        },
    };
</script>