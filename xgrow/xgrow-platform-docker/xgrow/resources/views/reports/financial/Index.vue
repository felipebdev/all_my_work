<template>
  <div>
    <Breadcrumb :items="breadcrumbs"></Breadcrumb>
    <Tab id="nav-pages">
      <template v-slot:header>
        <TabNav
          :items="tabs.items"
          id="nav-tab"
          :start-tab="activeScreen"
          @change-page="changePage"
        >
        </TabNav>
      </template>
      <template v-slot:body>
        <TabContent
          id="financialOverview"
          :selected="activeScreen === 'financial.overview'"
        >
          <Overview :get-withdraw="() => getWithdraw()"></Overview>
        </TabContent>

        <TabContent
          id="financialWithdraw"
          :selected="activeScreen === 'financial.withdraw'"
        >
          <Withdraw ref="withdraw"></Withdraw>
        </TabContent>
      </template>
    </Tab>
  </div>
</template>

<script>
import Overview from "./dashboard/tabs/Overview";
import Withdraw from "./dashboard/tabs/Withdraw";

import Breadcrumb from "../../../js/components//XgrowDesignSystem/Breadcrumb/XgrowBreadcrumb.vue";
import Tab from "../../../js/components/XgrowDesignSystem/Tab/XgrowTab.vue";
import TabNav from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabNav.vue";
import TabContent from "../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";

export default {
  components: {
    Overview,
    Withdraw,
    Breadcrumb,
    TabNav,
    Tab,
    TabContent,
  },
  data() {
    return {
      activeScreen: "financial.overview",
      breadcrumbs: [
        { title: "Xgrow", link: "/" },
        { title: "Vendas", link: "/reports/sales" },
        { title: "Financeiro", link: false },
      ],
      tabs: {
        items: [
          { title: "Resumo", screen: "financial.overview" },
          { title: "Saques", screen: "financial.withdraw" },
        ],
      },
    };
  },
  methods: {
    changePage: function (screen) {
      this.activeScreen = screen.toString();
    },
    async getWithdraw() {
      await this.$refs.withdraw.getDataTableWithdraw();
    },
  },
};
</script>

<style></style>
