<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <div class="xgrow-tabs nav nav-tabs mb-3">
      <a
        class="xgrow-tab-item nav-item nav-link"
        @click="setTab('myAffiliations')"
        :class="{ active: tab === 'myAffiliations' }"
      >
        Minhas afiliações
      </a>
      <a
        class="xgrow-tab-item nav-item nav-link"
        @click="setTab('myRequests')"
        :class="{ active: tab === 'myRequests' }"
      >
        Pedidos
      </a>
    </div>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade show" :class="{ active: tab === 'myAffiliations' }">
        <Platforms />
      </div>
      <div class="tab-pane fade show" :class="{ active: tab === 'myRequests' }">
        <Requests />
      </div>
    </div>
  </div>
</template>

<script>
import Platforms from "./components/Platforms";
import Requests from "./components/Requests";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

export default {
  name: "Index",
  components: {
    Platforms,
    Requests,
    Breadcrumb,
  },
  data() {
    return {
      tab: "myAffiliations",
      breadcrumbs: [
        { title: "Área do afiliado", link: "#", isVueRouter: true },
      ],
    };
  },
  async mounted() {
    this.setMenu();
  },
  methods: {
    setTab(value) {
      this.tab = value;
    },
    setMenu() {
      document.getElementById("coProducerButton").style.display = "flex";
      document.getElementById("platforms-link").style.display = "flex";
      document.getElementById("affiliations-link").style.display = "flex";
      document.getElementById("documents-link").style.display = "flex";

      document.getElementById("affiliate-link-1").style.display = "none";
      document.getElementById("affiliate-link-2").style.display = "none";
      document.getElementById("affiliate-link-2-content").style.display = "none";
    },
  },
};
</script>

<style lang="scss" scoped>
.xgrow-tab-item.nav-item {
  cursor: pointer;
}
</style>
