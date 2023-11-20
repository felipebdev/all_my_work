<template>
  <Modal
    class="success-modal"
    :is-open="$store.state.isOpenSuccessModal"
    @close="$store.commit('toggleSuccessModal', false)"
  >
    <i class="fas fa-check-circle"></i>
    <h5>Token gerado com sucesso!</h5>
    <p class="mb-4">
      O novo token atrelado ao domínio "{{ domain }}" foi gerado com sucesso.
      Clique em "OK" par acontinuar
    </p>
    <div class="d-flex justify-content-center gap-3 mt-5">
      <Button text="OK" status="success" @click="$store.commit('toggleSuccessModal', false)" />
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
.success-modal {
  .modal__content {
    text-align: center;
  }

  button {
    width: 175px;
  }

  .fa-circle-check {
    color: var(--green1);
    font-size: 6rem;
    margin-bottom: 2rem;
  }
}
</style>
