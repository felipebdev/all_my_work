<template>
  <Modal id="cancelConfirmModal" :is-open="isOpen" @close="close">
    <Title
      >Confirmar cancelamento
      {{ subscriptionType === "R" ? "da assinatura" : "do produto" }}</Title
    >
    <hr />
    <Subtitle class="d-block"
      >Você tem certeza que deseja cancelar o produto <b>{{ productName }}</b> do aluno
      <b>{{ subscriberName }}</b
      >?
    </Subtitle>
    <form @submit.prevent="confirmCancellation">
      <Input
        v-if="subscriptionType === 'R'"
        id="cancellation-date"
        type="date"
        label="Data de cancelamento"
        v-model="form.canceled_at"
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
    subscriptionType: {
      type: String,
      default: "",
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
        canceled_at: this.getToday(),
      },
    };
  },
  methods: {
    getToday() {
      const date = new Date();
      let month = date.getMonth() + 1;
      let day = date.getDate();

      if (day < 10) {
        day = `0${day}`;
      }

      if (month < 10) {
        month = `0${month}`;
      }

      return `${date.getFullYear()}-${month}-${day}`;
    },
    async confirmCancellation() {
      this.loading = true;
      try {
        const url = cancelURL.replace(":id", this.subscriptionId);

        const data = {
          plan_id: this.product.payment_plan_id,
        };

        if (this.subscriptionType === "R" ) {
          data.canceled_at = this.form.canceled_at ;
        }

        await axios.put(url, data);
        this.loading = false;
        this.close();
        await this.getProducts();
        successToast("Sucesso!", "Status do produto atualizado com sucesso!");
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
