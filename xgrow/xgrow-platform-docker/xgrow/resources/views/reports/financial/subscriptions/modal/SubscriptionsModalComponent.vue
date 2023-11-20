<template>
    <ModalComponent :isOpen="isOpen" modalSize="xl" @close="closeFunction">
        <template v-slot:title>
            Detalhes da assinatura de:
            <a :href="`/subscribers/${modalData.subscription_information?.subscribers_id}/edit`" class="name">
                {{ modalData.subscription_information?.subscribers_name }}
            </a>
        </template>

        <template v-slot:content>
            <XgrowTab id="nav-tabSubscriptionsDetails">
                <template v-slot:header>
                    <XgrowTabNav
                        :items="tabs.items"
                        id="nav-tab"
                        :start-tab="activeScreen"
                        @change-page="changePage"
                    >
                    </XgrowTabNav>
                </template>
                <template v-slot:body>
                    <SubscriptionsPurchaseComponent
                        v-if="modalData.subscription_information"
                        :purchase-information="modalData.subscription_information"
                        :is-active="activeScreen === 'subscriptions.purchase'"
                    >
                    </SubscriptionsPurchaseComponent>
                    <SubscriptionsPaymentsComponent
                        v-if="modalData.payment_information"
                        :payments-information="modalData.payment_information"
                        :is-active="activeScreen === 'subscriptions.payments'"
                    >
                    </SubscriptionsPaymentsComponent>
                </template>
            </XgrowTab>
        </template>
        <template v-slot:footer>
            <button type="button" class="btn btn-success" @click.prevent="closeFunction">
                Voltar
            </button>
        </template>
    </ModalComponent>
</template>

<script>
import ModalComponent from "../../../../../js/components/ModalComponent.vue";
import XgrowTab from "../../../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabNav from "../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import SubscriptionsPurchaseComponent from "./tabs/SubscriptionsPurchaseComponent.vue";
import SubscriptionsPaymentsComponent from "./tabs/SubscriptionsPaymentsComponent.vue";

export default {
    name: "subscriptions-modal-component",
    components: {ModalComponent, XgrowTab, XgrowTabNav, SubscriptionsPurchaseComponent, SubscriptionsPaymentsComponent},
    props: {
        isOpen: {
            type: Boolean,
            required: true,
        },
        closeFunction: {
            type: Function,
            required: true,
        },
        modalData: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            /** Tabs */
            tabs: {
                items: [
                    {
                        title: "Informações da compra",
                        screen: "subscriptions.purchase",
                    },
                    {
                        title: "Pagamentos da recorrência",
                        screen: "subscriptions.payments",
                    },
                ],
            },
            activeScreen: "subscriptions.purchase",
        };
    },
    methods: {
        changePage: function (value) {
            this.activeScreen = value;
        },
    }
};
</script>

<style scoped lang="scss">
:deep(.modal-body) {
    padding: 0;

    & > div {
        width: 100%;
    }
}

.name {
    text-decoration: underline;
    color: #8fb623;
}

.btn-success{
    font-size: 0.875rem;
    font-weight: 700;
    width: fit-content;
    padding: 0.625rem 1.75rem;

    &:hover{
        background: #C4CF00 !important;
        outline: none !important;
    }
    &:active{
        background: #93BC1E !important;
        outline: none !important;
    }
}
</style>
