<template>
  <Modal id="changeStatusModal" :is-open="isOpen" @close="close">
    <Title>Alterar status do produto: {{ productName }}</Title>
    <hr />
    <Subtitle
      >Selecione abaixo qual status será atribuído ao produto selecionado:</Subtitle
    >
    <form @submit.prevent="updateStatus">
      <Select
        id="product-status"
        label="Status do produto"
        placeholder="Selecione uma opção"
        :options="status"
        v-model="form.sub_status"
        class="mb-4"
      />
      <div class="d-flex d-flex justify-content-center gap-4">
        <Button style="width: 200px" text="Cancelar" type="button" outline @click="close" />
        <Button style="width: 200px" text="Salvar" status="success" type="submit" />
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
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js";
import axios from "axios";

export default {
  components: {
    Loading,
    Modal,
    Title,
    Subtitle,
    Select,
    Button,
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    subscriptionId: {
      type: Number,
      default: 0,
    },
    planId: {
      type: Number,
      default: 0,
    },
    productName: {
      type: String,
      default: "",
    },
    close: {
      type: Function,
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
      status: [
        { value: "active", name: "Ativo" },
        { value: "canceled", name: "Cancelado" },
        { value: "pending", name: "Pendente" },
      ],
      form: {
        sub_id: null,
        sub_status: null,
      },
    };
  },
  methods: {
    async updateStatus() {
      this.loading = true;
      try {
        const url = updateUserProductStatusURL;

        const res = await axios.put(url, {
          sub_id: this.subscriptionId,
          plan_id: this.planId,
          sub_status: this.form.sub_status
        })
        this.loading = false;
        this.close()
        await this.getProducts()
        successToast('Sucesso!', 'Status do produto atualizado com sucesso!');
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
  },
};
</script>

<style lang="scss">
#changeStatusModal {
  .modal__content {
    padding: 40px;
  }
}
</style>
