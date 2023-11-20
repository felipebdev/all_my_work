<template>
  <div class="xgrow-card card-dark py-4 material h-100">
    <Title>Dados bancários</Title>
    <Subtitle
      >Esta conta bancária será utilizada para depositar seus lucros.</Subtitle
    >

    <div
      class="alert alert-warning d-flex align-items-center gap-2"
      role="alert"
      v-if="$store.state.edit == false"
    >
      <img
        src="/xgrow-vendor/assets/img/documents/warning.svg"
        alt="Cadastro finalizado"
      />
      <div>
        <p class="text-warning" style="font-weight: 600">Atenção!</p>
        <p class="text-warning">
          A conta bancária deve ser de sua titularidade.
        </p>
      </div>
    </div>

    <Row>
      <Col sm="12" md="12" lg="12" xl="12">
        <div class="xgrow-inner-card">
          <Subtitle :is-small="true" icon="fas fa-user" icon-color="#3D4353">
            Dados de identificação
          </Subtitle>
          <div class="d-flex" v-if="$store.state.documentType">
            <p class="text-white">
              <span style="font-weight: 600"
                >{{
                  $store.state.documentType == "cnpj" ? "Razão social" : "Nome"
                }}:
              </span>
              {{ $store.state.identity.name }}
            </p>
            <span class="pipe">|</span>
            <p class="text-white">
              <span style="font-weight: 600"
                >{{ $store.state.documentType == "cnpj" ? "CNPJ" : "CPF" }}:
              </span>
              {{ $store.state.identity.document }}
            </p>
          </div>
        </div>
      </Col>
    </Row>

    <Row>
      <Subtitle :isSmall="true">Dados bancários</Subtitle>
    </Row>
    <Row>
      <Col sm="12" md="6" lg="6" xl="6">
        <Input
          id="name"
          :label="
            $store.state.documentType === 'cnpj'
              ? 'Razão Social'
              : 'Nome do titular'
          "
          v-model="form.name"
          :pattern="{
            mask: 'HHHHHHHHHHHHHHHHHHHHHHHHHHHHHH',
            tokens: {
              H: { pattern: /[a-zA-ZáàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ'0-9\s]/ },
            },
          }"
          :disabled="$store.state.edit && !edit"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6">
        <Input
          id="document"
          :label="
            $store.state.documentType === 'cnpj'
              ? 'CNPJ do titular'
              : 'CPF do titular'
          "
          v-model="form.document"
          :mask="
            $store.state.documentType === 'cpf'
              ? '###.###.###-##'
              : '##.###.###/####-##'
          "
          :disabled="$store.state.edit && !edit"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6">
        <Select
          id="bank"
          label="Tipo de conta"
          placeholder="Selecione uma opção"
          :options="accountTypeList"
          v-model="form.account_type"
          :disabled="$store.state.edit && !edit"
        />
      </Col>
      <Col sem="12" md="6" lg="6" xl="6">
        <Select
          id="bank"
          label="Banco"
          placeholder="Selecione uma opção"
          :options="bankList"
          v-model="form.bank"
          @change="cleanBankInfo"
          :disabled="$store.state.edit && !edit"
        />
      </Col>
      <Col sm="8" md="4" lg="4" xl="4">
        <Input
          id="bank_agency"
          label="Agência"
          v-model="form.bank_agency"
          :disabled="$store.state.edit && !edit"
          mask="####"
        />
      </Col>
      <Col sm="4" md="2" lg="2" xl="2">
        <Input
          id="bank_agency_digit"
          label="Dígito"
          v-model="form.bank_agency_digit"
          :disabled="$store.state.edit && !edit"
          mask="#"
        />
      </Col>
      <Col sm="8" md="4" lg="4" xl="4">
        <Input
          id="account_number"
          label="Conta"
          v-model="form.account_number"
          :disabled="$store.state.edit && !edit"
          mask="#############"
        />
      </Col>
      <Col sm="4" md="2" lg="2" xl="2">
        <Input
          id="account_digit"
          label="Dígito"
          v-model="form.account_digit"
          :disabled="$store.state.edit && !edit"
          mask="##"
        />
      </Col>
    </Row>

    <Row class="button-section">
      <div
        class="button-section__buttons"
        :class="{
          'button-section__buttons--spaced': edit || $store.state.edit == false,
        }"
      >
        <Button
          v-if="$store.state.edit == false"
          text="Voltar"
          :outline="true"
          status="dark"
          :on-click="backStep"
        />
        <Button
          v-if="$store.state.edit == false"
          text="Enviar"
          status="success"
          icon="fas fa-check"
          :on-click="validateForm"
        />

        <Button
          v-if="edit"
          text="Voltar"
          :outline="true"
          status="dark"
          :on-click="() => (edit = false)"
        />
        <Button
          v-if="!edit && $store.state.edit == true"
          text="Editar"
          status="success"
          icon="fas fa-pen"
          :on-click="toggleEditMode"
        />
        <Button
          v-if="edit"
          text="Salvar"
          status="success"
          :on-click="validateForm"
        />
      </div>
    </Row>
    <StatusModalComponent :is-open="loading" :status="loadingStatus" />
    <ModalEditBank
      :email="form.email"
      :is-open="modalOpen"
      @resend="twoFactorToEdit"
      @close="closeModal"
      @timer="checkTimerIsActive"
      @confirm="verifyToken"
    />
  </div>
</template>

<script>
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";

import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";

import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";

import StatusModalComponent from "../../../js/components/StatusModalComponent";

import ModalEditBank from "../modals/DocumentsEditBankData.vue";

import maska from "maska";
import axios from "axios";
import moment from "moment";
import { cnpj, cpf } from "cpf-cnpj-validator";

export default {
  name: "BankData",
  components: {
    StatusModalComponent,
    Title,
    Subtitle,
    Row,
    Col,
    Button,
    Input,
    Select,
    ModalEditBank,
  },
  props: {
    payload: { type: Object, default: () => {} },
  },
  directives: { maska },
  data() {
    return {
      loading: false,
      loadingStatus: "loading",
      form: {
        name: "",
        document: "",
        account_type: "",
        account_digit: "",
        account_number: "",
        bank: "",
        bank_agency: "",
        bank_agency_digit: "",
        email: "",
        loaded: false
      },
      timerIsActive: false,
      accountTypeList: [
        { value: "checking", name: "Conta Corrente" },
        { value: "savings", name: "Conta Poupança" },
      ],
      bankList: [],
      edit: false,
      modalOpen: false,
    };
  },
  methods: {
    cleanBankInfo() {
      if(this.form.loaded || this.$store.state.edit == false) {
        this.form.bank_agency = ''
        this.form.bank_agency_digit = ''
        this.form.account_digit = ""
        this.form.account_number = ""
      }
    },
    async verifyToken(payload) {
      const params = {
        two_factor_code: payload,
      };

      axios
        .get(verifyToken, { params })
        .then(({ data }) => {
          successToast("Sucesso", data.data.message);
          this.$store.commit("setTokenCode", payload);
          this.modalOpen = false;
          this.edit = true;
        })
        .catch((error) => {
          errorToast("Erro", "Token Invalido!");
        });
    },
    async updateDataBank() {
      this.loading = true;
      this.loadingStatus = "saving";

      const document_number = this.sanitizeDocument(this.form.document);
      const params = {
        two_factor_code: this.$store.state.two_factor_code,
        bank_code: this.form.bank,
        agency: this.form.bank_agency,
        agency_digit: this.form.bank_agency_digit,
        account: this.form.account_number,
        account_type: this.form.account_type,
        account_digit: this.form.account_digit,
        document_number: document_number,
        document_type: this.form.documentType,
        legal_name: this.form.name,
      };

      try {
        const res = await axios.put(updateDataBank, params);
        const { data } = res.data;
        console.log(data);
        if (data.errors) {
          this.$store.commit("setErrorAlert", true);
          this.$store.commit("setTab", "validate");
        } else {
          successToast("Sucesso!", "Conta bancária atualizada com sucesso!");
        }
      } catch {
        errorToast(
          "Erro",
          "Ocorreu um erro ao tentar atualizar os dados bancários, tente novamente"
        );
      }

      this.loading = false;
      this.loadingStatus = "loading";
    },
    setTab(value) {
      this.$store.commit("setTab", value);
    },
    backStep() {
      this.setTab("validate");
    },
    nextStep() {
      this.setTab("address");
    },
    async getIdentity() {
      await axios
        .get(getIdentity)
        .then(({ data }) => {
          const identity = data.data;
          let newIdentity = {
            name:
              identity.type_person === "J"
                ? identity.company_name
                : `${identity.first_name} ${identity.last_name}`,
            document:
              identity.type_person === "J"
                ? this.formatDocument(identity.cnpj)
                : this.formatDocument(identity.cpf),
          };

          this.$store.commit(
            "setDocumentType",
            identity.type_person === "J" ? "cnpj" : "cpf"
          );
          this.$store.commit("setIdentity", newIdentity);
        })
        .catch((e) => console.log(e));
    },
    async getInfo() {
      this.loading = true;

      if (this.$store.state.edit == false) {
        this.loading = false;
        return;
      }

      await axios
        .get(getBankDetails)
        .then(({ data }) => {
          this.form.name = data.data.legal_name;
          this.form.document = this.formatDocument(data.data.document_number);
          this.form.account_type = data.data.account_type;
          this.form.account_digit = data.data.account_digit;
          this.form.account_number = data.data.account;
          this.form.bank = data.data.bank_code;
          this.form.email = data.data.email;
          this.form.bank_agency = data.data.agency;
          this.form.bank_agency_digit = data.data.agency_digit;
          this.form.documentType = data.data.document_type;
        })
        .catch((e) => {
          errorToast("Erro", e.message);
        });
      this.form.loaded = true;
      this.loading = false;
    },
    async getBankList() {
      await axios
        .get(getBankListURL)
        .then(({ data }) => {
          this.bankList = data.map(({ code, bank }) => {
            return { value: code, name: `${code} - ${bank}` };
          });
        })
        .catch((e) => console.log(e));
    },
    async twoFactorToEdit() {
      if (this.timerIsActive) return;

      await axios
        .get(sendToken)
        .then(({ data }) => {
          successToast("Sucesso!", data.data.message);
        })
        .catch((r) => {
          errorToast(
            "Error",
            "Ocorreu um erro ao enviar o token, espere um pouco e tente novamente"
          );
        });
    },
    async validateForm() {
      if (!this.form.name) {
        errorToast(
          "Erro no envio dos documentos!",
          `Preencha corretamente o campo de ${
            this.$store.state.documentType === "cnpj" ? "razão social" : "nome"
          }`
        );
        return;
      }

      if (
        (this.$store.state.documentType === "cpf" &&
          !cpf.isValid(this.form.document)) ||
        (this.$store.state.documentType === "cnpj" &&
          !cnpj.isValid(this.form.document))
      ) {
        errorToast(
          "Erro no envio dos documentos!",
          "Preencha o número do documento corretamente!"
        );

        return;
      }

      const documentFromVuex = this.$store.state.identity.document;
      if (this.form.document != documentFromVuex) {
        errorToast(
          "Erro no envio dos documentos!",
          `O número do documento da conta bancária deve ser o mesmo dos dados de identificação`
        );
        return;
      }

      if (!this.form.account_type) {
        errorToast(
          "Erro no envio dos documentos!",
          `Selecione o tipo da conta bancária`
        );
        return;
      }

      if (!this.form.bank) {
        errorToast(
          "Erro no envio dos documentos!",
          `Selecionar o seu banco é obrigatório`
        );
        return;
      }

      if (!this.form.bank_agency) {
        errorToast(
          "Erro no envio dos documentos!",
          `O campo de agência é obrigatório`
        );
        return;
      }

      if (!this.form.account_number) {
        errorToast(
          "Erro no envio dos documentos!",
          `Verifique o número da conta informada`
        );
        return;
      }

      if (!this.form.account_digit) {
        errorToast(
          "Erro no envio dos documentos!",
          `O digito verificador da conta é obrigatório`
        );
        return;
      }
      if (this.$store.state.edit === true) {
        await this.updateDataBank();
      } else {
        await this.save();
      }
    },
    async save() {
      this.loading = true;
      this.loadingStatus = "saving";
      this.form = { ...this.payload, ...this.form };
      const document_number = this.sanitizeDocument(this.form.documentNumber);
      const formData = new FormData();
      formData.append("file", this.form.documentFile);
      formData.append("bank_code", this.form.bank);
      formData.append("agency", this.form.bank_agency);
      formData.append("agency_digit", this.form.bank_agency_digit);
      formData.append("account", this.form.account_number);
      formData.append("account_type", this.form.account_type);
      formData.append("account_digit", this.form.account_digit);
      formData.append("document_number", document_number);
      formData.append("document_type", this.form.documentType);
      formData.append("legal_name", this.form.name);
      formData.append(
        "document",
        this.sanitizeDocument(this.$store.state.identity.document)
      );

      if (this.$store.state.documentType == "cnpj") {
        formData.append("type_person", "J");
        formData.append("company_name", this.$store.state.identity.name);
      } else {
        const divide = this.$store.state.identity.name.split(" ");
        formData.append("type_person", "F");
        formData.append("first_name", divide[0]);
        formData.append("last_name", divide[divide.length - 1]);
      }

      await axios.post(storeIdentity, formData).then(({data}) => {
        const { error, message } = data;
        if (error) {
          console.log(message);
          this.$store.commit("setErrorAlert", true);
          this.$store.commit("setTab", "validate");
        } else {
          this.$store.commit("setErrorAlert", false);
          successToast("Sucesso!", "Conta bancária cadastrada com sucesso!");
          this.$store.commit("setTab", "address");
        }
      })
      .catch(e => {
        const { message } = e.response.data
        console.log(message)
        this.$store.commit("setErrorMessage", message);
        this.$store.commit("setErrorAlert", true);
        this.$store.commit("setTab", "validate");
      })

      this.loading = false;
      this.loadingStatus = "loading";
    },
    sanitizeDocument(doc) {
      let dest = doc.replaceAll("-", "");
      dest = dest.replaceAll("/", "");
      dest = dest.replaceAll(".", "");

      return dest;
    },
    toggleEditMode() {
      this.modalOpen = true;
      this.twoFactorToEdit();
    },
    formatDocument: function (value) {
      const nonNumericChar = new RegExp(/\D+/g);

      if (nonNumericChar.test(value)) value = value.replace(nonNumericChar, "");

      if (String(value).length == 11)
        return String(value).replace(
          /(\d{3})(\d{3})(\d{3})(\d{2})/,
          "$1.$2.$3-$4"
        );

      if (String(value).length == 14)
        return String(value).replace(
          /(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/,
          "$1.$2.$3/$4-$5"
        );

      return value;
    },
    checkTimerIsActive(timer) {
      this.timerIsActive = timer > 0;
    },
    stopTimer() {
      this.timerIsActive = false;
    },
    closeModal() {
      this.modalOpen = false;
    },
  },
  async mounted() {
    await this.getBankList();
    await this.getInfo();
    await this.getIdentity();
  },
};
</script>


<style lang="scss" scoped>
.button-section {
  justify-content: flex-end;
  border-top: 1px solid rgba(white, 0.25);
  padding-top: 24px;

  &__buttons {
    display: flex;
    justify-content: flex-end;
    column-gap: 20px;

    &--spaced {
      justify-content: space-between;
    }
  }
}

.xgrow-inner-card {
  background-color: #252932;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 20px;
}

.pipe {
  display: block;
  font-weight: 700;
  color: #3d4353;
  margin: 0 10px;
}

.alert-warning {
  border: none;
  border-radius: 3px;
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
</style>
