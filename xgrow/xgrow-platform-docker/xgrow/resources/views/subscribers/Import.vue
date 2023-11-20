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
        <TabContent id="tabImport" :selected="tabs.active === 'tabImport'">
          <ImportSubscribers />
        </TabContent>
        <TabContent id="tabImportStatus" :selected="tabs.active === 'tabImportStatus'">
          <ImportStatus />
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
import ImportSubscribers from "./tabs/ImportSubscribers";
import ImportStatus from "./tabs/ImportStatus";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

export default {
  components: {
    StatusModal,
    Tab,
    TabContent,
    TabNav,
    ImportSubscribers,
    ImportStatus,
    Breadcrumb,
  },
  data() {
    return {
      tabs: {
        active: "tabImport",
        items: [
          { title: "Importar alunos", screen: "tabImport" },
          { title: "Status da importação", screen: "tabImportStatus" },
        ],
      },
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Alunos", link: "/subscribers" },
        { title: "Importar", link: false },
      ],
    };
  },
  async mounted() {},
  methods: {},
};
</script>

<style lang="scss"></style>
