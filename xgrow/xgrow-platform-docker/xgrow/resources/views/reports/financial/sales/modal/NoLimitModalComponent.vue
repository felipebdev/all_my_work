<template>
    <ModalComponent :isOpen="isOpen" modalSize="xl" @close="closeFunction">
        <template v-slot:title>
            Detalhes da transação sem limite de:
            <a :href="`/subscribers/${modalData.purchase_information?.subscribers_id}/edit`" class="name">
                {{ modalData.purchase_information?.subscribers_name }}
            </a>
        </template>
        <template v-slot:content>
            <XgrowTab id="nav-tabNoLimtDetails">
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
                    <NoLimitInformationComponent
                        v-if="modalData.purchase_information"
                        :isActive="activeScreen === 'no.limit.information'"
                        :purchase-information="modalData.purchase_information"
                    >
                    </NoLimitInformationComponent>
                    <NoLimitPaymentsComponent
                        v-if="modalData.recurrence_payments"
                        :isActive="activeScreen === 'no.limit.payments'"
                        :recurrence-payments="modalData.recurrence_payments"
                    >
                    </NoLimitPaymentsComponent>
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
import NoLimitInformationComponent from "./tabs/NoLimitInformationComponent";
import NoLimitPaymentsComponent from "./tabs/NoLimitPaymentsComponent";


export default {
    name: "no-limit-modal-component",
    components: {NoLimitPaymentsComponent, NoLimitInformationComponent, ModalComponent, XgrowTab, XgrowTabNav},
    props: {
        isOpen: {
            type: Boolean,
            required: true
        },
        closeFunction: {
            type: Function,
            required: true
        },
        modalData: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            /** Tabs */
            tabs: {
                items: [
                    {
                        title: "Informações da compra",
                        screen: "no.limit.information",
                    },
                    {
                        title: "Pagamentos da recorrência",
                        screen: "no.limit.payments",
                    },
                ],
            },
            activeScreen: "no.limit.information",
        };
    },
    methods: {
        changePage: function (value) {
            this.activeScreen = value;
        },
    },
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

.btn-success {
    font-size: 0.875rem;
    font-weight: 700;
    width: fit-content;
    padding: 0.625rem 1.75rem;

    &:hover {
        background: #C4CF00 !important;
        outline: none !important;
    }

    &:active {
        background: #93BC1E !important;
        outline: none !important;
    }
}
</style>
