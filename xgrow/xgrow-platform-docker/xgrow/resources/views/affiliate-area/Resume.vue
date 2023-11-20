<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <div class="xgrow-tabs nav nav-tabs mb-3">
      <a
        role="button"
        class="xgrow-tab-item nav-item nav-link"
        @click="setTab('links')"
        :class="{ active: tab === 'links' }"
      >
        Links para afiliados
      </a>
      <a
        role="button"
        class="xgrow-tab-item nav-item nav-link"
        @click="setTab('promotional_material')"
        :class="{ active: tab === 'promotional_material' }"
      >
        Material de divulgação
      </a>

      <a
        role="button"
        class="xgrow-tab-item nav-item nav-link"
        @click="setTab('events')"
        :class="{ active: tab === 'events' }"
      >
        Eventos
      </a>

    </div>
    <div class="tab-content" id="nav-tabContent">
      <div class="tab-pane fade" :class="{ 'show active': tab === 'links' }">
        <Links
          :links="links"
          :product-name="productName"
          :product="{
            name: productName,
            supportEmail
          }" />
      </div>
      <div
        class="tab-pane fade"
        :class="{ 'show active': tab === 'promotional_material' }"
      >
        <PromotionalMaterial :content="promotionalMaterial" />
      </div>

      <div
        class="tab-pane fade"
        :class="{ 'show active': tab === 'events' }"
      >
        <Events :content="promotionalMaterial" />
      </div>

    </div>
    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Links from "./components/Links";
import PromotionalMaterial from "./components/PromotionalMaterial";
import Events from "./components/Events";
import StatusModalComponent from "../../js/components/StatusModalComponent";
import axios from "axios";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

export default {
  name: "Index",
  components: {
    Links,
    PromotionalMaterial,
    StatusModalComponent,
    Events,
    Breadcrumb
  },
  data() {
    return {
      loading: false,
      viewMode: "grid",
      platformId: localStorage.getItem("affiliates-platform_id"),
      productId: localStorage.getItem("affiliates-product_id"),
      productName: localStorage.getItem("affiliates-product_name"),
      tab: "links",
      links: {
        main: {
          id: 0,
          name: '',
          link: ''
        },
        additional: {
          id: 0,
          name: '',
          link: ''
        },
      },
      promotionalMaterial: "",
      supportEmail: "",
      app_env: '',
      breadcrumbs: [
        { title: "Área do afiliado", link: "/affiliations", isVueRouter: true },
        { title: "Produtos", link: "/affiliations/products" , isVueRouter: true},
        { title: "Resumo", link: "#", isVueRouter: true },
      ],
    };
  },
  async mounted() {
    this.setMenu();

    if (!this.productId) {
      if (!this.platformId) {
        return this.$router.push("/affiliates");
      }

      this.$router.push("/affiliates/products");
    }

    this.app_env = env;

    await this.getData();
  },
  methods: {
    setTab(value) {
      this.tab = value;
    },
    async getData() {
      this.loading = true;

      const productsUrl = affiliateProductsResumeUrl.replace(
        "platform_id",
        this.productId
      );

      const { data } = await axios.get(productsUrl);

      this.promotionalMaterial = data.instructions.instructions ?? '';
      this.supportEmail = data.instructions.support_email;

      this.links = {
        main: data.links,
        additional: data.additional_links,
      };
      this.loading = false;
    },
    setMenu() {
      document.getElementById('coProducerButton').style.display = 'none'
      document.getElementById('platforms-link').style.display = 'none'
      document.getElementById('affiliations-link').style.display = 'none'
      document.getElementById('documents-link').style.display = 'none'
      document.getElementById('affiliate-link-2-withdraw').style.display = 'none'

      document.getElementById('affiliate-link-1').classList.add('active')
      document.getElementById('affiliate-link-2-withdraw').classList.remove('active')
      document.getElementById('affiliate-link-2-transactions').classList.remove('active')

      document.getElementById('affiliate-link-2-withdraw').style.display = 'block'
      document.getElementById('affiliate-link-2-transactions').style.display = 'block'
      document.getElementById('affiliate-link-1').style.display = 'block'
      document.getElementById('affiliate-link-2').style.display = 'block'
      document.getElementById('affiliate-link-2-content').style.display = 'block'
    }
  }
};
</script>

<style lang="scss" scoped></style>
