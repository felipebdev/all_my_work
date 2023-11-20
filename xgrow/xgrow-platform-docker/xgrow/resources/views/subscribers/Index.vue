<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
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
        <TabContent id="tabSubscribers" :selected="tabs.active === 'tabSubscribers'">
          <IndexSubscribers />
        </TabContent>
        <TabContent id="tabSubscribersBlocked" :selected="tabs.active === 'tabSubscribersBlocked'">
          <IndexBlockedSubscribers />
        </TabContent>
      </template>
    </Tab>
  </div>
</template>

<script>
import StatusModal from "../../js/components/StatusModalComponent";
import Tab from "../../js/components/XgrowDesignSystem/Tab/XgrowTab";
import TabContent from "../../js/components/XgrowDesignSystem/Tab/XgrowTabContent";
import TabNav from "../../js/components/XgrowDesignSystem/Tab/XgrowTabNav";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import IndexSubscribers from "./tabs/IndexSubscribers"
import IndexBlockedSubscribers from "./tabs/IndexBlockedSubscribers"

export default {
  components: {
    StatusModal,
    Tab,
    TabContent,
    TabNav,
    Breadcrumb,
    IndexSubscribers,
    IndexBlockedSubscribers
  },
  data() {
    return {
      tabs: {
        active: "tabSubscribers",
        items: [
          { title: "Alunos", screen: "tabSubscribers" },
          { title: "Alunos bloqueados", screen: "tabSubscribersBlocked" },
        ],
      },
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Alunos", link: "/subscribers" },
      ],
    };
  },
  async mounted() {},
  methods: {},
};
</script>

<style lang="scss"></style>
