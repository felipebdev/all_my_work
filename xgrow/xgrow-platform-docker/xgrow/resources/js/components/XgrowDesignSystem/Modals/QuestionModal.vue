<template>
  <Modal :is-open="isOpen" modalSize="lg" class="question-modal" @close="isOpen = false">
    <i class="fa fa-question question"></i>
    <h4 class="text-center">
      {{ title }}
    </h4>
    <p v-if="description">
      {{ description }}
    </p>
    <div class="cta">
      <Button outline @click="isOpen = false" text="Voltar" />
      <Button status="success" @click="callback" :text="actionText" />
    </div>
  </Modal>
</template>

<script>
import Modal from "./Modal";
import Button from "../Buttons/DefaultButton";

export default {
  components: {
    Modal,
    Button,
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true,
      default: false,
    },
    title: {
      type: String,
      required: true,
      default: "Deseja confirmar a ação?",
    },
    description: {
      type: String,
      default: "Caso você prossiga, essa ação não poderá ser desfeita.",
    },
    actionText: {
      type: String,
      default: "Confirmar",
    },
    callback: {
      type: Function,
      default: () => {},
    },
  },
};
</script>

<style lang="scss">
.question-modal {
  .modal-dialog {
    display: flex;
    justify-content: center;
  }
  .modal__content {
    padding: 40px 52px;
    max-width: 600px;
    height: 430px !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 0px !important;

    p {
      text-align: center;
      margin-bottom: 40px;
      color: #fff;
      font-weight: 400;
      font-size: 16px;
    }
  }
}
</style>
<style lang="scss" scoped>
.question {
  height: 80px;
  background: #9b9b9b;
  padding: 15px 30px;
  border-radius: 50%;
  color: #2a2e39;
  margin-bottom: 30px;
  margin-top: 30px;
}
.cancel {
  background: none;
  border: 2px solid;
}
.cta {
  display: flex;
  justify-content: space-between;
  width: 100%;
  gap: 50px;

  button {
    width: 100%;
  }
}
</style>
