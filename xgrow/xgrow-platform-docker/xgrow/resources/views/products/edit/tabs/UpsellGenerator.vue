<template>
  <div>
    <div id="upsellGenerator">
      <p class="xgrow-card-title mb-2">Gerador de Upsell</p>

      <p class="mb-4">Crie um botão de upsell para colocar na sua página de checkout.</p>

      <div class="row">
        <div class="col-sm-12">
          <b class="text-white">Textos</b>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
          <Input
            id="iptTextAccept"
            label="Texto de aceite"
            placeholder="Insira o texto de aceite do upsell..."
            v-model="form.accept.text"
          />
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
          <Input
            id="iptTextRefuse"
            label="Texto de recusa"
            placeholder="Insira o texto de recusa do upsell..."
            v-model="form.refuse.text"
          />
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-sm-12">
          <b class="text-white">Ao aceitar um upsell redirecionar para:</b>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6">
          <div class="xgrow-form-control my-2">
            <Multiselect
              v-model="form.accept.product"
              :options="form.accept.products"
              :searchable="true"
              @select="getPlansByProductsAccept"
              @clear="false"
              placeholder="Digite ou selecione o produto..."
            />
          </div>
        </div>

        <div class="col-sm-12 col-md-6 col-lg-6">
          <div class="xgrow-form-control my-2">
            <Multiselect
              v-model="form.accept.selectedPlan"
              :options="form.accept.plans"
              :searchable="true"
              @select="generateCheckoutAcceptLink"
              @clear="false"
              placeholder="Digite ou selecione o plano..."
            />
          </div>
        </div>

        <div class="col-sm-12" v-if="form.accept.product !== ''">
          <Input
            id="iptTextAcceptUrl"
            label="URL do próximo passo"
            placeholder="URL do próximo passo https://link.com.br"
            v-model="form.accept.url"
          />
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-sm-12">
          <b class="text-white">Ao recusar um upsell</b>
        </div>
        <div class="col-sm-12 my-2">
          <div class="xgrow-form-control">
            <Multiselect
              v-model="form.refuse.upsell"
              :options="form.refuse.upsells"
              @select="null"
              @clear="false"
              placeholder="Selecione uma opção..."
            />
          </div>
        </div>
        <template v-if="showRefuseField">
          <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="xgrow-form-control my-2">
              <Multiselect
                v-model="form.refuse.product"
                :options="form.refuse.products"
                :searchable="true"
                @select="getPlansByProductsRefuse"
                @clear="false"
                placeholder="Digite ou selecione o produto..."
              />
            </div>
          </div>

          <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="xgrow-form-control my-2">
              <Multiselect
                v-model="form.refuse.selectedPlan"
                :options="form.refuse.plans"
                :searchable="true"
                @select="generateCheckoutRefuseLink"
                @clear="false"
                placeholder="Digite ou selecione o plano..."
              />
            </div>
          </div>
        </template>

        <template v-if="form.refuse.upsell !== ''">
          <Input
            id="iptTextRefuseUrl"
            label="URL do próximo passo"
            placeholder="URL do próximo passo https://link.com.br"
            v-model="form.refuse.url"
          />
        </template>
      </div>

      <div class="row mt-3">
        <div class="col-sm-12">
          <b class="text-white">Design</b>
        </div>
        <div class="col-sm-12 col-md-4 my-2">
          <div class="d-flex gap-2">
            <input
              type="color"
              id="btnBgColor"
              class="input-color"
              v-model="form.design.btnBgColor"
            />
            <div>
              <label for="btnBgColor">Cor do botão</label>
              <p class="m-0 p-0">{{form.design.btnBgColor}}</p>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4 my-2">
          <div class="d-flex gap-2">
            <input
              type="color"
              id="btnTxtColor"
              class="input-color"
              v-model="form.design.btnTxtColor"
            />
            <div>
              <label for="btnTxtColor">Cor da fonte</label>
              <p class="m-0 p-0">{{form.design.btnTxtColor}}</p>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-4 my-2">
          <div class="d-flex gap-2">
            <input
              type="color"
              id="btnBorderColor"
              class="input-color"
              v-model="form.design.btnBorderColor"
            />
            <div>
              <label for="levelColor">Cor da borda</label>
              <p class="m-0 p-0">{{form.design.btnBorderColor}}</p>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-sm-12 mt-4">
          <b class="text-white">Preview</b>
        </div>
        <div class="col-sm-12 my-2">
          <div
            class="preview-container d-flex flex-column justify-content-center align-items-center text-center gap-2"
          >
            <template v-if="notFill">
              <i class="fa fa-magic fa-4x" style="color: #383c48"></i>
              <p style="color: #a1a5af"><b>Seu preview está vazio!</b></p>
              <p style="color: #717686">
                Preencha os campos acima para começar a customizar.
              </p>
            </template>
            <template v-else>
              <div class="xgrow-la-container">
                <button
                  id="xgrow-la-upsell-button"
                  class="xgrow-la-upsell-button"
                  :style="`background-color:${form.design.btnBgColor};
                                                         color:${form.design.btnTxtColor};
                                                         border-color:${form.design.btnBorderColor}`"
                >
                  {{form.accept.text}}
                </button>
                <a id="xgrow-la-downsell-button" class="xgrow-la-downsell-button">
                  {{form.refuse.text}}
                </a>
              </div>
            </template>
          </div>
        </div>
      </div>

      <StatusModalComponent
        :is-open="scriptModal.isOpen"
        @close="scriptModal.isOpen = false"
        modal-size="lg"
      >
        <template v-slot:content>
          <div class="row gap-3 text-center w-100" style="color: var(--gray1)">
            <div class="col-sm-12">
              <h5 class="text-white" style="font-size: 22px">
                <b>Script gerado com sucesso!</b>
              </h5>
              <p style="font-size: 16px">
                Clique no botão copiar para pegar o script e colar na sua página.<br />
                Lembrando que este script não é salvo na plataforma.
              </p>
            </div>

            <div class="col-sm-12 text-start d-flex flex-column gap-3 mt-3 box-clear">
              <p><b>Código do Widget</b></p>
              <textarea
                type="text"
                :value="scriptHtml"
                ref="scriptHtml"
                readonly
                rows="10"
                @focus="$event.target.select()"
              >
              </textarea>
              <div class="text-end w-100">
                <button
                  type="button"
                  class="btn btn-success xgrow-button"
                  @click.prevent="copyToClipboard"
                >
                  <i class="fas fa-copy mr-2"></i> Copiar Código
                </button>
              </div>
            </div>
          </div>
        </template>
        <template v-slot:footer="slotProps">
          <div class="w-100 text-center">
            <button
              type="button"
              class="btn btn-outline-light xgrow-button-cancel"
              style="width: 150px"
              @click="slotProps.closeModal"
            >
              Voltar
            </button>
          </div>
        </template>
      </StatusModalComponent>

      <div class="row my-3">
        <div class="col-sm-12 text-center">
          <button
            type="button"
            class="btn btn-success xgrow-button w150"
            @click="
              updateHtml();
              scriptModal.isOpen = true;
            "
          >
            <i class="fas fa-code mr-2"></i> Gerar script
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import StatusModalComponent from "../../../../js/components/ModalComponent";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";

export default {
  components: {
    StatusModalComponent,
    Multiselect,
    Input,
  },
  mixins: [],
  data() {
    return {
      /** If has loading */
      loading: {
        active: false,
        status: "loading",
      },
      scriptModal: {
        isOpen: false,
      },
      form: {
        accept: {
          products: [],
          product: "",
          text: "Sim, eu aceito a oferta especial!",
          url: "https://",
          plans: [],
          selectedPlan: "",
        },
        refuse: {
          products: [],
          product: "",
          text: "NÃO, eu quero recusar essa oferta.",
          upsell: "",
          upsells: [
            { value: 1, label: "Enviar para checkout" },
            { value: 2, label: "Ir para página" },
          ],
          url: "https://",
          plans: [],
          selectedPlan: "",
        },

        design: {
          btnBgColor: "#91bd1d",
          btnTxtColor: "#ffffff",
          btnBorderColor: "#779c18",
        },
      },
      scriptHtml: "",
    };
  },
  computed: {
    /** Verify if fields as filled */
    notFill: function () {
      return this.form.accept.text === "" && this.form.refuse.text === "";
    },
    /** Show or not the field based selection */
    showRefuseField: function () {
      return this.form.refuse.upsell === 1;
    },
  },
  methods: {
    /** Get all products */
    getProducts: async function () {
      const res = await axios.get(getUpsellURL);
      this.form.accept.products = this.form.refuse.products = res.data.products.map(
        (item) => {
          return {
            value: item.id,
            label: item.name,
          };
        }
      );
    },
    /** Get plans by products for accept */
    getPlansByProductsAccept: async function () {
      this.form.accept.selectedPlan = "";
      const getPlansByProducturl = getPlansByProductURL.replace(
        /:id/g,
        this.form.accept.product
      );
      const res = await axios.get(getPlansByProducturl);
      const data = res.data.data;
      this.form.accept.plans = data.map((item) => {
        if (item.status === "1") {
          return {
            value: item.id,
            label: item.name,
            platform: item.platform,
          };
        }
      });
    },
    /** Get plans by products for refuse */
    getPlansByProductsRefuse: async function () {
      this.form.refuse.selectedPlan = "";
      const getPlansByProducturl = getPlansByProductURL.replace(
        /:id/g,
        this.form.refuse.product
      );
      const res = await axios.get(getPlansByProducturl);
      const data = res.data.data;
      this.form.refuse.plans = data.map((item) => {
        if (item.status === "1") {
          return {
            value: item.id,
            label: item.name,
            platform: item.platform,
          };
        }
      });
    },
    /** Generate checkout link */
    generateCheckoutAcceptLink: function () {
      const res = this.form.accept.plans.filter(
        (plan) => plan.value === this.form.accept.selectedPlan
      )[0];
      this.form.accept.url = `${checkoutUrl}/${res.platform}/${btoa(
        this.form.accept.selectedPlan
      )}`;
    },
    /** Generate checkout link */
    generateCheckoutRefuseLink: function () {
      const res = this.form.refuse.plans.filter(
        (plan) => plan.value === this.form.refuse.selectedPlan
      )[0];
      this.form.refuse.url = `${checkoutUrl}/${res.platform}/${btoa(
        this.form.refuse.selectedPlan
      )}`;
    },
    /** Copy to clipboard */
    copyToClipboard: async function () {
      if (this.form.accept.text === "" || this.form.refuse.text === "")
        return errorToast(
          "Erro ao copiar script!",
          "Você precisa preencher os textos de aceite e recusa da oferta."
        );
      if (this.form.accept.url === "")
        return errorToast(
          "Erro ao copiar script!",
          "Você precisa preencher a URL ao aceitar uma oferta."
        );
      if (this.form.refuse.url === "")
        return errorToast(
          "Erro ao copiar script!",
          "Você precisa preencher a URL ao recusar uma oferta."
        );

      this.updateHtml();
      await new Promise((resolve) => setTimeout(resolve, 10));
      this.$refs.scriptHtml.focus();
      document.execCommand("copy");
      successToast(
        "Script copiado!",
        "O html foi copiado para sua área de transferência."
      );
    },
    /** Update html text */
    updateHtml: function () {
      this.scriptHtml = `<div> <style>.xgrow-container{display: flex; flex-direction: column; width: 100%; align-items: center; justify-content: space-between; text-align: center;}.xgrow-upsell-button{margin-top: 12px; padding: 14px 32px; cursor: pointer; background-color: ${this.form.design.btnBgColor}; color: ${this.form.design.btnTxtColor}; font-weight: 600; border-radius: 4px; border: 1px solid ${this.form.design.btnBorderColor}; font-size: 20px; font-family: Roboto, sans-serif; transition: all 0.2s ease-in-out;}.xgrow-upsell-button:hover{filter: opacity(80%);}.xgrow-downsell-button{margin-top: 1rem; cursor: pointer; font-size: 16px; text-decoration: underline; font-family: sans-serif;}</style> <script>let xgrowUpSellLink='${this.form.accept.url}'; let xgrowDownSellLink='${this.form.refuse.url}'; <\/script> <script src="${oneClickScript}"><\/script> <div class="xgrow-container"> <button id="xgrow-upsell-button" class="xgrow-upsell-button" onclick="openXgrowUpSell()" > ${this.form.accept.text}</button> <a id="xgrow-downsell-button" class="xgrow-downsell-button" onclick="openXgrowDownSell()" > ${this.form.refuse.text}</a > </div></div>`;
    }
  },
  async created() {
    await this.getProducts();
  },
};
</script>

<style></style>
