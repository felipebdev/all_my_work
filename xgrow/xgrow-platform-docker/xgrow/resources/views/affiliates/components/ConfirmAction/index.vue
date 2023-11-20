<template>
  <Modal :is-open="modal.open" modalSize="md"
      class="confirm-modal"
      @close="modal.open = false">
      <i class="fa fa-question question"></i>
      <h5>
        Deseja
        <span class="lowercase">{{
          modal.actionTexts[modal.actionType].button
        }}</span>
        afiliação?
      </h5>
      <p>
        Caso você {{ modal.actionTexts[modal.actionType].text }} a afiliaçao de
        <b>{{ modal.user }}</b
        >, esta ação não poderá ser desfeita.
      </p>
      <div class="cta">
        <button class="xgrow-button cancel" @click="modal.open = false">
          Voltar
        </button>
        <button class="xgrow-button" @click="handleAction">
          {{ modal.actionTexts[modal.actionType].button }} afiliação
        </button>
      </div>
    </Modal>
</template>

<script>
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import maska from "maska";

export default {
  name: "AffiliatesActiveShowUserModal",
  components: {
    Modal,
    Row,
    Col,
    Input,
    Subtitle,
  },
  directives: {maska},
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    modal: {
      type: Object,
      default: () => {
        return {
          actionType: "block",
          user: "",
          route: "",
          actionTexts: {
            block: {
              text: "bloqueie",
              button: "Bloquear",
              title: "bloqueada",
            },
          },
        }
      },
    },
    handleAction: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {

    }
  },
  methods: {

  }
};
</script>

<style lang="scss" src="./styles.scss" scoped></style>
<style lang="scss">
.confirm-modal {
  .modal__content {
    padding: 15px;
    max-width: 350px;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 0px !important;

    p {
      text-align: center;
      margin-bottom: 20px;
      color: #FFF;
    }
  }

  .modal-dialog {
    display: flex;
    justify-content: center;
  }
}
</style>
