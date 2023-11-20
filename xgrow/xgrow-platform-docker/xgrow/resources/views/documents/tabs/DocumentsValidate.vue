<template>
  <div class="xgrow-card card-dark py-4 mb-2">
    <h4 style="color: #fff">Verifique a sua identidade</h4>
    <!-- <p class="mb-5">
      Aprenda um pouco mais sobre a
      <a
        href="https://ajuda.xgrow.com/pt-br/article/que-documentos-sao-necessarios-para-eu-me-cadastrar-pwwget/1"
        target="_blank"
        >verificação de identidade.</a
      >
    </p> -->

    <div
      class="danger alert-danger d-flex align-items-center gap-2"
      role="danger"
      v-if="$store.state.hasErrors === true"
    >
      <img
        src="/xgrow-vendor/assets/img/documents/danger.svg"
        alt="Cadastro finalizado"
      />
      <div>
        <p class="text-danger" style="font-weight: 600">Não conseguimos verificar sua identidade:</p>
        <p class="text-danger" v-if="$store.state.errorMessage" v-html="$store.state.errorMessage"></p>
      </div>
    </div>

    <h5>Upload de documento (com foto)</h5>
    <hr />
    <p class="mb-3">
      Este documento será utilizado para confirmar a veracidade de seus dados
      informados.
    </p>
    <h6 class="mb-3">Dados de identificação</h6>
    <h6 class="mb-3">Para onde devemos enviar os seus saques?</h6>
    <Row>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-2">
        <RadioButton
          id="cnpj"
          :checked="form.documentType === 'cnpj'"
          name="documentType"
          label="Quero receber na minha empresa (CNPJ)"
          v-model="form.documentType"
          option="cnpj"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-2">
        <RadioButton
          id="cpf"
          :checked="form.documentType === 'cpf'"
          name="documentType"
          label="Quero receber na minha conta de pessoa física (CPF)"
          v-model="form.documentType"
          option="cpf"
        />
      </Col>
    </Row>
    <Row>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="name"
          :label="
            form.documentType === 'cpf' ? 'Nome completo' : 'Razão social'
          "
          v-model="form.fullname"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="doc"
          :label="form.documentType === 'cpf' ? 'CPF' : 'CNPJ'"
          :mask="
            form.documentType === 'cpf'
              ? '###.###.###-##'
              : '##.###.###/####-##'
          "
          v-model="form.documentNumber"
        />
      </Col>
    </Row>
    <h6>Escolha o documento a ser enviado</h6>
    <Row>
      <Col sm="12" md="12" lg="12" xl="12" class="mb-4">
        <Select
          id="doc"
          :options="documentTypes"
          v-model="form.documentToValidate"
          label="Tipo de documento"
        />
      </Col>
    </Row>
    <h6>Foto do documento</h6>
    <Row>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <UploadDropzoneImage
          ref="front"
          refer="front"
          side=""
          btn-title="Buscar arquivo"
          @send-image="receiveImage"
          @clear="form.document = null"
        >
        </UploadDropzoneImage>
      </Col>
    </Row>
    <Row class="button-section">
      <div class="button-section__buttons">
        <Button text="Avançar" status="success" @click.prevent="validateForm" />
      </div>
    </Row>
  </div>
</template>

<script>
import axios from "axios";
import img from "../../../../public/xgrow-vendor/assets/img/documents/documents.svg";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import RadioButton from "../../../js/components/XgrowDesignSystem/Form/RadioButton.vue";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select";
import UploadDropzoneImage from "../../../js/components/UploadDropzoneImage";
import maska from "maska";
import { cnpj, cpf } from "cpf-cnpj-validator";

export default {
  name: "ValidateDocuments",
  components: {
    Row,
    Col,
    Input,
    RadioButton,
    Button,
    Select,
    UploadDropzoneImage,
  },
  props: {
    hasErrors: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      img,
      loading: false,
      form: {
        documentType: "cpf",
        documentFile: null,
        documentNumber: "",
        fullname: "",
        documentToValidate: "",
      },
      documentTypes: [
        { value: 1, name: "CNH" },
        { value: 2, name: "RG" },
        { value: 2, name: "Novo RG" },
        { value: 4, name: "CPF" },
      ],
    };
  },
  directives: { maska },
  watch: {
    "form.documentType": function () {
      this.form.documentNumber = "";
    },
  },
  methods: {
    receiveImage(obj) {
      this.form.documentFile = obj.file.files[0];
    },
    setDocument(doc, remove) {
      document.getElementById(remove).checked = false;
      document.getElementById(doc).checked = true;
      this.form.documentType = doc;
    },
    setTab(value) {
      this.$store.commit("setTab", value);
    },
    cleanForm() {
      this.form.name = "";
      this.form.documentNumber = "";
    },
    async getInfo() {
      await axios
        .get(getIdentity)
        .then(({ data }) => {
          const identity = data.data;
          this.form.fullname = `${identity.first_name} ${identity.last_name}`;
          this.form.documentType =
            identity.type_person === "J" ? "cnpj" : "cpf";
          this.form.documentNumber =
            identity.type_person === "J"
              ? this.formatDocument(identity.cnpj)
              : this.formatDocument(identity.cpf);
        })
        .catch((e) => console.log(e));
    },
    validateForm() {
      if (!this.form.documentFile) {
        errorToast(
          "Erro no envio dos documentos!",
          "Você precisa enviar ao menos uma foto do documento"
        );
        return;
      }

      if (
        !this.form.documentToValidate ||
        this.form.documentToValidate === "0"
      ) {
        errorToast(
          "Erro no envio dos documentos!",
          `Selecione o tipo de documento`
        );

        return;
      }

      if (
        this.form.documentType !== "cpf" &&
        this.form.documentType !== "cnpj"
      ) {
        errorToast(
          "Erro no envio dos documentos!",
          "Você precisa selecionar ao menos um tipo de documento"
        );

        return;
      }

      if (
        (this.form.documentType === "cpf" &&
          !cpf.isValid(this.form.documentNumber)) ||
        (this.form.documentType === "cnpj" &&
          !cnpj.isValid(this.form.documentNumber))
      ) {
        errorToast(
          "Erro no envio dos documentos!",
          "Preencha o número do documento corretamente!"
        );

        return;
      }

      if (!this.form.fullname) {
        errorToast(
          "Erro no envio dos documentos!",
          `Digite ${
            this.form.documentType === "cpf" ? "o nome" : "a razão social"
          } para prosseguir!`
        );

        return;
      }

      const splittedName = this.form.fullname.trim().split(' ')
      const cleanSplittedName = splittedName.filter((item) => item.trim() !== '')

      if (cleanSplittedName.length <= 1) {
        return errorToast(
          "Erro no envio dos documentos!",
          `Digite ao menos um sobrenome para prosseguir!`
        );
      }

      this.$store.commit("setIdentity", {
        name: this.form.fullname,
        document: this.form.documentNumber,
      });
      this.$store.commit("setDocumentType", this.form.documentType);
      this.$emit("payload", this.form);
      this.setTab("bank-data");
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
  },
  async mounted() {
    await this.getInfo();
  },
};
</script>

<style lang="scss" scoped>
p,
h5,
h6 {
  color: #fff;
}

hr {
  color: #c4c4c4;
  background-color: #c4c4c4;
}

a {
  color: var(--green1);
}

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

.alert-danger {
  border: none;
  border-radius: 3px;
  padding: 16px;
  color: #f1a6a6;
  border-left: 4px solid #f96c6c;
  background-color: #382b30;
  align-items: flex-start !important;
  margin-bottom: 20px;
}

.text-danger {
  color: #f1a6a6;

  .error-list {
    & > li {
      list-style: disc !important;
    }
  }
}

.danger-warning > i::before {
  background: rgba(0, 0, 0, 0.1);
  padding: 5px;
  border-radius: 8px;
}
</style>
