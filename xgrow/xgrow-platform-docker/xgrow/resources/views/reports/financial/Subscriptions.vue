<template>
  <div>
    <Breadcrumb :items="breadcrumbs" />
    <Subscriptions />
  </div>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Subscriptions from "./subscriptions/Subscriptions";

export default {
  components: {
    Breadcrumb,
    Subscriptions,
  },
  data() {
    return {
      breadcrumbs: [
        { title: "Início", link: "/" },
        { title: "Vendas", link: "/reports/sales" },
        { title: "Assinatura", link: false },
      ],
    };
  },
};
</script>

<style></style>
