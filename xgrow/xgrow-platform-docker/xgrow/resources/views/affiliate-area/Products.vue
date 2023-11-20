<template>
  <div>
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Products />
  </div>
</template>

<script>
import Products from "./components/Products";
import Breadcrumb from "../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";

export default {
  name: "Index",
  components: {
    Products,
    Breadcrumb
  },
  data() {
    return {
      breadcrumbs: [
        { title: "Área do afiliado", link: "/affiliations", isVueRouter: true },
        { title: "Produtos", link: "#" , isVueRouter: true},
      ],
    };
  },
  async mounted() {
    this.setMenu()
  },
  methods: {
    setMenu() {
      document.getElementById("coProducerButton").style.display = "flex";
      document.getElementById("platforms-link").style.display = "flex";
      document.getElementById("affiliations-link").style.display = "flex";
      document.getElementById("documents-link").style.display = "flex";

      document.getElementById('affiliate-link-2-withdraw').style.display = 'none'
      document.getElementById("affiliate-link-1").style.display = "none";
      document.getElementById("affiliate-link-2").style.display = "none";
      document.getElementById("affiliate-link-2-content").style.display = "none";
    }
  }
};
</script>

<style lang="scss" scoped></style>
