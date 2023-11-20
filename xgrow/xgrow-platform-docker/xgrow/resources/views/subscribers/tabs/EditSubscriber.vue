<template>
  <div>
    <Title class="mb-5 mt-4">Dados do aluno</Title>

    <div class="row d-flex justify-content-between mb-5">
      <div class="col-md-7">
        <h6 class="mb-2">Dados pessoais</h6>
        <form id="user-form" @submit="updateData">
          <Col sm="12" md="12" lg="12" xl="12" class="my-4">
            <Input id="user_name" label="Nome" v-model="form.name" />
          </Col>
          <Col sm="12" md="12" lg="12" xl="12" class="my-4">
            <Input
              id="email"
              type="email"
              label="E-mail"
              placeholder="Preencha o e-mail do aluno..."
              v-model="form.email"
              required
            />
          </Col>
          <Col sm="12" md="12" lg="12" xl="12" class="my-4">
            <div class="countries">
              <img :src="flag" alt="" />
              <Input
                id="country_id"
                label="País"
                v-model="form.nacionality"
                disabled
                :class="flag ? 'selected' : ''"
              />
            </div>
          </Col>
          <Col sm="12" md="12" lg="12" xl="12" class="my-4">
            <Input
              id="cpf"
              :label="form.documentLabel"
              placeholder="000.000.000-00"
              v-model="form.document"
              disabled
            />
          </Col>
          <Col sm="12" md="12" lg="12" xl="12" class="my-4 mb-0">
            <div class="d-flex align-items-center gap-3">
              <Input
                class="w-100"
                id="cel"
                label="Celular"
                placeholder="(00) 000000-0000"
                :pattern="{
                  mask: '(HH) HHHHH-HHHH',
                  tokens: {
                    H: { pattern: /[\d+]/ },
                  },
                }"
                v-model="form.cel_phone"
              />
              <Button
                type="button"
                style="max-width: 300px; width: 100%; font-size: 14px"
                icon="fa-brands fa-whatsapp"
                text="Chamar no whatsapp"
                status="success"
                outline
                @click="callWhatsapp"
              />
            </div>
          </Col>
        </form>
      </div>
      <div class="col-md-4">
        <h6 class="mb-2">Detalhes da conta</h6>
        <div style="background-color: var(--ds2-primary-90)">
          <ul class="p-3" style="border-radius: 8px">
            <li class="mb-2">
              <span style="color: var(--ds2-primary-50)"
                ><i class="fa-regular fa-calendar"></i> CRIADO EM</span
              >
              <br />
              {{ formatDateTimeSingleLine(form.created_at, true) }}
            </li>
            <li>
              <span style="color: var(--ds2-primary-50)">
                <i class="fa-regular fa-clock"></i>
                ÚLTIMO ACESSO </span
              ><br />
              {{
                form.last_access === null
                  ? "Nunca acessou"
                  : formatDateTimeSingleLine(form.last_access, true)
              }}
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="mb-5">
      <h6 class="mb-2">Reenviar e-mail de boas vindas</h6>
      <p class="mb-3">
        Ao reenviar o e-mail de boas vindas, uma nova senha é gerada automaticamente.
      </p>
      <Button
        style="width: 288px; font-size: 14px"
        text="Reenviar e-mail de boas vidas"
        status="success"
        icon="fa-solid fa-envelope"
        outline
        @click="sendAccessData"
        type="button"
      />
    </div>
    <div class="mb-5">
      <h6 class="mb-2">Alterar senha</h6>
      <form autocomplete="off">
        <Col sm="12" md="7" lg="7" xl="7" class="my-4">
          <Switch
            id="manual"
            class="mb-3"
            :model-value="form.changePassword"
            v-on:update:model-value="
              (res) => {
                form.changePassword = res;

                return enableShowPassword(res);
              }
            "
          >
            Deseja alterar a senha?
          </Switch>
        </Col>
        <Col sm="12" md="7" lg="7" xl="7" class="my-4 position-relative">
          <Input
            id="password"
            :type="passwordType.first"
            label="Nova senha"
            autocomplete="new-password"
            placeholder="Insira a nova senha de acesso..."
            :disabled="!form.changePassword"
            v-model="form.raw_password"
          />
          <button type="button" @click="togglePassword('first')" class="show-button" :disabled="!form.changePassword">
            <i id="item-1" class="fa fa-eye show-password disabled"></i>
          </button>
        </Col>
        <Col sm="12" md="7" lg="7" xl="7" class="my-4 position-relative">
          <Input
            id="re-password"
            :type="passwordType.second"
            label="Repita a nova senha"
            placeholder="Repita a nova senha de acesso..."
            :disabled="!form.changePassword"
            v-model="form.raw_password_confirmation"
          />
          <button type="button" @click="togglePassword('second')" class="show-button" :disabled="!form.changePassword">
            <i id="item-2" class="fa fa-eye show-password disabled"></i>
          </button>
        </Col>
      </form>
    </div>
    <hr />
    <div class="d-flex justify-content-between">
      <Button
        style="width: 200px"
        text="Voltar"
        outline
        @click="$router.push({ name: 'subscribers-index' })"
      />
      <Button
        style="width: 200px"
        text="Salvar"
        status="success"
        form="user-form"
        type="submit"
      />
    </div>

    <Loading :is-open="loading" status="loading" />
  </div>
</template>

<script>
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Button from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Switch from "../../../js/components/XgrowDesignSystem/Form/SwitchButton";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import countriesJson from "../../../json/countries.json";
import Loading from "../../../js/components/StatusModalComponent";
import formatDateTimeDualLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import formatDateTimeSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeSingleLine";
import formatCnpjCpf from "../../../js/components/XgrowDesignSystem/Mixins/formatCpfCnpj.js";
import formatTryCatchError from "../../../js/components/XgrowDesignSystem/Mixins/formatTryCatchError.js";
import axios from "axios";

export default {
  components: {
    Loading,
    Title,
    Button,
    Switch,
    Input,
    Select,
    Col,
  },
  mixins: [formatCnpjCpf, formatDateTimeDualLine, formatDateTimeSingleLine],
  data() {
    return {
      loading: false,
      form: {
        name: "",
        email: "",
        nacionality: "País não selecionado",
        document: "",
        documentLabel: "",
        cel_phone: "",
        created_at: "",
        last_access: "",
        raw_password: "",
        raw_password_confirmation: "",
        changePassword: false,
      },
      flag: "",
      countries: [],
      passwordType: {
        first: 'password',
        second: 'password',
      },
      showPassword: {
        first: false,
        second: false,
      }
    };
  },
  methods: {
    togglePassword(pos) {
      this.showPassword[pos] = !this.showPassword[pos];
      this.passwordType[pos] = this.showPassword[pos] ? 'text' : 'password'
    },
    enableShowPassword(val) {
      if (val) {
        document.getElementById("item-1").classList.remove("disabled");
        document.getElementById("item-2").classList.remove("disabled");
        return;
      }

      document.getElementById("item-1").classList.add("disabled");
      document.getElementById("item-2").classList.add("disabled");
    },
    documentMask() {
      if (this.form.document.length < 14) {
        return "##.###.###-##";
      }

      return "##.###.###/####-##";
    },
    callWhatsapp() {
      if (this.form.name.trim().length < 3) {
        return errorToast("Atenção!", "Preencha o nome do aluno.");
      }

      if (this.form.cel_phone === null || this.form.cel_phone.length < 10) {
        return errorToast("Atenção!", "Digite um número de celular válido.");
      }

      let text = encodeURI(`Olá ${this.form.name}, tudo bem?`);

      return window
        .open(
          `https://api.whatsapp.com/send?phone=${this.form.cel_phone}&text=${text}`,
          "_blank"
        )
        .focus();
    },
    setCountry() {
      const country = this.countries.filter((item) => {
        if (this.form.nacionality.length === 3) {
          return item.alpha3 === this.form.nacionality;
        }

        return item.alpha2 === this.form.nacionality;
      })[0];

      this.flag = country.flag;
      this.form.nacionality = country.name;
      this.form.documentLabel = country.alpha3 === "BRA" ? "CPF / CNPJ" : "Documento";
    },
    async getData() {
      this.loading = true;
      const url = getUserDataURL.replace(":id", this.$route.params.id);

      try {
        const res = await axios.get(url);
        const user = res.data.response;

        this.form.name = user.name;
        this.form.email = user.email;
        this.form.document = this.formatCnpjCpf(user.document_number);
        this.form.cel_phone = user.cel_phone;
        this.form.created_at = user.created_at;
        this.form.last_access = user.login;
        this.loading = false;

        if (user.address_country) {
          this.form.nacionality = user.address_country;
          this.setCountry();
        }
      } catch (error) {
        errorToast("Atenção!", "Erro ao carregar dados, entre em contato com o suporte!");
        this.loading = false;
      }
    },
    async updateData(event) {
      event.preventDefault();

      if (this.form.name.trim().length < 3) {
        return errorToast("Atenção!", "O nome do aluno deve ter ao menos 3 caracteres.");
      }

      if (this.form.changePassword) {
        if (
          this.form.raw_password.trim().length === 0 ||
          this.form.raw_password_confirmation.trim().length === 0
        ) {
          return errorToast(
            "Atenção!",
            "Para alterar a senha, o campo de 'Nova senha' e 'Repita a nova senha' devem ser preenchidos e serem iguais."
          );
        }
      }

      this.loading = true;
      const url = updateUserDataURL.replace(":id", this.$route.params.id);

      try {
        const data = {
          name: this.form.name,
          email: this.form.email,
          cel_phone: this.form.cel_phone,
        };

        if (this.form.changePassword) {
          data.raw_password = this.form.raw_password;
          data.raw_password_confirmation = this.form.raw_password_confirmation;
        }

        await axios.patch(url, data);

        this.form.changePassword = false;
        this.form.raw_password = "";
        this.form.raw_password_confirmation = "";

        this.loading = false;
        successToast("Sucesso!", "Dados atualizados com sucesso!");
      } catch (error) {
        this.loading = false;
        errorToast("Atenção!", formatTryCatchError(error));
      }
    },
    async sendAccessData() {
      this.loading = true;
      const url = sendAccessDataURL.replace(":id", this.$route.params.id);

      try {
        const res = await axios.get(url);

        this.loading = false;
        successToast("Sucesso!", "Dados enviados com sucesso!");
      } catch (error) {
        this.loading = false;
        return errorToast("Atenção!", formatTryCatchError(error));
      }
    },
    getContries() {
      this.loading = true;
      this.countries = countriesJson.map((item, index) => {
        return {
          alpha2: item.alpha2,
          alpha3: item.alpha3,
          name: item.name_pt,
          flag: item.flag,
        };
      });
      this.loading = false;
    },
  },
  async mounted() {
    this.getContries();
    await this.getData();
  },
};
</script>

<style lang="scss">
.countries {
  position: relative;

  img {
    position: absolute;
    z-index: 1;
    top: 35px;
    left: 12.5px;
  }

  .selected {
    input {
      padding-left: 40px;
    }
  }
}

.show-button {
  color: white;
  background: none;
  border: none;
  top: 25px;
  right: 15px;
  position: absolute;
}

.show-password {
  cursor: pointer;
  opacity: 100%;
}

.show-password.disabled {
  opacity: 50%;
  cursor: not-allowed;
}
</style>
