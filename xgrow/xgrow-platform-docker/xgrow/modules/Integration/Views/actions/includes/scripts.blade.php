<script>
    const integrations = {
        "activecampaign": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                list = "", tags = [], days_never_accessed = 1, change_card_field = []
            } = metadata;
            $("#activecampaign-is_active").prop("checked", Boolean(is_active));
            $("#activecampaign-description").val(description).addClass("mui--is-not-empty");
            $("#activecampaign-event").val(event).change();
            $("#activecampaign-action").val(action).change();
            $("#rdstation-days_never_accessed").val(days_never_accessed);
            $("#activecampaign-list").val(list).change();
            $("#activecampaign-change_card_field").val(change_card_field).change();

            tags.forEach(tag => {
                $("#activecampaign-tags").val(tag).change();
            });

            $("#modal-action-activecampaign form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-activecampaign form");
        },
        "cademi": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                days_never_accessed
            } = metadata;
            $("#cademi-is_active").prop("checked", Boolean(is_active));
            $("#cademi-description").val(description).addClass("mui--is-not-empty");
            $("#cademi-event").val(event).change();
            $("#cademi-action").val(action).change();
            $("#cademi-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-cademi form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-cademi form");
        },
        "hubspot": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                list = [], days_never_accessed = 1
            } = metadata || {};
            $("#hubspot-is_active").prop("checked", Boolean(is_active));
            $("#hubspot-description").val(description).addClass("mui--is-not-empty");
            $("#hubspot-event").val(event).change();
            $("#hubspot-action").val(action).change();
            $("#hubspot-days_never_accessed").val(days_never_accessed).change();
            $("#hubspot-list").val(list).change();

            $("#modal-action-hubspot form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-hubspot form");
        },
        "infusion": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                tags = [], days_never_accessed = 1
            } = metadata;
            $("#infusion-is_active").prop("checked", Boolean(is_active));
            $("#infusion-description").val(description).addClass("mui--is-not-empty");
            $("#infusion-event").val(event).change();
            $("#infusion-action").val(action).change();
            $("#infusion-days_never_accessed").val(days_never_accessed).change();

            tags.forEach(tag => {
                $("#infusion-tags").val(tag).change();
            });

            $("#modal-action-infusion form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-infusion form");
        },
        "kajabi": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                product_webhook = "", days_never_accessed = 1
            } = metadata;
            $("#kajabi-is_active").prop("checked", Boolean(is_active));
            $("#kajabi-description").val(description).addClass("mui--is-not-empty");
            $("#kajabi-product_webhook").val(product_webhook).addClass("mui--is-not-empty");
            $("#kajabi-event").val(event).change();
            $("#kajabi-action").val(action).change();
            $("#kajabi-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-kajabi form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-kajabi form");
        },
        "mailchimp": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                list = "", tags = [], days_never_accessed = 1
            } = metadata;
            $("#mailchimp-is_active").prop("checked", Boolean(is_active));
            $("#mailchimp-description").val(description).addClass("mui--is-not-empty");
            $("#mailchimp-event").val(event).change();
            $("#mailchimp-action").val(action).change();
            $("#mailchimp-days_never_accessed").val(days_never_accessed).change();
            $("#mailchimp-list").val(list).change();

            tags.forEach(tag => {
                $("#mailchimp-tags").val(tag).change();
            });

            $("#modal-action-mailchimp form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-mailchimp form");
        },
        "pipedrive": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#pipedrive-is_active").prop("checked", Boolean(is_active));
            $("#pipedrive-description").val(description).addClass("mui--is-not-empty");
            $("#pipedrive-event").val(event).change();
            $("#pipedrive-action").val(action).change();
            $("#pipedrive-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-pipedrive form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-pipedrive form");
        },
        "octadesk": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#octadesk-is_active").prop("checked", Boolean(is_active));
            $("#octadesk-description").val(description).addClass("mui--is-not-empty");
            $("#octadesk-event").val(event).change();
            $("#octadesk-action").val(action).change();
            $("#octadesk-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-octadesk form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-octadesk form");
        },
        "rdstation": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#rdstation-is_active").prop("checked", Boolean(is_active));
            $("#rdstation-description").val(description).addClass("mui--is-not-empty");
            $("#rdstation-event").val(event).change();
            $("#rdstation-action").val(action).change();
            $("#rdstation-days_never_accessed").val(days_never_accessed);
            $("#modal-action-rdstation form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-rdstation form");
        },
        "smartnotas": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#smartnotas-is_active").prop("checked", Boolean(is_active));
            $("#smartnotas-description").val(description).addClass("mui--is-not-empty");
            $("#smartnotas-event").val(event).change();
            $("#smartnotas-action").val(action).change();
            $("#smartnotas-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-smartnotas form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-smartnotas form");
        },
        "wisenotas": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#wisenotas-is_active").prop("checked", Boolean(is_active));
            $("#wisenotas-description").val(description).addClass("mui--is-not-empty");
            $("#wisenotas-event").val(event).change();
            $("#wisenotas-action").val(action).change();
            $("#wisenotas-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-wisenotas form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-wisenotas form");
        },
        "webhook": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#webhook-is_active").prop("checked", Boolean(is_active));
            $("#webhook-description").val(description).addClass("mui--is-not-empty");
            $("#webhook-event").val(event).change();
            $("#webhook-action").val(action).change();
            $("#webhook-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-webhook form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-webhook form");
        },
        "leadlovers": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                machineCode = 0, sequenceCode = 0, levelCode = 0, tags = [], days_never_accessed = 1
            } = metadata;
            $("#leadlovers-is_active").prop("checked", Boolean(is_active));
            $("#leadlovers-description").val(description).addClass("mui--is-not-empty");
            $("#leadlovers-event").val(event).change();
            $("#leadlovers-action").val(action).change();
            $("#leadlovers-days_never_accessed").val(days_never_accessed).change();
            $("#leadlovers-machine").val(machineCode).change();
            $("#leadlovers-emailSequence").val(sequenceCode).change();
            $("#leadlovers-level").val(levelCode).change();

            tags.forEach(tag => {
                $("#leadlovers-tags").val(tag).change();
            });

            $("#modal-action-leadlovers form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-leadlovers form");
        },
        "mautic": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                list = [], days_never_accessed = 1
            } = metadata;
            $("#mautic-is_active").prop("checked", Boolean(is_active));
            $("#mautic-description").val(description).addClass("mui--is-not-empty");
            $("#mautic-event").val(event).change();
            $("#mautic-action").val(action).change();
            $("#mautic-list").val(list).change();
            $("#mautic-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-mautic form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-mautic form");
        },

        "voxuy": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#voxuy-is_active").prop("checked", Boolean(is_active));
            $("#voxuy-description").val(description).addClass("mui--is-not-empty");
            $("#voxuy-event").val(event).change();
            $("#voxuy-action").val(action).change();
            $("#voxuy-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-voxuy form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-voxuy form");
        },
        "enotas": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#enotas-is_active").prop("checked", Boolean(is_active));
            $("#enotas-description").val(description).addClass("mui--is-not-empty");
            $("#enotas-event").val(event).change();
            $("#enotas-action").val(action).change();
            $("#enotas-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-enotas form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-enotas form");
        },
        "memberkit": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                days_never_accessed
            } = metadata;
            $("#memberkit-is_active").prop("checked", Boolean(is_active));
            $("#memberkit-description").val(description).addClass("mui--is-not-empty");
            $("#memberkit-event").val(event).change();
            $("#memberkit-action").val(action).change();
            $("#memberkit-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-memberkit form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-memberkit form");
        },
        "builderall": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const {
                list = "", tags = [], days_never_accessed = 1
            } = metadata;
            $("#builderall-is_active").prop("checked", Boolean(is_active));
            $("#builderall-description").val(description).addClass("mui--is-not-empty");
            $("#builderall-event").val(event).change();
            $("#builderall-action").val(action).change();
            $("#builderall-days_never_accessed").val(days_never_accessed);
            $("#builderall-list").val(list).change();

            tags.forEach(tag => {
                $("#builderall-tags").val(tag).change();
            });

            $("#modal-action-builderall form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-builderall form");
        },
        "notazz": ({
            id,
            app_id,
            action,
            description,
            event,
            is_active,
            metadata
        }) => {
            const days_never_accessed = metadata === null ? 1 : metadata.days_never_accessed;
            $("#notazz-is_active").prop("checked", Boolean(is_active));
            $("#notazz-description").val(description).addClass("mui--is-not-empty");
            $("#notazz-event").val(event).change();
            $("#notazz-action").val(action).change();
            $("#notazz-days_never_accessed").val(days_never_accessed).change();
            $("#modal-action-notazz form").attr("action", `/apps/integrations/${app_id}/actions/${id}`);
            $("<input>").attr({
                type: "hidden",
                name: "_method",
                value: "PUT"
            }).appendTo("#modal-action-notazz form");
        },
    };

    const metadataApps = {
        "activecampaign": () => {
            activecampaignLists();
            activecampaignTags();
            activecampaignChangeCardField();
        },
        "hubspot": () => {
            hubspotLists();
        },
        "infusion": () => {
            infusionTags();
        },
        "mailchimp": () => {
            mailchimpLists();
        },
        "leadlovers": () => {
            leadloversMachines();
            leadloversTags();
        },
        "mautic": () => {
            mauticLists();
        },
        "builderall": () => {
            builderallLists();
        }
    };

    const eventsLang = {
        "onCreateLead": "Lead gerado",
        "onCreateBankSlip": "Boleto gerado",
        "onApprovePayment": "Compra aprovada",
        "onRefusePayment": "Compra recusada",
        "onRefundPayment": "Compra estornada",
        "onChargebackPayment": "Compra com chargeback",
        "onCancelSubscription": "Matrícula cancelada",
        "onExpirePayment": "Compra expirada"
    };
</script>
