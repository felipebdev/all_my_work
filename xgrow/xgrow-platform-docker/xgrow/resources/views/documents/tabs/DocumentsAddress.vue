<template>
  <div class="xgrow-card card-dark py-4 material h-100">
    <Title>Endereço</Title>
    <Subtitle
      >Estes dados serão utilizados para sua identificação dentro da
      plataforma.</Subtitle
    >

    <Row>
      <Col md="4" lg="4" xl="4">
        <Input
          id="cep"
          label="CEP"
          placeholder="Insira o CEP do endereço..."
          v-model="form.zipcode"
          :disabled="!edit && $store.state.edit === true"
          @input="CheckCEP"
          :mask="'#####-###'"
        />
      </Col>
      <Col md="4" lg="4" xl="4">
        <Select
          id="state"
          label="Estado"
          placeholder="Selecione uma opção"
          :options="states"
          v-model="form.state"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
      <Col md="4" lg="4" xl="4">
        <Input
          id="city"
          label="Cidade"
          placeholder="Insira o nome da cidade..."
          :maxlength="40"
          v-model="form.city"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
      <Col md="8" lg="8" xl="8">
        <Input
          id="address"
          label="Logradouro"
          placeholder="Insira o logradouro do endereço..."
          :maxlength="60"
          v-model="form.address"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
      <Col md="4" lg="4" xl="4">
        <Input
          id="number"
          label="Número"
          placeholder="0"
          :maxlength="10"
          v-model="form.number"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
      <Col md="6" lg="6" xl="6">
        <Input
          id="district"
          label="Bairro"
          placeholder="Insira o bairro do endereço..."
          :maxlength="30"
          v-model="form.district"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
      <Col md="6" lg="6" xl="6">
        <Input
          id="complement"
          label="Complemento (opcional)"
          placeholder="Insira o complemento do endereço..."
          :maxlength="20"
          v-model="form.complement"
          :disabled="!edit && $store.state.edit === true"
        />
      </Col>
    </Row>

    <Row class="button-section">
      <div
        class="button-section__buttons"
        :class="{ 'button-section__buttons--spaced': edit }"
      >
        <Button
          style="background: none; border-color: #fff !important; width: 200px"
          v-if="edit"
          text="Voltar"
          :outline="true"
          status="dark"
          @click="edit = false"
        />
        <Button
          v-if="!edit && $store.state.edit === true"
          text="Editar"
          status="success"
          icon="fas fa-pen"
          @click="edit = true"
          style="width: 200px"
        />
        <Button
          v-if="
            (!edit && $store.state.edit === false) ||
            (edit && $store.state.edit === true)
          "
          style="width: 200px"
          text="Salvar e prosseguir"
          status="success"
          @click="saveAddress"
        />
      </div>
    </Row>
    <StatusModalComponent :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";

import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";

import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";

import StatusModalComponent from "../../../js/components/StatusModalComponent";
import axios from "axios";
import moment from "moment";

export default {
  name: "Address",
  components: {
    StatusModalComponent,
    Title,
    Subtitle,
    Row,
    Col,
    Button,
    Input,
    Select,
  },
  props: {
    env: { required: false },
  },
  data() {
    return {
      loading: false,
      edit: false,
      form: {
        address: "",
        city: "",
        complement: "",
        district: "",
        number: "",
        state: "",
        zipcode: "",
      },
      states: [
        { name: "Acre", value: "AC" },
        { name: "Alagoas", value: "AL" },
        { name: "Amapá", value: "AP" },
        { name: "Amazonas", value: "AM" },
        { name: "Bahia", value: "BA" },
        { name: "Ceará", value: "CE" },
        { name: "Distrito Federal", value: "DF" },
        { name: "Espírito Santo", value: "ES" },
        { name: "Goiás", value: "GO" },
        { name: "Maranhão", value: "MA" },
        { name: "Mato Grosso", value: "MT" },
        { name: "Mato Grosso do Sul", value: "MS" },
        { name: "Minas Gerais", value: "MG" },
        { name: "Pará", value: "PA" },
        { name: "Paraíba", value: "PB" },
        { name: "Paraná", value: "PR" },
        { name: "Pernambuco", value: "PE" },
        { name: "Piauí", value: "PI" },
        { name: "Rio de Janeiro", value: "RJ" },
        { name: "Rio Grande do Norte", value: "RN" },
        { name: "Rio Grande do Sul", value: "RS" },
        { name: "Rondônia", value: "RO" },
        { name: "Roraima", value: "RR" },
        { name: "Santa Catarina", value: "SC" },
        { name: "São Paulo", value: "SP" },
        { name: "Sergipe", value: "SE" },
        { name: "Tocantins", value: "TO" },
      ],
    };
  },
  methods: {
    async getInfo() {
      this.loading = true;
      await axios
        .get(getAddressUrl)
        .then(({ data }) => {
          this.form.address = data.data.address;
          this.form.city = data.data.city;
          this.form.complement = data.data.complement;
          this.form.district = data.data.district;
          this.form.number = data.data.number;
          this.form.state = data.data.state;
          this.form.zipcode = data.data.zipcode;
        })
        .catch((e) => {
          console.log(e);
        });

      this.loading = false;
    },
    async saveAddress() {
      this.loading = true;
      try {
        await axios.put(updateAddressUrl, this.form);
        successToast("Sucesso!", "Endereço atualizado com sucesso!");
        if (this.$store.state.edit === false) {
          this.$store.commit("setTab", "rates-and-terms");
        }
      } catch (e) {
        errorToast(
          "Algo deu errado!",
          "Algo deu errado ao tentar alterar o seu endereço."
        );
        console.log(e);
      }
      this.loading = false;
    },
    async CheckCEP() {
      const cep = this.form.zipcode;

      if (cep.length != 9) return;

      this.loading = true;

      //https://viacep.com.br/ - api
      await axios
        .get(`https://viacep.com.br/ws/${cep}/json/`)
        .then(({ data }) => {
          this.form.address = data.logradouro;
          this.form.city = data.localidade;
          this.form.complement = data.complemento;
          this.form.district = data.bairro;
          this.form.state = this.states.filter(
            (state) => state.value == data.uf
          )[0].value;
          this.form.zipcode = data.cep;
        })
        .catch((error) => console.log(error));

      this.loading = false;
    },
  },
  async mounted() {
    await this.getInfo();
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
</style>
