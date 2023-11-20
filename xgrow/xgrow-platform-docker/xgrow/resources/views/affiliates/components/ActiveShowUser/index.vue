<template>
  <Modal
    :is-open="isOpen"
    modalSize="lg"
    @close="isOpen = false"
  >
    <h5>
      Dados do afiliado: <span>{{ user.name }}</span>
    </h5>
    <hr>
    <h6 v-if="showAffiliationDetails">Detalhes da afiliação</h6>
    <Row v-if="showAffiliationDetails">
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="status"
          label="Status da afiliação"
          :readonly="true"
          v-model="status"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="date"
          label="Data da afiliação"
          :readonly="true"
          v-model="affiliationDate"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="product_name"
          label="Produto afiliado"
          :readonly="true"
          v-model="user.product_name"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="percent"
          label="Comissão"
          :readonly="true"
          v-model="commission"
        />
      </Col>
    </Row>

    <h6>Dados pessoais</h6>
    <Row>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="name"
          label="Nome completo"
          :readonly="true"
          v-model="user.name"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="email"
          label="E-mail"
          :readonly="true"
          v-model="user.email"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="phone_number"
          label="Telefone"
          :readonly="true"
          v-model="user.phone_number"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="cpf"
          label="CPF/CNPJ"
          :readonly="true"
          v-model="affiliateDocument"
        />
      </Col>
    </Row>

    <h6>Endereço</h6>
    <Row>
      <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
        <Input
          id="zipcode"
          label="CEP"
          :readonly="true"
          v-model="user.zipcode"
        />
      </Col>
      <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
        <Input
          id="state"
          label="Estado"
          :readonly="true"
          v-model="user.state"
        />
      </Col>
      <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
        <Input
          id="city"
          label="Cidade"
          :readonly="true"
          v-model="user.city"
        />
      </Col>
      <Col sm="12" md="8" lg="8" xl="8" class="mb-4">
        <Input
          id="street"
          label="Logradouro"
          :readonly="true"
          v-model="user.address"
        />
      </Col>
      <Col sm="12" md="4" lg="4" xl="4" class="mb-4">
        <Input
          id="number"
          label="Número"
          :readonly="true"
          v-model="user.number"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="district"
          label="Bairro"
          :readonly="true"
          v-model="user.district"
        />
      </Col>
      <Col sm="12" md="6" lg="6" xl="6" class="mb-4">
        <Input
          id="complement"
          label="Complemento"
          :readonly="true"
          v-model="user.complement"
        />
      </Col>
    </Row>
    <button class="xgrow-button" style="float:right" @click="isOpen = false">Voltar</button>
  </Modal>
</template>

<script>
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Modal from "../../../../js/components/XgrowDesignSystem/Modals/Modal.vue";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../../js/components/XgrowDesignSystem/Utils/Col";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";

import moment from "moment"

export default {
  name: "AffiliatesActiveShowUserModal",
  components: {
    Modal,
    Row,
    Col,
    Input,
    Subtitle,
  },
  props: {
    isOpen: {
      type: Boolean,
      default: false,
    },
    showAffiliationDetails: {
      type: Boolean,
      default: true
    },
    user: {
      type: Object,
      default: () => {
        return {
          created_at: "",
          product_name: "",
          percent: "",

          name: "",
          email: "",
          phone_number: "",
          cpf: "",

          zipcode: "",
          state: "",
          city: "",
          address: "",
          number: "",
          district: "",
          complement: "",
        }
      }
    }
  },
  computed: {
    commission() {
      return `${String(this.user.percent).replace('.',',')}%`;
    },
    affiliationDate() {
      return moment(this.user.created_at).format("DD/MM/YYYY");
    },
    affiliateDocument() {
      return this.user.cpf || this.user.cnpj
    }
  },
  data() {
    return {
      isOpenData: this.isOpen,
      status: "Ativa",
    }
  },
  methods: {
  }
};
</script>

<style lang="scss" src="./styles.scss" scoped></style>
<style lang="scss">
.modal__content {
  padding: 30px;
}

.form-group {
  margin: 0px !important;
}
</style>
