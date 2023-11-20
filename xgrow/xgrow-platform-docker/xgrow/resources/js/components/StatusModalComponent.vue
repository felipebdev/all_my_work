<template>
  <div class="bg" :style="isOpen ? 'display:block' : 'display:none'">
    <div
      class="modal-sections modal"
      tabindex="-1"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
      :style="isOpen ? 'display:block' : 'display:none'"
    >
      <div class="modal-dialog modal-dialog-centered modal-md">
        <div
          class="modal-content"
          style="max-width: 100% !important; padding: 0 0 65px 0"
        >
          <div class="modal-header">
            <button
              type="button"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="isOpen = !isOpen"
              v-show="!['loading', 'saving'].includes(status)"
            >
              <i class="fa fa-times" style="font-size: 2rem"></i>
            </button>
            <div style="height:34px"
            v-show="['loading', 'saving'].includes(status)"
            ></div>
          </div>
          <div class="modal-body d-block">
            <div class="align-self-center mb-3">
              <img
                src="/xgrow-vendor/assets/img/logo/dark.svg"
                height="46"
              />
            </div>
            <h5><span v-html="getText"></span></h5>
            <div class="fa-7x"><span v-html="getIcon"></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "StatusModalComponent",
  props: {
    status: { type: String, default: "success" },
    isOpen: { type: Boolean, default: false },
  },
  data() {
    return {
      open: true,
    };
  },

  computed: {
    getIcon() {
      if (
        this.status === "loading" ||
        this.status === "saving" ||
        this.status === "creatingPlatform"
      )
        return '<i class="fas fa-circle-notch fa-spin"></i>';
      if (this.status === "success")
        return '<i class="fas fa-check-circle" style="color:var(--green1)"></i>';
      if (this.status === "error")
        return '<i class="fas fa-info-circle" style="color:#eb5757"></i>';
      if (this.status === "creatingPlatform") return "";
    },
    getText() {
      if (this.status === "loading")
        return "Aguarde, estamos carregando as<br>informações...";
      if (this.status === "saving")
        return "Aguarde enquanto salvamos<br>as informações...";
      if (this.status === "success") return "Informações salvas com sucesso!";
      if (this.status === "error")
        return "Ocorreu algum problema ao salvar as<br>informações, tente novamente mais tarde.";
      if (this.status === "creatingPlatform")
        return "<img src='xgrow-vendor/assets/img/dashboard.svg' class='mt-3'/><br><b>Criando sua plataforma</b><br>Estamos preparando tudo para você";
    },
  },
};
</script>

<style scoped>
.bg {
  background: rgba(0, 0, 0, 0.5);
  width: 100%;
  height: 100%;
  position: fixed;
  left: 0;
  top: 0;
  z-index: 99999;
  display: block;
}

@media (min-width: 992px) {
  .modal-md {
    max-width: 600px;
  }
}
</style>
