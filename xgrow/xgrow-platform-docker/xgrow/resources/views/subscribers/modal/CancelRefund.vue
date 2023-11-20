<template>
  <Modal id="cancelConfirmModal" :is-open="isOpen" @close="close">
    <Title
      >Confirmar cancelamento de produto e estorno de pagamento</Title
    >
    <hr />
    <Subtitle class="d-block"
      >Você tem certeza que deseja cancelar o produto <b>{{ productName }}</b> do aluno
      <b>{{ subscriberName }}</b
      > e <b>estornar os pagamentos</b>?
    </Subtitle>
    <form @submit.prevent="cancelRevert">
      <Input
        id="reason"
        label="Motivo"
        placeholder="Informe o motivo do cancelamento e estorno"
        v-model="form.reason"
        class="mb-4"
      />
      <div class="confirm-cta d-flex d-flex justify-content-center gap-4">
        <Button
          style="width: 200px"
          text="Voltar"
          type="button"
          outline
          @click="close"
        />
        <Button style="width: 200px" text="Sim, cancelar" status="success" type="submit" />
      </div>
    </form>
    <Loading :is-open="loading" status="loading" />
  </Modal>
</template>

<script>
import Loading from "../../../js/components/StatusModalComponent";
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js";
import axios from "axios";

export default {
  components: {
    Loading,
    Modal,
    Title,
    Subtitle,
    Input,
    Button,
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    productName: {
      type: String,
      default: "",
    },
    subscriptionId: {
      type: Number,
      default: 0,
    },
    subscriberName: {
      type: String,
      default: "",
    },
    close: {
      type: Function,
      default: () => {},
    },
    product: {
      type: Object,
      default: () => {},
    },
    getProducts: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
      loading: false,
      form: {
        reason: ""
      },
    };
  },
  methods: {
    async cancelRevert() {
      try {
        this.loading = true;
        const res = await axios.post(cancelRevertURL, {
          type: this.product.payment_type_payment,
          payment_plan_id: this.product.payment_plan_id,
          reason: this.form.reason,
        });
        this.loading = false;
        successToast("Sucesso!", res.data.message);
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
  },
};
</script>

<style lang="scss">


#cancelConfirmModal {
  .modal__content {
    padding: 40px;
    position: relative;
  }

  .form-group {
    margin-bottom: 4.5em!important;
  }
}

.confirm-cta {
  position: absolute;
    bottom: 40px;
    right: 0;
    width: 100%;
}
</style>
