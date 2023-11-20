<template>
  <Modal :is-open="isOpen" modalSize="lg" class="status-modal" @close="closeModal">
    <i :class="getIcon()" class="icon"></i>
    <h4 class="text-center">
      {{ title }}
    </h4>
    <p v-if="description" v-html="description" />
    <div class="cta">
      <Button status="success" @click="closeModal" :text="actionText" />
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
      default: "Default Title",
    },
    description: {
      type: String,
      default: "Default Description",
    },
    actionText: {
      type: String,
      default: "Ok",
    },
    status: {
      type: String,
      required: true,
    },
    closeModal: {
      type: Function,
      default: () => {}
    }
  },
  methods: {
    getIcon() {
      const icons = {
        success: 'fa fa-check icon__success',
        failed: 'fa-solid fa-xmark icon__failed',
        info: 'fas fa-info icon__info',
        warning: 'fa fa-exclamation-triangle icon__warning',
      }

      return icons[this.status]
    }
  }
};
</script>

<style lang="scss">
.status-modal {
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
.icon {
  height: 80px;
  background: #9b9b9b;
  padding: 15px 30px;
  border-radius: 50%;
  color: #2a2e39;
  margin-bottom: 30px;
  margin-top: 30px;

  &__success {
    background: var(--status-success);
    padding: 15px!important;
  }
  &__failed {
    background: var(--status-danger);
  }
  &__info {
    background: var(--status-info);
    padding: 15px 40px;
  }
  &__warning {
    background: var(--status-warning);
    padding: 15px;
  }
}

.cancel {
  background: none;
  border: 2px solid;
}
.cta {
  display: flex;
  width: 100%;
  justify-content: center;

  button {
    width: 100%;
    max-width: 150px;
  }
}
</style>
