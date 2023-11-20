<template>
  <ModalComponent :isOpen="isOpen" modalSize="xl" @close="closeFunction">
    <template v-slot:title>
      Detalhes da transação de:
      <a
        :href="`/subscribers/${modalData.sale_information?.subscribers_id}/edit`"
        class="name"
      >
        {{ modalData.sale_information?.subscribers_name }}
      </a>
    </template>
    <template v-slot:content>
      <XgrowTab id="nav-tabNoLimtDetails" class="mt-3">
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
          <TransactionsSalesComponent
            v-if="modalData.sale_information"
            :isActive="activeScreen === 'transactions.sales'"
            :sale-information="modalData.sale_information"
          >
          </TransactionsSalesComponent>
          <TransactionsPaymentsComponent
            v-if="modalData.payment_information"
            :isActive="activeScreen === 'transactions.payments'"
            :payment-information="modalData.payment_information"
            :payment-id="modalData.payment_id"
            :get-transactions="getTransactions"
            :close="closeFunction"
          >
          </TransactionsPaymentsComponent>
          <TransactionsComissionsComponent
            v-if="modalData.commissions"
            :isActive="activeScreen === 'transactions.comissions'"
            :commissions="modalData.commissions"
          >
          </TransactionsComissionsComponent>
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
import TransactionsSalesComponent from "./tabs/TransactionsSalesComponent.vue";
import TransactionsPaymentsComponent from "./tabs/TransactionsPaymentsComponent.vue";
import TransactionsComissionsComponent from "./tabs/TransactionsComissionsComponent.vue";

export default {
  name: "transactions-modal-component",
  components: {
    ModalComponent,
    XgrowTab,
    XgrowTabNav,
    TransactionsSalesComponent,
    TransactionsPaymentsComponent,
    TransactionsComissionsComponent,
  },
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
    getTransactions: {
      type: Function,
      required: true,
      default: () => {},
    },
  },
  data() {
    return {
      /** Tabs */
      tabs: {
        items: [
          {
            title: "Informações da venda",
            screen: "transactions.sales",
          },
          {
            title: "Informações do pagamento",
            screen: "transactions.payments",
          },
          {
            title: "Comissões",
            screen: "transactions.comissions",
          },
        ],
      },
      activeScreen: "transactions.sales",
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
    background: #c4cf00 !important;
    outline: none !important;
  }

  &:active {
    background: #93bc1e !important;
    outline: none !important;
  }
}
</style>
