<script>
    const integrations = {
        "activecampaign": ({
            id,
            is_active,
            description,
            api_key,
            api_webhook
        }) => {
            $("#activecampaign-is_active").prop("checked", Boolean(is_active));
            $("#activecampaign-description_integration").val(description).addClass("mui--is-not-empty");
            $("#activecampaign-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#activecampaign-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-activecampaign form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-activecampaign form");
        },
        "cademi": ({
            id,
            is_active,
            description,
            api_key,
            api_webhook
        }) => {
            $("#cademi-is_active").prop("checked", Boolean(is_active));
            $("#cademi-description_integration").val(description).addClass("mui--is-not-empty");
            $("#cademi-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#cademi-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-cademi form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-cademi form");
        },
        "digitalmanagerguru": ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const {
                sourceToken = "", api_key = ""
            } = JSON.parse(source_token);
            $("#digitalmanagerguru-days_limit_payment_pendent").val(days_limit_payment_pendent).addClass(
                "mui--is-not-empty");
            $("#digitalmanagerguru-flag_enable").prop("checked", Boolean(flag_enable));
            $("#digitalmanagerguru-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#digitalmanagerguru-trigger_email").prop("checked", Boolean(trigger_email));
            $("#digitalmanagerguru-name_integration").val(name_integration).addClass("mui--is-not-empty");
            $("#digitalmanagerguru-url_webhook").val(url_webhook).removeClass("d-none").addClass(
                "mui--is-not-empty");
            $("#modal-digitalmanagerguru form").attr("action", `/integracao/update/${id}`);
        },
        "eduzz": ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            $("#eduzz-days_limit_payment_pendent").val(days_limit_payment_pendent).addClass(
            "mui--is-not-empty");
            $("#eduzz-flag_enable").prop("checked", Boolean(flag_enable));
            $("#eduzz-source_token").val(source_token).addClass("mui--is-not-empty");
            $("#eduzz-trigger_email").prop("checked", Boolean(trigger_email));
            $("#eduzz-name_integration").val(name_integration).addClass("mui--is-not-empty");
            $("#eduzz-url_webhook").val(url_webhook).removeClass("d-none").addClass("mui--is-not-empty");
            $("#modal-eduzz form").attr("action", `/integracao/update/${id}`);
        },
        "facebookpixel": ({
            id,
            is_active,
            description,
            api_key,
            api_account,
            metadata
        }) => {
            $("#facebookpixel-is_active").prop("checked", Boolean(is_active));
            $("#facebookpixel-description").val(description).addClass("mui--is-not-empty");
            $("#facebookpixel-api_account").val(api_account).addClass("mui--is-not-empty");
            $("#facebookpixel-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#facebookpixel-test_event_code").val(metadata.test_event_code).addClass("mui--is-not-empty");

            $("#facebookpixel-checkout_visit").prop("checked", Boolean(metadata.checkout_visit));
            $("#facebookpixel-sales_conversion").prop("checked", Boolean(metadata.sales_conversion));
            $(`#facebookpixel-${metadata.payment_method}`).prop("checked", true);

            $("#modal-facebookpixel form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-facebookpixel form");

        },
        "fandone": ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            const sourceToken = JSON.parse(source_token);
            $("#fandone-days_limit_payment_pendent").val(days_limit_payment_pendent).addClass(
                "mui--is-not-empty");
            $("#fandone-flag_enable").prop("checked", Boolean(flag_enable));
            $("#fandone-source_token").val(source_token).addClass("mui--is-not-empty");
            $("#fandone-trigger_email").prop("checked", Boolean(trigger_email));
            $("#fandone-name_integration").val(name_integration).addClass("mui--is-not-empty");
            $("#fandone-url_webhook").val(url_webhook).removeClass("d-none").addClass("mui--is-not-empty");
            $("#fandone-prod_count_id").val(sourceToken.production.count_id || "").removeClass("d-none")
                .addClass("mui--is-not-empty");
            $("#fandone-prod_public_key").val(sourceToken.production.public_key || "").removeClass("d-none")
                .addClass("mui--is-not-empty");
            $("#fandone-prod_secret_key").val(sourceToken.production.secret_key || "").removeClass("d-none")
                .addClass("mui--is-not-empty");
            $("#fandone-homol_count_id").val(sourceToken.local.count_id || "").removeClass("d-none").addClass(
                "mui--is-not-empty");
            $("#fandone-homol_public_key").val(sourceToken.local.public_key || "").removeClass("d-none")
                .addClass("mui--is-not-empty");
            $("#fandone-homol_secret_key").val(sourceToken.local.secret_key || "").removeClass("d-none")
                .addClass("mui--is-not-empty");
            $("#modal-fandone form").attr("action", `/integracao/update/${id}`);
        },
        "googleads": ({
            id,
            description,
            is_active,
            metadata
        }) => {
            $("#google-is_active").prop("checked", is_active);
            $("#google-description").val(description).addClass("mui--is-not-empty");
            $("#google-ads_id").val(metadata.adsId).addClass("mui--is-not-empty");
            $("#google-ads_conversion_label").val(metadata.adsConversionLabel).addClass("mui--is-not-empty");
            $("#google-ads_checkout_visit").prop("checked", metadata.adsCheckoutVisit);
            $("#google-ads_sales_conversion").prop("checked", metadata.adsSalesConversion);
            $("#google-ads_all_payment_methods").prop("checked", metadata.adsPaymentMethods === "all");
            $("#google-ads_card_payment_methods").prop("checked", metadata.adsPaymentMethods === "card");
            $("#google-ads_sale_real_price").prop("checked", metadata.adsSalePrice === "sale");
            $("#google-ads_sale_client_price").prop("checked", metadata.adsSalePrice === "defined");
            $("#modal-googleads form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-googleads form");
        },
        "hubspot": ({
            id,
            is_active,
            description,
            api_key
        }) => {
            $("#hubspot-is_active").prop("checked", Boolean(is_active));
            $("#hubspot-description_integration").val(description).addClass("mui--is-not-empty");
            $("#hubspot-api_key").val(api_key).removeClass("d-none").addClass("mui--is-not-empty");
            $("#modal-hubspot form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-hubspot form");
        },
        "hotmart": ({
            id,
            days_limit_payment_pendent,
            flag_enable,
            source_token,
            trigger_email,
            url_webhook,
            name_integration
        }) => {
            $("#hotmart-days_limit_payment_pendent").val(days_limit_payment_pendent).addClass(
                "mui--is-not-empty");
            $("#hotmart-flag_enable").prop("checked", Boolean(flag_enable));
            $("#hotmart-source_token").val(source_token).addClass("mui--is-not-empty");
            $("#hotmart-trigger_email").prop("checked", Boolean(trigger_email));
            $("#hotmart-name_integration").val(name_integration).addClass("mui--is-not-empty");
            $("#hotmart-url_webhook").val(url_webhook).removeClass("d-none").addClass("mui--is-not-empty");
            $("#modal-hotmart form").attr("action", `/integracao/update/${id}`);
        },
        "kajabi": ({
            id,
            is_active,
            description,
            api_account
        }) => {
            $("#kajabi-is_active").prop("checked", Boolean(is_active));
            $("#kajabi-description_integration").val(description).addClass("mui--is-not-empty");
            $("#kajabi-api_account").val(api_account).addClass("mui--is-not-empty");
            $("#modal-kajabi form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-kajabi form");
        },
        "mailchimp": ({
            id,
            is_active,
            description,
            api_key
        }) => {
            $("#mailchimp-is_active").prop("checked", Boolean(is_active));
            $("#mailchimp-description_integration").val(description).addClass("mui--is-not-empty");
            $("#mailchimp-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-mailchimp form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-mailchimp form");
        },
        "pipedrive": ({
            id,
            is_active,
            description,
            api_key,
            api_account
        }) => {
            $("#pipedrive-is_active").prop("checked", Boolean(is_active));
            $("#pipedrive-description_integration").val(description).addClass("mui--is-not-empty");
            $("#pipedrive-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#pipedrive-api_account").val(api_account).removeClass("d-none").addClass("mui--is-not-empty");
            $("#modal-pipedrive form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-pipedrive form");
        },
        "octadesk": ({
            id,
            is_active,
            description,
            api_key,
            api_account
        }) => {
            $("#octadesk-is_active").prop("checked", Boolean(is_active));
            $("#octadesk-description_integration").val(description).addClass("mui--is-not-empty");
            $("#octadesk-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#octadesk-api_account").val(api_account).addClass("mui--is-not-empty");
            $("#modal-octadesk form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-octadesk form");
        },
        "pandavideo": ({
            id,
            flag_enable,
            source_token,
            name_integration
        }) => {
            const {
                api_key = ""
            } = JSON.parse(source_token);
            $("#pandavideo-flag_enable").prop("checked", Boolean(flag_enable));
            $("#pandavideo-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#pandavideo-name_integration").val(name_integration).addClass("mui--is-not-empty");
            $("#modal-pandavideo form").attr("action", `/integracao/update/${id}`);
        },
        "smartnotas": ({
            id,
            is_active,
            description,
            api_webhook,
            metadata: {
                process_after_days = 30
            } = {}
        }) => {
            $("#smartnotas-is_active").prop("checked", Boolean(is_active));
            $("#smartnotas-description_integration").val(description).addClass("mui--is-not-empty");
            $("#smartnotas-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#smartnotas-process_after_days").val(process_after_days).addClass("mui--is-not-empty");
            $("#modal-smartnotas form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-smartnotas form");
        },
        "wisenotas": ({
            id,
            is_active,
            description,
            api_key,
            api_webhook,
            metadata: {
                process_after_days = 30
            } = {}
        }) => {
            $("#wisenotas-is_active").prop("checked", Boolean(is_active));
            $("#wisenotas-description_integration").val(description).addClass("mui--is-not-empty");
            $("#wisenotas-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#wisenotas-api_key").val(api_key).removeClass("d-none").addClass("mui--is-not-empty");
            $("#wisenotas-process_after_days").val(process_after_days).addClass("mui--is-not-empty");
            $("#modal-wisenotas form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-wisenotas form");
        },
        "webhook": ({
            id,
            is_active,
            description,
            api_webhook
        }) => {
            $("#webhook-is_active").prop("checked", Boolean(is_active));
            $("#webhook-description_integration").val(description).addClass("mui--is-not-empty");
            $("#webhook-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#modal-webhook form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-webhook form");
        },
        "leadlovers": ({
            id,
            is_active,
            description,
            api_key
        }) => {
            $("#leadlovers-is_active").prop("checked", Boolean(is_active));
            $("#leadlovers-description_integration").val(description).addClass("mui--is-not-empty");
            $("#leadlovers-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-leadlovers form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-leadlovers form");
        },
        "mautic": ({
            id,
            is_active,
            description,
            api_webhook,
            api_account,
            api_key
        }) => {
            $("#mautic-is_active").prop("checked", Boolean(is_active));
            $("#mautic-description_integration").val(description).addClass("mui--is-not-empty");
            $("#mautic-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#mautic-api_account").val(api_account).addClass("mui--is-not-empty");
            $("#mautic-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-mautic form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-mautic form");
        },
        "voxuy": ({
            id,
            is_active,
            description,
            api_webhook,
            api_key,
            metadata
        }) => {
            const {
                planId
            } = metadata;
            $("#voxuy-is_active").prop("checked", Boolean(is_active));
            $("#voxuy-description_integration").val(description).addClass("mui--is-not-empty");
            $("#voxuy-api_webhook").val(api_webhook).addClass("mui--is-not-empty");
            $("#voxuy-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#voxuy-planId").val(planId).addClass("mui--is-not-empty");
            $("#modal-voxuy form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-voxuy form");
        },
        "enotas": ({
            id,
            is_active,
            description,
            api_key,
            metadata
        }) => {
            const {
                process_after_days
            } = metadata;
            $("#enotas-is_active").prop("checked", Boolean(is_active));
            $("#enotas-description_integration").val(description).addClass("mui--is-not-empty");
            $("#enotas-process_after_days").val(process_after_days).addClass("mui--is-not-empty");
            $("#enotas-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-enotas form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-enotas form");
        },
        "memberkit": ({
            id,
            is_active,
            description,
            api_key,
            metadata
        }) => {
            // const { } = metadata;
            $("#memberkit-is_active").prop("checked", Boolean(is_active));
            $("#memberkit-description_integration").val(description).addClass("mui--is-not-empty");
            $("#memberkit-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-memberkit form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-memberkit form");
        },
        "rdstation": ({
            id,
            is_active,
            description,
            api_key,
            metadata
        }) => {
            // const { } = metadata;
            $("#rdstation-is_active").prop("checked", Boolean(is_active));
            $("#rdstation-description_integration").val(description).addClass("mui--is-not-empty");
            $("#rdstation-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-rdstation form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-rdstation form");
        },
        "tiktok": ({
            id,
            is_active,
            description,
            api_key
        }) => {
            $("#tiktok-is_active").prop("checked", Boolean(is_active));
            $("#tiktok-description_integration").val(description).addClass("mui--is-not-empty");
            $("#tiktok-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-tiktok form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-tiktok form");
        },
        "builderall": ({
            id,
            is_active,
            description,
            api_key,
        }) => {
            $("#builderall-is_active").prop("checked", Boolean(is_active));
            $("#builderall-description_integration").val(description).addClass("mui--is-not-empty");
            $("#builderall-api_key").val(api_key).addClass("mui--is-not-empty");
            $("#modal-builderall form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-builderall form");
        },
        "notazz": ({
            id,
            is_active,
            description,
            api_key,
            api_webhook,
            metadata: {
                process_after_days = 30
            } = {}
        }) => {
            $("#notazz-is_active").prop("checked", Boolean(is_active));
            $("#notazz-description_integration").val(description).addClass("mui--is-not-empty");
            $("#notazz-api_key").val(api_key).removeClass("d-none").addClass("mui--is-not-empty");
            $("#notazz-process_after_days").val(process_after_days).addClass("mui--is-not-empty");
            $("#modal-notazz form").attr("action", `/apps/integrations/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-notazz form");
        },
    };
</script>
