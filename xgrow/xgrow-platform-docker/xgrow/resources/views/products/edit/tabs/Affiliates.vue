<template>
  <div>
    <div class="affiliates__header border-bottom">
      <div class="affiliates__title">
        <h4>Afiliados</h4>
        <p>Configure as ferramenta de afiliados como desejar.</p>
      </div>
      <div class="form-check form-switch affiliates__enable">
        <input
          type="checkbox"
          id="enable"
          class="form-check-input"
          @change="enableAffiliates(false)"
          :checked="isEnabled"
        />
        <label for="enable" class="form-check-label"
          >Habilitar programa de afiliados</label
        >
      </div>
    </div>
    <div
      :class="disabled ? 'affiliates__settings--disabled' : ''"
      class="affiliates__settings"
    >
      <form @submit.prevent="handleSubmit">
        <div class="affiliates__group">
          <h6 class="mb-3">Configurações dos afiliados</h6>
          <div class="affiliates__manual">
            <SwitchButton
              id="manual"
              class="mb-3"
              :disabled="disabled"
              :model-value="form.approve_request_manually"
              v-on:update:model-value="(res) => (form.approve_request_manually = res)"
            >
              Aprovar cada solicitação de afiliado manualmente
            </SwitchButton>

            <SwitchButton
              v-show="form.approve_request_manually"
              id="notifications"
              class="mb-3"
              :disabled="disabled"
              :model-value="form.receive_email_notifications"
              v-on:update:model-value="(res) => (form.receive_email_notifications = res)"
            >
              Receber notificações via e-mail
            </SwitchButton>
          </div>
          <SwitchButton
            id="allow"
            class="mb-3"
            :disabled="disabled"
            :model-value="form.buyers_data_access_allowed"
            v-on:update:model-value="(res) => (form.buyers_data_access_allowed = res)"
          >
            Liberar acesso aos dados dos compradores
          </SwitchButton>
        </div>
        <div class="affiliates__group">
          <h6 class="mb-3">Suporte para afiliados</h6>
          <Input
            class="w-100"
            type="text"
            id="email"
            label="E-mail de suporte"
            placeholder="Insira o e-mail de suporte"
            :disabled="disabled"
            :model-value="form.support_email"
            :maxlength="191"
            v-on:update:model-value="(res) => (form.support_email = res)"
          />
        </div>
        <div class="affiliates__group">
          <h6>Instruções para afiliados</h6>
          <p class="mb-3">
            Estas instruções servirão para o afiliado entender melhor como divulgar seu
            produto, você pode adicionar textos e links.
          </p>
          <textarea class="summernote" id="summernote" cols="30" rows="10" v-html="form.instructions" />
        </div>
        <div class="affiliates__group affiliates__double-field">
          <div class="w-100">
            <h6 class="mb-3">Comissão</h6>
            <Input
              class="w-100"
              label="Valor da comissão"
              type="text"
              id="value"
              info="O Valor da comissão deve ser no mínimo 1% e no máximo 80%"
              v-mask="['#%', '#,#%', '#,##%', '##,##%']"
              :disabled="disabled"
              v-on:update:model-value="checkPercent"
              :model-value="commissionUnformated"
            />
          </div>
          <div class="w-100">
            <h6 class="mb-3">Cookies</h6>
            <Select
              id="duration"
              label="Duração dos Cookies"
              :options="[
                /* {value: '0', name: 'Eterno'},
                            {value: '1', name: '1 dia'},
                            {value: '30', name: '30 dias'},
                            {value: '60', name: '60 dias'}, */
                { value: '90', name: '90 dias' },
                /* {value: '180', name: '180 dias'}, */
              ]"
              :disabled="disabled"
              v-on:update:model-value="(val) => (form.cookie_duration = val)"
              :model-value="form.cookie_duration"
            />
          </div>
        </div>
        <div class="affiliates__group affiliates__group--p border-bottom">
          <h6 class="mb-3">Atribuição</h6>
          <div class="affiliates__assignment d-flex gap-4">
            <div class="xgrow-radio d-flex align-items-center">
              <input
                type="radio"
                class="mr-2"
                name="assignment"
                id="last_click"
                :disabled="disabled"
                :checked="form.assignment === 'last_click'"
                @change="form.assignment = 'last_click'"
              />
              <label for="last_click">Último clique (recomendado)</label>
            </div>
            <!--
                    <div class="xgrow-radio d-flex align-items-center">
                        <input type="radio" class="mr-2" name="assignment" id="first_click" :disabled="disabled"
                            :checked="form.assignment === 'first_click'"
                            @change="form.assignment = 'first_click'"/>
                        <label for="first_click">Primeiro clique</label>
                    </div>
                -->
          </div>
        </div>
        <div class="affiliates__group affiliates__group--p border-bottom">
          <h6>Link de convite do afiliado</h6>
          <p class="mb-3">
            Compartilhe este link para convidar seus afiliados. Salve os dados anteriores
            para gerar um link.
          </p>
          <div class="position-relative" style="position: relative">
            <button
              class="affiliates__copy-link xgrow-button"
              type="button"
              @click="copyInviteLink"
              :disabled="disabled"
            >
              <i class="far fa-copy"></i><span>Copiar link</span>
            </button>
            <Input
              class="w-100"
              type="text"
              id="link"
              :readonly="true"
              :disabled="disabled"
              :model-value="form.invite_link"
            />
          </div>
        </div>
        <div class="affiliates__cta">
          <button
            type="submit"
            class="affiliates__button xgrow-button"
            :disabled="disabled"
          >
            Salvar
          </button>
        </div>
      </form>
    </div>
    <StatusModalComponent
      :is-open="isLoading"
      status="loading"
    ></StatusModalComponent>
  </div>
</template>

<script>
import axios from "axios";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Select from "../../../../js/components/XgrowDesignSystem/Form/Select";
import StatusModalComponent from "../../../../js/components/StatusModalComponent";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/Form/SwitchButton";

import {mask} from "vue-the-mask"

export default {
  components: {
    Input,
    Select,
    StatusModalComponent,
    SwitchButton
  },
  mixins: [],
  directives: {mask},
  data() {
    return {
      isLoading: false,
      disabled: !affiliation_enabled,
      isEnabled: affiliation_enabled,
      manual: false,
      commissionUnformated: "",
      form: {
        product_id: affiliateProductId,
        approve_request_manually: false,
        receive_email_notifications: false,
        buyers_data_access_allowed: false,
        support_email: "",
        instructions: "",

        commission: "",
        // cookie_duration: null,
        cookie_duration: "90",
        assignment: null,
        invite_link: "",
      },
    };
  },
  watch: {
    isEnabled(val) {
      this.changeVisualEditor(val);
    },
  },
  async mounted() {
    if (this.isEnabled) {
      await this.enableAffiliates(true);
    }
    $("#summernote").summernote({
      height: 300,
      minHeight: null,
      maxHeight: null,
      lang: "pt-BR",
    });
    this.changeVisualEditor(this.isEnabled);
  },
  methods: {
    changeVisualEditor(val) {
      if (val === true) {
        $(".note-editor").css("pointer-events", "inherit");
        $(".note-editor").css("background-color", "#252932");
        $(".note-toolbar").css("background-color", "#121419");
        $(".note-statusbar").css("background-color", "#121419");
      } else {
        $(".note-editor").css("pointer-events", "none");
        $(".note-editor").css("background-color", "#393D49");
        $(".note-toolbar").css("background-color", "#393D49");
        $(".note-statusbar").css("background-color", "#393D49");
        $(".note-editable").html("");
      }
    },
    async enableAffiliates(firstRender) {
      this.isLoading = true;

      if (!firstRender) {
        this.disabled = !this.disabled;
        this.isEnabled = !this.isEnabled;

        if (!this.isEnabled) {
          this.resetFields();
        }
      }

      const response = await axios.post(affiliateEnableURL, {
        affiliation_enabled: this.isEnabled,
        _method: "PUT",
      });

      const affiliationSettings = response.data.data.affiliation_settings;

      if (affiliationSettings !== null && this.isEnabled) {
        this.form.approve_request_manually = Boolean(
          affiliationSettings.approve_request_manually
        );
        this.form.receive_email_notifications = Boolean(
          affiliationSettings.receive_email_notifications
        );
        this.form.buyers_data_access_allowed = Boolean(
          affiliationSettings.buyers_data_access_allowed
        );
        this.form.support_email = affiliationSettings.support_email;
        this.form.instructions = affiliationSettings.instructions;
        $(".note-editable").html(affiliationSettings.instructions);
        this.form.commission = affiliationSettings.commission;
        this.commissionUnformated =
          affiliationSettings.commission.replace(".", ",") + "%";
        // this.form.cookie_duration = affiliationSettings.cookie_duration
        this.form.cookie_duration = "90";

        this.form.assignment = affiliationSettings.assignment;

        this.form.invite_link = `${window.location.origin}/affiliate-invite?invite=${affiliationSettings.invite_link}`;
      }

      this.isLoading = false;
    },
    validateFields() {
      let message = "";

      if (
        this.form.support_email === "" ||
        !/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(this.form.support_email)
      ) {
        message += 'Preencha o campo "E-mail de suporte" com um e-mail válido.\n';
      }

      if (
        this.form.commission === "" ||
        !this.checkPercent(this.commissionUnformated, false)
      ) {
        message += 'O "Valor da comissão" deve ser no mínimo 1% e no máximo 80%.\n';
      }

      if (this.form.cookie_duration === null) {
        message += 'Selecione à "Duração dos Cookies". \n';
      }

      if (this.form.assignment === null) {
        message += 'Selecione uma "Atribuição".';
      }

      if (message.length > 0) {
        errorToast("Atenção", message);
        return false;
      }

      return true;
    },
    async updateSettings() {
      this.isLoading = true;

      const response = await axios.post(affiliateSettingsURL, this.form);
      const { invite_link } = response.data.data.data;
      this.form.invite_link = `${window.location.origin}/affiliate-invite?invite=${invite_link}`;

      this.isLoading = false;
    },
    checkPercent(val, showToast = true) {
      if (val === "") {
        if (showToast)
          errorToast(
            "Atenção",
            "O Valor da comissão deve ser no mínimo 1% e no máximo 80%"
          );

        return false;
      }

      const pieces = val.split("");
      const finalValue = parseFloat(val.replace(",", "."));

      if (pieces[val.length - 1] === "%") {
        if (finalValue < 1 || finalValue > 80) {
          if (showToast)
            errorToast(
              "Atenção",
              "O Valor da comissão deve ser no mínimo 1% e no máximo 80%"
            );

          this.commissionUnformated = val;
          this.form.commission = val.replace(",", ".").replace("%", "");
          return false;
        }

        this.commissionUnformated = val;
        this.form.commission = val.replace(",", ".").replace("%", "");

        return true;
      }
    },
    handleSubmit() {
      if (!this.validateFields()) {
        return false;
      }
      this.form.instructions = $(".note-editable").html();
      this.updateSettings();
    },
    resetFields() {
      this.commissionUnformated = "";
      this.form = {
        product_id: affiliateProductId,
        approve_request_manually: false,
        receive_email_notifications: false,
        buyers_data_access_allowed: false,
        support_email: "",
        instructions: "",

        commission: "",
        // cookie_duration: '',
        cookie_duration: "90",
        assignment: null,
        invite_link: "",
      };
    },
    copyInviteLink() {
      navigator.clipboard.writeText(this.form.invite_link);
    },
  },
};
</script>

<style></style>
