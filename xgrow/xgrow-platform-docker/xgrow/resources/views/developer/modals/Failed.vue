<template>
  <Modal
    class="modal"
    :is-open="$store.state.isOpenFailedModal"
    @close="$store.commit('toggleFailedModal', false)"
  >
    <i class="fas fa-times-circle"></i>
    <h5>Erro ao gerar o token!</h5>
    <p class="mb-4">
      Ocorreu um erro inesperado ao gerar o token para o domínio “{{ domain }}”. 
      Tente novamente mais tarde.
    </p>
    <div class="d-flex justify-content-center gap-3 mt-5">
      <Button text="OK" status="success" @click="$store.commit('toggleFailedModal', false)" />
    </div>
  </Modal>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
  components: {
    Modal,
    Button,
  },
  props: {
    domain: {
      type: String,
      default: ''
    }
  },
};
</script>

<style lang="scss">
.modal {
  .modal__content {
    text-align: center;
  }

  button {
    width: 175px;
  }

  .fa-circle-xmark {
    color: #EB5757;
    font-size: 6rem;
    margin-bottom: 2rem;
  }
}
</style>
