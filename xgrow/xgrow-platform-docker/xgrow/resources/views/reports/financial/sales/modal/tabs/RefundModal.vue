<template>
  <ModalComponent :isOpen="isOpen" @close="closeModal">
    <template v-slot:title>Confirmar estorno</template>

    <template v-slot:content>
      <p class="value my-2">
        Valor do estorno:
        <span>{{
          modalData.net_value || formatBRLCurrency(modalData.customer_value)
        }}</span>
      </p>
      <p class="message my-2">
        O estorno do pagamento poderá realizar o cancelamento do produto.
      </p>
      <Input
        id="reason-refund"
        label="Motivo"
        placeholder="O motivo deve contar de 10 a 50 caracteres"
        v-model="reason"
      >
      </Input>

      <div v-if="modalData.single !== undefined">
        <p class="message my-2" v-if="modalData.single">
          Essa operação irá estornar o pagamento apenas desta parcela. Cobranças
          anteriores não serão reembolsadas e as cobranças futuras serão mantidas. O
          acesso do cliente ao produto será mantido também.<br />Confirma o estorno da
          parcela selecionada?
        </p>
        <p class="message my" v-else>
          Essa operação irá estornar o pagamento de todas parcelas. Cobranças futuras
          serão canceladas. O acesso do cliente ao produto será perdido também.<br />Confirma
          o estorno do cliente selecionado?
        </p>
      </div>

      <div v-if="modalData.type_payment === 'Boleto'">
        <p class="mt-3">Preencha os dados do favorecido para prosseguir</p>
        <p class="message mt-2">
          O documento do titular da conta deve ser o mesmo do aluno
        </p>

        <div class="row mt-2">
          <div class="col-12">
            <Multiselect
              :options="banks"
              v-model="accountData.bank_code"
              :searchable="true"
              mode="single"
              placeholder="Banco"
            >
              <template v-slot:noresults>
                <p class="multiselect-option" style="opacity: 0.5">
                  Banco não encontrado...
                </p>
              </template>
            </Multiselect>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <Select
              id="account_type"
              label="Tipo de conta"
              placeholder="Selecione uma opção"
              :options="bankTypes"
              v-model="accountData.account_type"
              required
            />
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-lg-6 col-md-6 col-sm-12">
            <Input
              id="bank-branch"
              label="Agência"
              placeholder="Número da agência"
              type="number"
              v-model="accountData.agency"
            >
            </Input>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <Input
              id="bank-branch-digit"
              label="DG"
              placeholder="Dígito da agência (opcional)"
              type="number"
              v-model="accountData.agency_digit"
            >
            </Input>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-lg-6 col-md-6 col-sm-12">
            <Input
              id="bank-account"
              label="Conta"
              placeholder="Número da conta"
              type="number"
              v-model="accountData.account"
            >
            </Input>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <Input
              id="bank-account-digit"
              label="DG"
              placeholder="Dígito da conta"
              type="number"
              v-model="accountData.account_digit"
            >
            </Input>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="type-toggle">
              <label>Tipo de pessoa</label>
              <div class="buttons-toggle">
                <button
                  :class="{ active: type === 'fisical' }"
                  @click.prevent="type = 'fisical'"
                >
                  Física
                </button>
                <button
                  :class="{ active: type === 'legal' }"
                  @click.prevent="type = 'legal'"
                >
                  Jurídica
                </button>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-md-6 col-sm-12">
            <Input
              id="bank-document"
              :label="type === 'fisical' ? 'CPF' : 'CNPJ'"
              :mask="type === 'fisical' ? '###.###.###-##' : '##.###.###/####-##'"
              placeholder="Número do documento"
              v-model="accountData.document_number"
            >
            </Input>
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-12">
            <Input
              id="bank-name"
              :label="type === 'fisical' ? 'Nome' : 'Razão social'"
              :placeholder="`${
                type === 'fisical' ? 'Nome completo' : 'Razão social'
              } do favorecido`"
              v-model="accountData.legal_name"
            >
            </Input>
          </div>
        </div>
      </div>
    </template>

    <template v-slot:footer>
      <button type="button" class="btn btn-success" @click.prevent="confirmRefund">
        Confirmar
      </button>
      <button
        type="button"
        class="btn btn-outline-success"
        @click.prevent="closeModal"
      >
        Cancelar
      </button>
    </template>
  </ModalComponent>
</template>

<script>
import ModalComponent from "../../../../../../js/components/ModalComponent.vue";
import Select from "../../../../../../js/components/XgrowDesignSystem/Form/Select.vue";
import Input from "../../../../../../js/components/XgrowDesignSystem/Input.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import axios from "axios";

export default {
  name: "refund-modal",
  components: {
    Select,
    ModalComponent,
    Input,
    Multiselect,
  },
  props: {
    isOpen: {
      type: Boolean,
      required: true,
    },
    closeFunction: {
      type: Function,
      required: true,
    },
    modalData: {
      type: Object,
      required: true,
    },
    refundFunction: {
      type: Function,
      required: true,
    },
  },
  data() {
    return {
      banks: {},
      type: "fisical", // fisical|legal
      reason: "",
      accountData: {
        bank_code: null,
        account: null,
        account_digit: null,
        agency: null,
        agency_digit: null,
        document_number: "",
        legal_name: "",
        account_type: null,
      },
      bankTypes: [
        {
          value: 'savings',
          name: 'Conta Poupança',
        },
        {
          value: 'checking',
          name: 'Conta Corrente',
        },
      ],
    };
  },
  methods: {
    closeModal() {
      this.reason = '';
      this.accountData = {
        bank_code: null,
        account: null,
        account_digit: null,
        agency: null,
        agency_digit: null,
        document_number: "",
        legal_name: "",
        account_type: null,
      };

      this.closeFunction();
    },
    getBankList: function () {
      axios
        .get(getBankListURL)
        .then((res) => {
          res.data.forEach((bank) => {
            this.banks[bank.code] = `${bank.code} - ${bank.bank}`;
          });
        })
        .catch((error) => {
          errorToast(
            "Algum erro aconteceu!",
            "Não foi possível carregar os dados para o estorno, recarrega a página e tente novamente."
          );
        });
    },

    confirmRefund: function () {
      const fType = this.formatPaymentMethodInverse(this.modalData.type_payment);
      const [status, message] = this.validation(
        fType,
        this.modalData.paymentId || this.modalData.payment_plan_id
      );

      if (!status) {
        errorToast("Algum erro aconteceu!", message);
        return;
      }

      const data = {
        reason: this.reason,
        ...this.accountData,
      };
      if (this.modalData.single !== undefined) {
        data["single"] = this.modalData.single;
      }

      this.refundFunction(
        fType,
        this.modalData.paymentId || this.modalData.payment_plan_id,
        data
      );
      this.closeModal();
    },

    validation: function (type, payment_plan_id) {
      if (payment_plan_id === null || payment_plan_id === undefined) {
        return [
          false,
          "Não é possível fazer o estorno no momento, por favor contate o suporte.",
        ];
      }

      if (
        this.reason === null ||
        this.reason === "" ||
        this.reason.length < 10 ||
        this.reason.length > 50
      ) {
        return [false, "Preencha o motivo do estorno corretamente."];
      }

      if (type === "boleto") {
        if (this.accountData.bank_code === 0 || this.accountData.bank_code === null) {
          return [false, "Escolha um banco"];
        }
        if (this.accountData.account_type === null) {
          return [false, "Escolha um tipo de conta"];
        }
        if (this.accountData.agency === 0 || this.accountData.agency === null) {
          return [false, "Preencha o número da agência"];
        }
        if (this.accountData.account === 0 || this.accountData.account === null) {
          return [false, "Preencha o número da conta"];
        }
        if (this.accountData.account_digit === null) {
          return [false, "Preencha o número da conta"];
        }
        if (this.type === "fisical") {
          if (this.accountData.document_number.length < 14) {
            return [false, "Preencha o núermo do CPF corretamente"];
          }
        }
        if (this.type === "legal") {
          if (this.accountData.document_number.length < 18) {
            return [false, "Preencha o núermo do CNPJ corretamente"];
          }
        }
        if (
          this.accountData.legal_name === null ||
          this.accountData.legal_name === "" ||
          this.accountData.legal_name.length < 5 ||
          this.accountData.legal_name.length > 30
        ) {
          return [false, "Preencha o nome corretamente"];
        }
      }

      return [true, ""];
    },

    formatPaymentMethodInverse: function (method) {
      const methodFormatted = {
        "Cartão de Crédito": "credit_card",
        Boleto: "boleto",
        PIX: "pix",
        Paypal: "paypal",
      };
      return methodFormatted[method] || "-";
    },
    formatBRLCurrency: function (value) {
      return new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(value);
    },
  },
  created() {
    this.getBankList();
  },
};
</script>

<style scoped lang="scss">
@import "../../../../../../sass/util";

:deep(.modal-body) {
  padding: 0;
  width: 100%;
  flex-direction: column;
  justify-content: flex-start;
}

:deep(.modal-footer) {
  padding: 0;
}

.value {
  span {
    font-weight: 700;
  }
}

.message {
  font-size: pxToRem(12px);
  font-style: italic;
}

.type-toggle {
  margin: 12px 0;
  padding: 0 12px;
  width: 100%;

  label {
    display: inline-block;
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 5px;
  }

  .buttons-toggle {
    button {
      box-sizing: border-box;
      width: 50%;
      border: 1px solid #ffffff;
      color: #ffffff;
      padding: 6px 12px;
      background-color: transparent;

      &.active {
        border: 1px solid #93bc1e !important;
        color: #93bc1e;
        font-weight: bold;
      }

      &:first-child {
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
        border-right: none;
      }

      &:last-child {
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
        border-left: none;
      }
    }
  }
}
</style>
