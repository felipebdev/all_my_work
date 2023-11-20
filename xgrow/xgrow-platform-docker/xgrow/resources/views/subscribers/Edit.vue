<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <div class="xgrow-card card-dark p-4">
      <Tab id="tabCourse">
        <template v-slot:header>
          <TabNav
            :items="tabs.items"
            id="tabNav"
            :startTab="tabs.active"
            @change-page="
              (val) => {
                tabs.active = val;
              }
            "
          />
        </template>
        <template v-slot:body>
          <TabContent id="data" :selected="tabs.active === 'data'">
            <EditSubscriber />
          </TabContent>
          <TabContent id="products" :selected="tabs.active === 'products'">
            <EditProducts />
          </TabContent>
          <TabContent id="payment-history" :selected="tabs.active === 'payment-history'">
            <EditPaymentHistory />
          </TabContent>
          <TabContent id="sending-emails" :selected="tabs.active === 'sending-emails'">
            <EditSendingEmails />
          </TabContent>
        </template>
      </Tab>
    </div>
  </div>
</template>

<script>
import StatusModal from "../../js/components/StatusModalComponent";
import Tab from "../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import TabContent from "../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import TabNav from "../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

import EditSubscriber from "./tabs/EditSubscriber.vue";
import EditProducts from "./tabs/EditProducts.vue";
import EditPaymentHistory from "./tabs/EditPaymentHistory.vue"
import EditSendingEmails from "./tabs/EditSendingEmails.vue"

export default {
  components: {
    StatusModal,
    Tab,
    TabContent,
    TabNav,
    Breadcrumb,
    EditSubscriber,
    EditProducts,
    EditPaymentHistory,
    EditSendingEmails
  },
  data() {
    return {
      subscriberId: this.$route.params.id,
      tabs: {
        active: "data",
        items: [
          { title: "Dados", screen: "data" },
          { title: "Produtos", screen: "products" },
          { title: "Histórico de pagamentos", screen: "payment-history" },
          { title: "Envio de e-mails", screen: "sending-emails" },
        ],
      },
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Alunos", link: "/subscribers/next", isVueRouter: true },
        { title: "Editar", link: "#", isVueRouter: true },
      ],
    };
  },
  async mounted() {
  },
  methods: {},
};
</script>

<style lang="scss"></style>
