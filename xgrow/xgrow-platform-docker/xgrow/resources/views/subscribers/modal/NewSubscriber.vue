<template>
  <div>
    <Modal
      :is-open="open"
      @close="
        closeModal();
        resetForm();
      "
    >
      <h5>Adicionar novo aluno</h5>
      <hr />
      <p>Insira abaixo os dados solicitados do novo aluno:</p>
      <form @submit="storeUser">
        <Input
          id="name"
          class="mb-3"
          label="Nome completo"
          placeholder="Insira o nome completo do aluno..."
          v-model="form.full_name"
          required
        />
        <Input
          id="email"
          type="email"
          class="mb-3"
          label="E-mail"
          placeholder="Insira o e-mail do aluno..."
          v-model="form.email"
          required
        />
        <Select
          id="plans_id"
          label="Produto"
          placeholder="Selecione uma opção"
          :options="plans"
          v-model="form.plan_id"
          required
        />
        <hr style="margin-top: 40px; margin-bottom: 40px !important" />
        <div class="cta">
          <Button
            id="cancel"
            text="Cancelar"
            outline
            @click="
              closeModal();
              resetForm();
            "
          />
          <Button
            id="create"
            text="Criar aluno e enviar senha"
            type="submit"
            status="success"
          />
        </div>
      </form>
    </Modal>
    <Loading :is-open="loading" status="loading" />
    <StatusModal
      v-if="statusModal.open"
      :close-modal="resetModal"
      :is-open="statusModal.open"
      :status="statusModal.status"
      :title="statusModal.title"
      :description="statusModal.description"
    />
  </div>
</template>

<script>
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import StatusModal from "../../../js/components/XgrowDesignSystem/Modals/StatusModal";
import Loading from "../../../js/components/StatusModalComponent";
import axios from "axios";
import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js"

export default {
  components: {
    Modal,
    Input,
    Select,
    Button,
    StatusModal,
    Loading,
  },
  props: {
    open: {
      type: Boolean,
      default: false,
    },
    closeModal: {
      type: Function,
      default: () => {},
    },
    getSubscribers: {
      type: Function,
      default: () => {},
    },
  },
  data() {
    return {
      loading: false,
      plans: [],
      form: {
        full_name: "",
        email: "",
        plan_id: null,
      },
      statusModal: {
        open: false,
        status: "",
        title: "",
        description: "",
      },
    };
  },
  methods: {
    resetForm() {
      this.form = {
        full_name: "",
        email: "",
        plan_id: null,
      };

      document.getElementById('plans_id').value = ''
    },
    resetModal() {
      this.statusModal = {
        open: false,
        status: "",
        title: "",
        description: "",
      };
    },
    async storeUser(event) {
      event.preventDefault();

      this.loading = true;
      try {

        await axios.post(storeUserURL, this.form);

        await this.getSubscribers();

        this.closeModal();
        this.resetForm();
        this.statusModal.status = "success";
        this.statusModal.title = "Conta criada com sucesso";
        this.statusModal.description = `A conta do aluno <b>${this.form.full_name}</b> foi criada com sucesso e uma
          senha de acesso foi gerada e enviada para o mesmo`;

      } catch (error) {
        this.statusModal.status = "failed";
        this.statusModal.title = "Erro ao criar a conta do aluno!";

        this.loading = false;
        this.statusModal.description = formatTryCatchError(error);
      }

      this.loading = false;
      this.statusModal.open = true;
    },
    async getPlans() {
      const res = await axios.get(plansRoute);
      try {
        this.plans = res.data.plans.map((item) => {
          return {
            value: item.id,
            name: item.name,
          };
        });
      } catch (error) {}
    },
  },
  async mounted() {
    await this.getPlans();
  },
};
</script>

<style>
.modal__content {
  padding: 40px 52px;
}
</style>
<style scoped>
.cta {
  display: flex;
  justify-content: space-evenly;
}
</style>
