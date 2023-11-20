

<template>
  <Modal class="modal" :is-open="$store.state.isDeleteConfirmModal" @close="$store.commit('toggleDeleteConfirmModal', false)">
    <i class="fas fa-question-circle"></i>
    <h5>Tem certeza que deseja excluir o Token?</h5>
    <hr />
    <p class="mb-4">Essa ação é irreversível!</p>

    <div class="d-flex justify-content-center gap-3 mt-5">
      <Button
        :outline="true"
        text="Cancelar"
        @click="$store.commit('toggleDeleteConfirmModal', false)"
      />
      <Button
        text="Confirmar"
        status="success"
        @click="$emit('confirmed')"
      />
    </div>
  </Modal>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";

export default {
  components: {
    Modal,
    Input,
    Button,
  },
  props: {
  },
};
</script>

<style lang="scss" scoped>
.modal {
  text-align: center;

  button {
    width: 175px;
  }

  .fa-circle-question {
    font-size: 6rem;
    margin-bottom: 2rem;
  }
}
</style>
