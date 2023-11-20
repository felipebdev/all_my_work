<template>
    <div class="pb-2 mb-3">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="tab-pane-title card-title">
                    <h5 class="mb-3">Integrações</h5>
                </div>
                <p class="xgrow-medium-regular card-subtitle">
                    Veja todas a integrações disponíveis em nossa plataforma.
                </p>
            </div>
        </div>

        <integration-card-section title="Todas as integrações"/>
        <div class="row row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4">
            <integration-card
                v-for="integration in integrations"
                :key="integration"
                :integration="integration"
            />
        </div>
    </div>
</template>

<script>
import integrations from "../../../../../resources/js/integrations/integrations.json";
import IntegrationCard from "./IntegrationCard";
import IntegrationCardSection from "./IntegrationCardSection";

export default {
    name: "IntegrationsIndexComponent",
    components: {IntegrationCardSection, IntegrationCard},
    data() {
        return {
            integrations: []
        };
    },
    methods: {
        /** Load integrations */
        loadIntegrations: function () {
            this.integrations = integrations;
        }
    },
    created() {
        this.loadIntegrations();
    }
};
</script>

<style scoped>
.card-title h5 {
    font-size: 24px;
    padding-bottom: 0;
}

.card-subtitle {
    font-size: 16px !important;
}
</style>
