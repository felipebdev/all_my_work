<template>
  <div>
    <xgrow-breadcrumb :items="breadcrumbs"></xgrow-breadcrumb>

    <xgrow-tab id="nav-tabContent">
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
        <XgrowTabContent
          id="salesTransactions"
          :selected="activeScreen === 'sales.transactions'"
        >
          <TransactionsComponent
            @start-loading="startLoading"
            @end-loading="endLoading"
            ref="transactionComponent"
          ></TransactionsComponent>
        </XgrowTabContent>
        <XgrowTabContent id="salesNoLimit" :selected="activeScreen === 'sales.no.limit'">
          <NoLimitComponent
            @start-loading="startLoading"
            @end-loading="endLoading"
            ref="noLimitComponent"
          ></NoLimitComponent>
        </XgrowTabContent>
      </template>
    </xgrow-tab>

    <StatusModalComponent :is-open="loading.active" :status="loading.status" />
  </div>
</template>

<script>
import XgrowBreadcrumb from "../../../js/components//XgrowDesignSystem/Breadcrumb/XgrowBreadcrumb.vue";
import XgrowTabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import XgrowTab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import XgrowTabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";

import TransactionsComponent from "./sales/TransactionsComponent.vue";
import NoLimitComponent from "./sales/NoLimitComponent.vue";
import StatusModalComponent from "../../../js/components/StatusModalComponent";
import moment from "moment";

export default {
  name: "Index",
  components: {
    XgrowBreadcrumb,
    XgrowTabNav,
    XgrowTab,
    XgrowTabContent,
    TransactionsComponent,
    NoLimitComponent,
    StatusModalComponent,
  },
  mixins: [],
  data() {
    return {
      loading: {
        active: false,
        status: "loading",
      },

      /** Breadcrumbs */
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Vendas", link: false },
      ],

      /** Tabs */
      tabs: {
        items: [
          { title: "Transações", screen: "sales.transactions" },
          { title: "Sem limite", screen: "sales.no.limit" },
        ],
      },
      activeScreen: "sales.transactions",
    };
  },
  watch: {},
  async mounted() {},
  methods: {
    changePage: function (value) {
      this.activeScreen = value;

      if (value === "sales.no.limit") {
        if (!this.$refs.noLimitComponent.alreadyLoaded) {
          this.$refs.noLimitComponent.loadData();
          this.$refs.noLimitComponent.alreadyLoaded = true;
        }
      }
    },
    startLoading: function () {
      this.loading.active = true;
    },
    endLoading: function () {
      this.loading.active = false;
    },
  },
};
</script>
<style>
.dropdown-item:hover {
  color: #fff;
  background-color: #222429;
}

.modal__content {
  padding: 30px 40px;
  color: #fff;
}
</style>
<style lang="scss" scoped>
.token {
  &__key {
    margin-right: 12px;
  }
  &__actions {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background-color: var(--green1);
    border: 0px;
    margin-right: 12px;
    color: #fff;
  }
  &__search {
    width: 400px;
    padding: 0;
    margin: 0;
  }

  &__copy-message {
    background: #1a1a1a;
    color: white;
    position: absolute;
    top: -35px;
    right: -236px;
    border-radius: 8px;
    padding: 5px 10px;
    font-weight: 400;
    width: 290px;
  }
  &__link {
    position: relative;
    display: inline-block;
  }
}
.icon {
  color: #f96c6c;
}
.fa-check {
  color: var(--green1);
  margin-right: 5px;
}
</style>
