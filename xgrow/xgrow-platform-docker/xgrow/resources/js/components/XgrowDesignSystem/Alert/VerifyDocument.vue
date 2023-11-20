<template>
  <div class="container-fluid p-0">
    <div class="row">
      <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
          <img :src="img" style="margin-right: 1rem" />
          <div>
            <h6>Atenção!</h6>
            <p v-if="!description">
              Antes de realizar sua primeira venda, nós precisamos verificar a sua
              identidade.
              <a style="color: inherit; font-weight: 700" href="/documents"
                >Clique aqui para verificar.</a
              >
            </p>
            <p v-else v-html="description"></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import img from "../../../../../public/xgrow-vendor/assets/img/documents/warning.svg"
export default {
  props: {
    description: {
      type: String,
      default: null
    }
  },
  data() {
    return { img };
  },
};
</script>

<style></style>
