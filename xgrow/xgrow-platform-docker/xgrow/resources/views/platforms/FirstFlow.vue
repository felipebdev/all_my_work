<template>
  <div class="py-4 mb-2">
    <h4 class="text-center text-white">Olá, {{ user.name }}!</h4>
    <p class="mb-5 text-center text-white">
      Primeiro, precisamos saber algumas informações para dar continuidade ao
      seu cadastro.
    </p>
    <div
      class="xgrow-tabs nav nav-tabs justify-content-center"
      style="border-bottom: 1px solid #353a47"
    >
      <a
        role="button"
        class="xgrow-tab-item nav-item nav-link"
        :class="{ active: activeTab === 'registerType' }"
      >
        Tipo de cadastro
      </a>
      <a
        role="button"
        class="xgrow-tab-item nav-item nav-link"
        :class="{ active: activeTab === 'identifyData' }"
      >
        Dados de identificação
      </a>
    </div>
    <div
      class="tab-pane fade p-4"
      :class="activeTab === 'registerType' ? 'show active' : 'd-none'"
    >
      <p class="mb-2 text-center text-white" style="font-weight: 600">
        Para onde devemos enviar os seus saques?
      </p>
      <p class="mb-4 text-center text-white">Selecione apenas 1 opção.</p>
      <div class="xgrow-inner-card">
        <div class="d-flex justify-content-center gap-2">
          <div>
            <Checkbox
              id="CNPJ"
              label="Quero receber na minha empresa (CNPJ)."
              :checked="registerType === 'CNPJ'"
              @checked="selectRegisterType('CNPJ')"
            />
          </div>
          <div>
            <Checkbox
              id="CPF"
              label="Quero receber na minha conta de pessoa física (CPF)."
              :checked="registerType === 'CPF'"
              @checked="selectRegisterType('CPF')"
            />
          </div>
        </div>
      </div>
      <div
        class="py-4 mt-4 d-flex justify-content-center"
        style="border-top: 1px solid #353a47"
      >
        <Button
          text="Confirmar e prosseguir"
          status="success"
          @click="nextStep"
        />
      </div>
    </div>

    <div
      class="tab-pane fade p-4"
      :class="activeTab === 'identifyData' ? 'show active' : 'd-none'"
    >
      <p class="mb-2 text-center text-white" style="font-weight: 600">
        Informe seus dados de identificação como pessoa {{user.type === 'J' ? 'jurídica' : 'física'}}.
      </p>

      <div
        class="alert alert-warning d-flex align-items-center gap-2"
        role="alert"
      >
        <img
          src="/xgrow-vendor/assets/img/documents/warning.svg"
          alt="Cadastro finalizado"
        />
        <div>
          <p class="text-warning" style="font-weight: 600">Atenção!</p>
          <p class="text-warning">
            Estes dados não poderão ser alterados mais tarde.
          </p>
        </div>
      </div>
      <div class="xgrow-inner-card">
        <div class="row">
          <div class="col-sm-12 col-md-12">
            <Input id="name" v-model="user.name" :label="user.type === 'J' ? 'Razão social' : 'Nome Completo'" />
          </div>
          <div class="col-sm-12 col-md-6">
            <Input
              id="email"
              v-model="user.email"
              label="E-mail"
              :disabled="true"
            />
          </div>
          <div class="col-sm-12 col-md-6">
            <Input
              id="document"
              v-model="user.identity"
              :label="user.type === 'J' ? 'CNPJ' : 'CPF'"
              :mask="
                user.type === 'J' ? '##.###.###/####-##' : '###.###.###-##'
              "
            />
          </div>
        </div>
      </div>
      <div
        class="py-4 mt-4 d-flex justify-content-center gap-4"
        style="border-top: 1px solid #353a47"
      >
        <Button
          text="Voltar"
          status="dark"
          :outline="true"
          @click="backStep"
        />
        <Button
          text="Confirmar e prosseguir"
          status="success"
          @click="openConfirmModal"
        />
      </div>
    </div>

    <Modal :is-open="confirmModal" @close="confirmModal = false">
      <template v-slot:content>
        <div class="row gap-3 text-center w-100" style="color: var(--gray1)">
          <i
            aria-hidden="true"
            class="fas custom-alert-symbol fa-exclamation-circle fa-5x"
          ></i>
          <h5 class="m-0 p-0 text-white">
            <b>Deseja realmente continuar?</b>
          </h5>
          <span><b>Estes dados não poderão ser alterados mais tarde.</b></span>
          <p><b>Nome completo:</b> {{ user.name }}</p>
          <p><b>E-mail:</b> {{ user.email }}</p>
          <p><b>Documento:</b> {{ user.identity }}</p>
        </div>
      </template>
      <template
        v-slot:footer="slotProps"
        style="justify-content: center !important"
      >
        <Button
          text="Voltar"
          status="dark"
          :outline="true"
          @click="slotProps.closeModal"
        />

        <Button
          text="Sim, confirmar"
          status="success"
          icon="fas fa-check"
          @click="save"
        />
      </template>
    </Modal>

    <StatusModalComponent
      :is-open="loading"
      :status="status"
    ></StatusModalComponent>

  </div>
</template>

<script>
import axios from "axios";
import Checkbox from "../../js/components/XgrowDesignSystem/Form/Checkbox";
import Button from "../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../js/components/XgrowDesignSystem/Form/Input";

import StatusModalComponent from "../../js/components/StatusModalComponent";
import { cpf, cnpj } from "cpf-cnpj-validator";
import Modal from "../../js/components/ModalComponent.vue";

import "../../../public/xgrow-vendor/assets/js/toast-config.js"

export default {
  name: "AffiliatePlatforms",
  components: {
    Checkbox,
    Button,
    Input,
    StatusModalComponent,
    Modal,
  },
  props: {},
  data() {
    return {
      loading: false,
      status: "loading",
      activeTab: "registerType",
      registerType: "CNPJ",
      confirmModal: false,
      user: {
        name: "",
        email: "",
        type: "J",
        identity: "",
      },
    };
  },
  methods: {
    setTab(tab) {
      this.activeTab = tab;
    },
    selectRegisterType(type) {
      this.registerType = type;
      this.user.type = type === "CNPJ" ? "J" : "F";
    },
    nextStep() {
      this.setTab("identifyData");
    },
    backStep() {
      this.setTab("registerType");
    },
    async getUserInfo() {
      const res = await axios.get(userURL);
      if (res.status === 200) {
        this.user.name = res.data.response.name;
        this.user.email = res.data.response.email;
      } else {
        errorToast(
          "Falha ao realizar ação",
          "Erro ao carregar dados do usuário."
        );
      }
    },
    async save() {
      this.confirmModal = false;
      if (this.verifyFields()) return true;
      this.loading = true;
      try {
        const formData = new FormData();

        const name = this.user.name.split(/\s+/);
        formData.append("first_name", name.shift());
        formData.append("last_name", name.join(" "));
        formData.append(
          "document",
          this.user.identity
            .replaceAll(".", "")
            .replaceAll("-", "")
            .replaceAll("/", "")
        );
        formData.append("type_person", this.user.type);
        formData.append(
          "_token",
          document.getElementsByName("csrf-token")[0].content
        );

        const res = await axios.post(clientURL, formData);
        this.status = "creatingPlatform";
        this.activeScreen = "";
        await new Promise((resolve) => setTimeout(resolve, 5000));
        successToast("Ação realizada com sucesso", res.data.message.toString());
        this.loading = false;
        this.status = "loading";
        window.location.href = "/platforms";
        return true;
      } catch (e) {
        this.loading = false;
        this.status = "loading";
        errorToast(
          "Falha ao realizar ação",
          e.response.data.message.toString()
        );
        if (e.response.status >= 500) {
          errorToast("Falha ao realizar ação", e.response.statusText);
        } else {
          const error = e.response.data.errors;
          this.isKeyExists(error, "first_name");
          this.isKeyExists(error, "last_name");
          this.isKeyExists(error, "type_person");
          this.isKeyExists(error, "document");
        }
      }
    },
    openConfirmModal() {
      if (this.verifyFields()) return true;
      this.confirmModal = true;
    },
    verifyFields() {
      if (
        this.user.type === "" ||
        (this.user.type === "F" && this.user.type === "J")
      ) {
        errorToast(
          "Algum erro aconteceu!",
          "Você deve selecionar um tipo de conta."
        );
        return true;
      }
      if (this.user.name.trim() === "") {
        errorToast("Algum erro aconteceu!", "O nome é obrigatório.");
        return true;
      }
      if (this.user.name.trim() !== "") {
        let name = this.user.name.trim();
        name = name.split(" ", 2);
        if (name.length < 2) {
          errorToast(
            "Algum erro aconteceu!",
            "O nome está incompleto. Verifique."
          );
          return true;
        }
      }
      if (this.user.email.trim() === "") {
        errorToast("Algum erro aconteceu!", "O e-mail é obrigatório.");
        return true;
      }
      if (!this.emailRegex(this.user.email)) {
        errorToast("Algum erro aconteceu!", "O e-mail informado é inválido.");
        return true;
      }
      if (this.user.identity.trim() === "") {
        const type = this.user.type === "F" ? "CPF" : "CNPJ";
        errorToast("Algum erro aconteceu!", `O ${type} é obrigatório.`);
        return true;
      }
      if (this.user.type === "F" && !cpf.isValid(this.user.identity)) {
        errorToast("Algum erro aconteceu!", `O CPF informado é inválido.`);
        return true;
      }
      if (this.user.type === "J" && !cnpj.isValid(this.user.identity)) {
        errorToast("Algum erro aconteceu!", `O CNPJ informado é inválido.`);
        return true;
      }
    },
    isKeyExists(obj, key) {
      if (obj.hasOwnProperty(key)) {
        errorToast("Falha ao realizar ação", obj[key][0].toString());
      }
      return false;
    },
    emailRegex(val) {
        return String(val)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
    },
  },
  async mounted() {
    await this.getUserInfo();
  },
};
</script>

<style lang="scss" scoped>
.xgrow-inner-card {
  background-color: #252932;
  padding: 2rem;
  border-radius: 8px;
  margin: 2rem;
}

.alert-warning {
  border: none;
  border-radius: 3px;
  margin: 2rem;
  padding: 6px 16px;
  color: #ffb200;
  border-left: 4px solid #ffb200;
  background-color: #3d3736;
}

.alert-text {
  color: #ffb200;
}

.alert-warning > i::before {
  background: rgba(0, 0, 0, 0.1);
  padding: 5px;
  border-radius: 8px;
}

.mui-textfield > input ~ label {
  top: -18px;
  font-size: 16px;
}

.mui-textfield > input::placeholder {
  color: #c1c5cf;
  position: absolute;
  top: 26px;
}

input:focus::-webkit-input-placeholder {
  opacity: 0;
}
</style>
