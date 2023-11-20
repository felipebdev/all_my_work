<template>
  <div class="xgrow-card card-dark py-4 material">
    <h3 class="material__title">Material de divulgação</h3>
    <p class="material__description">
      São as instruções que a Xgrow e o Produtor disponibilizam para a
      divulgação deste produto.
    </p>
    <hr />
    <div class="material__content p-3">
      <h6>
        <i class="fas fa-list-ul" aria-hidden="true"></i>
        Instruções para afiliados
      </h6>
      <p v-html="content"></p>
    </div>
  </div>
</template>

<script>
export default {
  name: "AffiliatePromotionalMaterial",
  components: {},
  props: {
    content: {
      type: String,
      default: "",
    },
  },
};
</script>

<style lang="scss" scoped src="./styles.scss"></style>
