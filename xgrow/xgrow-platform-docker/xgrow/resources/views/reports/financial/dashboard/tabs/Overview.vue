<template>
  <div>
    <div class="xgrow-card card-dark mx-0 mb-2 row align-items-center">
      <div class="col-md-7 col-12 p-0">
        <h5 class="text-white mb-0">
          Resumo - Financeiro
        </h5>
      </div>

      <div
        class="col-md-5 col-12 p-0 d-flex flex-column flex-md-row justify-content-between gap-2 gap-md-0 align-items-center"
      >
        <p class="text-white mb-0 p-2 pe-0 w-100" style="border-left: 1px solid #4e525f">
          Filtrar por Período:
        </p>
        <DatePicker
          class="mini-date-range w-50"
          v-model:value="datePicker"
          format="DD/MM/YYYY"
          :clearable="false"
          :confirm="true"
          confirm-text="Aplicar"
          :editable="false"
          separator=" - "
          type="date"
          range
          placeholder="Selecione o período"
          @confirm="updateDatePicker"
        />
      </div>
    </div>

    <div class="xgrow-card card-dark">
      <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
        <FinancialCard
          v-for="(card, index) in financialCards"
          :key="`financial-card-${index}`"
          :title="card.title"
          :subtitle="card.subtitle"
          :border-color="card.borderColor"
        >
          <template v-if="card.withdrawn">
            <div class="align-self-end">
              <button
                class="xgrow-button withdrawn-button"
                type="button"
                href="javascript:void(0)"
                @click="openWithdrawModal"
              >
                Sacar
              </button>
            </div>
          </template>
          <Tooltip
            v-if="card.hasTooltip"
            :id="`tooltip-${index}`"
            icon='<i class="fas fa-info-circle"></i>'
            :tooltip="card.tooltipText"
          />
        </FinancialCard>
      </div>
      <hr />
      <div class="container-fluid d-flex flex-column gap-3 p-0">
        <div class="row p-0 m-0 gap-3 gap-md-0">
          <div class="col-12 col-md-6 px-0 pe-md-2">
            <FinancialIndicatorCard>
              <template v-slot:container>
                <div>
                  <p>Volume transações</p>
                  <p class="xgrow-large-bold value">
                    {{ formatBRLCurrency(sumTransactions) }}
                  </p>
                </div>
              </template>
            </FinancialIndicatorCard>
          </div>
          <div class="col-12 col-md-6 px-0 ps-md-2">
            <FinancialIndicatorCard>
              <template v-slot:container>
                <div>
                  <p>Ticket médio</p>
                  <p class="xgrow-large-bold value">
                    {{ formatBRLCurrency(ticketAverage) }}
                  </p>
                </div>
              </template>
            </FinancialIndicatorCard>
          </div>
        </div>

        <div class="row p-0 m-0 gap-3 gap-md-0">
          <div class="col-12 col-md-3 p-0 pe-md-3">
            <FinancialIndicatorCard title="Nº de transações" icon="fas fa-exchange-alt">
              <template v-slot:list>
                <ul class="p-0 m-0">
                  <li>
                    <FinancialIndicatorItem
                      content="Paga"
                      :value="totalTransaction.paid"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Pendente"
                      :value="totalTransaction.pending"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Com falha"
                      :value="totalTransaction.failed"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Chargeback"
                      :value="totalTransaction.chargeback"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Estornada"
                      :value="totalTransaction.refunded"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Estorno pendente"
                      :value="totalTransaction.pending_refund"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Expirada"
                      :value="totalTransaction.expired"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Total"
                      :value="totalTransaction.total"
                      :border="false"
                      font-size="bigger"
                    />
                  </li>
                </ul>
              </template>
            </FinancialIndicatorCard>
          </div>

          <div class="col-12 col-md-3 px-0 pe-md-2">
            <FinancialIndicatorCard
              title="Formas de Pagamento"
              icon="fas fa-money-check-alt"
            >
              <template v-slot:container>
                <div class="xgrow-form-control mb-2 col-12">
                  <Multiselect
                    :options="filter.status.options"
                    v-model="filter.status.selected"
                    :searchable="true"
                    mode="tags"
                    placeholder="Filtrar por status"
                    @change="changeFilter($event, 'status')"
                  >
                    <template v-slot:noresults>
                      <p class="multiselect-option" style="opacity: 0.5">
                        Forma de pagamento não encontrada...
                      </p>
                    </template>
                  </Multiselect>
                </div>
              </template>
              <template v-slot:list>
                <ul class="p-0 m-0">
                  <li>
                    <FinancialIndicatorItem
                      content="Boleto"
                      :value="typePayment.boleto"
                    />
                  </li>
                  <li>
                    <FinancialIndicatorItem content="Pix" :value="typePayment.pix" />
                  </li>
                  <li>
                    <FinancialIndicatorItem
                      content="Crédito"
                      :value="typePayment.credit_card"
                    />
                  </li>
                </ul>
              </template>
            </FinancialIndicatorCard>
          </div>

          <div class="col-md-6 px-0 ps-md-2 d-flex flex-column gap-3">
            <div class="row p-0 m-0" style="background: #222429; border-radius: 10px">
              <div class="col-md-6 p-0">
                <FinancialIndicatorCard
                  title="Conversão Pix"
                  :local-icon="'/xgrow-vendor/assets/img/reports/pix.svg'"
                >
                  <template v-slot:list>
                    <ul class="p-0 m-0">
                      <li>
                        <FinancialIndicatorItem
                          content="Gerado"
                          :value="conversion.pix.generated"
                        />
                      </li>
                      <li>
                        <FinancialIndicatorItem
                          content="Pago"
                          :value="conversion.pix.paid"
                        />
                      </li>
                      <li>
                        <FinancialIndicatorItem
                          content="Conversão"
                          :value="conversion.pix.percent"
                          :border="false"
                          font-size="bigger"
                        />
                      </li>
                    </ul>
                  </template>
                </FinancialIndicatorCard>
              </div>
              <div class="col-md-6 p-0">
                <FinancialIndicatorCard
                  title="Conversão Boleto"
                  icon="fas fa-file-invoice-dollar"
                >
                  <template v-slot:list>
                    <ul class="p-0 m-0">
                      <li>
                        <FinancialIndicatorItem
                          content="Gerado"
                          :value="conversion.boleto.generated"
                        />
                      </li>
                      <li>
                        <FinancialIndicatorItem
                          content="Pago"
                          :value="conversion.boleto.paid"
                        />
                      </li>
                      <li>
                        <FinancialIndicatorItem
                          content="Conversão"
                          :value="conversion.boleto.percent"
                          :border="false"
                          font-size="bigger"
                        />
                      </li>
                    </ul>
                  </template>
                </FinancialIndicatorCard>
              </div>
            </div>

            <div class="row p-0 m-0">
              <div class="col-md-12 p-0">
                <FinancialIndicatorCard
                  title="Pagamentos no Cartão"
                  icon="fas fa-credit-card"
                >
                  <template v-slot:list>
                    <ul class="p-0 m-0">
                      <li>
                        <FinancialIndicatorItem
                          content="Único"
                          :value="cardMultiples.single"
                        />
                      </li>
                      <li>
                        <FinancialIndicatorItem
                          content="Múltiplos"
                          :value="cardMultiples.multiple"
                        />
                      </li>
                    </ul>
                  </template>
                </FinancialIndicatorCard>
              </div>
            </div>
          </div>
        </div>

        <div class="row p-0 m-0 gap-3 gap-md-0">
          <div class="col-12 col-md-4 px-0 pe-md-2">
            <FinancialIndicatorCard
              title="Total de vendas por status"
              icon="fas fa-dollar-sign"
            >
              <template v-slot:list>
                <ul
                  class="p-0 m-0 d-flex flex-column h-100 gap-2"
                  v-if="checkHasTransactionsByStatus()"
                >
                  <li v-for="status in transactionByStatus" :key="status">
                    <div
                      class="d-flex justify-content-between align-items-center pb-2"
                      style="font-size: 12.8px; color: #e7e7e7; font-weight: 600"
                    >
                      <p>
                        <i
                          class="fas fa-circle pe-2"
                          :style="`font-size: 8px; color: ${status.color}`"
                        ></i>
                        {{ status.title }}
                      </p>
                      <p>{{ status.value }}</p>
                    </div>
                    <div
                      class="progress"
                      style="border-radius: 10px; height: 4px; background: #2c303c"
                    >
                      <div
                        class="progress-bar"
                        role="progressbar"
                        :style="`width: ${status.percent}%; background: ${status.color};border-radius: inherit`"
                        aria-valuenow="50"
                        aria-valuemin="0"
                        aria-valuemax="100"
                      />
                    </div>
                  </li>
                </ul>
                <p v-else class="h-100 d-flex align-items-center xgrow-large-bold">
                  Sem dados para mostrar
                </p>
              </template>
            </FinancialIndicatorCard>
          </div>
          <div class="col-12 col-md-8 px-0 ps-md-2">
            <FinancialIndicatorCard
              title="Transações por período"
              icon="fas fa-chart-line"
            >
              <template v-slot:container>
                <div id="chart-period" class="mt-3" style="height: 360px"></div>
              </template>
            </FinancialIndicatorCard>
          </div>
        </div>

        <div class="row p-0 m-0 gap-3 gap-md-0">
          <div class="col-12 col-md-4 px-0 pe-md-2">
            <div class="xgrow-card financial-body-card">
              <div class="xgrow-card-header p-3 pb-0">
                <p class="xgrow-card-title">Status das transações em cartão</p>
              </div>
              <div
                id="chart-card-transactions-container"
                class="xgrow-card-body p-3 pt-0"
              >
                <div
                  class="mb-3"
                  id="chart-card-transactions"
                  style="height: 250px; max-width: 100%; margin-top: -45px"
                ></div>
                <div
                  id="chart-card-transactions-description"
                  style="margin-top: -50px"
                ></div>
              </div>
              <div
                id="chart-card-transactions-message"
                class="xgrow-card-body px-3 py-5 align-items-center d-none"
              >
                <p class="xgrow-large-bold">Sem dados para mostrar</p>
              </div>
              <div class="xgrow-card-footer"></div>
            </div>
          </div>
          <div class="col-12 col-md-8 px-0 ps-md-2">
            <FinancialIndicatorCard
              title="Quantidades de parcelas no cartão"
              icon="fas fa-percentage"
            >
              <template v-slot:container>
                <BarChart
                  chart-id="barChart"
                  v-if="barGraph.dataSet.length > 0"
                  :data-labels="barGraph.labels"
                  :data-set="barGraph.dataSet"
                  :chart-options="barGraph.option"
                ></BarChart>
                <p v-else class="h-100 d-flex align-items-center xgrow-large-bold">
                  Sem dados para mostrar
                </p>
              </template>
            </FinancialIndicatorCard>
          </div>
        </div>

        <div class="row p-0 m-0">
          <div class="col-12 px-0">
            <FinancialIndicatorCard
              title="Bandeiras mais utilizadas"
              icon="fas fa-money-check"
            >
              <template v-slot:list>
                <div class="row p-0 m-0" v-if="this.creditCardBrands.common.length > 0">
                  <ul class="col-12 col-md-6 p-0 m-0 pe-md-2">
                    <li v-for="brand in [0, 1, 2]" :key="brand">
                      <FinancialIndicatorItem
                        v-if="this.creditCardBrands.common[brand]"
                        :local-icon="`/xgrow-vendor/assets/img/reports/${this.creditCardBrands.common[
                          brand
                        ]?.brand.toLowerCase()}.svg`"
                        :content="this.creditCardBrands?.common[brand]?.brand"
                        :value="this.creditCardBrands?.common[brand]?.percent"
                      />
                    </li>
                  </ul>
                  <ul class="col-12 col-md-6 p-0 m-0 ps-md-2">
                    <li v-for="brand in [3, 4]" :key="brand">
                      <FinancialIndicatorItem
                        v-if="this.creditCardBrands.common[brand]"
                        :local-icon="`/xgrow-vendor/assets/img/reports/${this.creditCardBrands.common[
                          brand
                        ]?.brand.toLowerCase()}.svg`"
                        :content="this.creditCardBrands.common[brand]?.brand"
                        :value="this.creditCardBrands.common[brand]?.percent"
                      />
                    </li>
                    <li>
                      <FinancialIndicatorItem
                        :has-info="true"
                        :tooltip="creditCardBrands.other.content"
                        content="Outro"
                        :value="creditCardBrands.other.total"
                      />
                    </li>
                  </ul>
                </div>
                <p v-else class="h-100 d-flex align-items-center xgrow-large-bold">
                  Sem dados para mostrar
                </p>
              </template>
            </FinancialIndicatorCard>
          </div>
        </div>
      </div>
    </div>

    <Modal :is-open="modalWithdraw.isOpen" @close="closeWithdrawModal">
      <h3 class="modal__title">Realizar saque</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <p class="modal__subtitle">
          Confirme os dados bancários cadastrados no seu perfil e insira o valor que
          deseja sacar.
        </p>
        <div class="bank-data">
          <h4 class="bank-data__title bank-data__item">Dados bancários</h4>
          <div class="bank-data__item bank-data__item--dark">
            <p>Banco</p>
            <span> {{ recipient.account.bank }} </span>
          </div>
          <div class="bank-data__item">
            <p>Agência</p>
            <span>
              {{ recipient.account.branch_number }}
              {{
                recipient.account.branch_check_digit
                  ? "-" + recipient.account.branch_check_digit
                  : recipient.account.branch_check_digit
              }}
            </span>
          </div>
          <div class="bank-data__item bank-data__item--dark">
            <p>Conta</p>
            <span>
              {{ recipient.account.account_number }}-{{
                recipient.account.account_check_digit
              }}
            </span>
          </div>
          <div class="bank-data__item">
            <p>CPF/CNPJ</p>
            <span> {{ formatCpfCnpj(recipient.document) }} </span>
          </div>
        </div>

        <p id="withdraw-label" class="withdraw__available">
          Saldo disponível para saque:
          <span>{{ formatBRLCurrency(recipient.account.available_amount) }}</span>
        </p>
        <Alert
            :title="'Saque indisponível'"
            status="warning"
            v-show="recipient.account.bank=='000'"
        >
            Sua conta bancária está em análise. Entre em contato com o suporte para solicitar a liberação.
        </Alert>
        <div class="xgrow-floating-input mui-textfield mui-textfield--float-label w-100">
          <input
            id="withdraw_value"
            autocomplete="off"
            type="text"
            spellcheck="false"
            v-model="recipient.account.withdraw_value"
            v-maska="[
              '#,##',
              '##,##',
              '###,##',
              '#.###,##',
              '##.###,##',
              '###.###,##',
              '#.###.###,##',
              '##.###.###,##',
              '###.###.###,##',
            ]"
            @keyup="checkWithdrawAvailable()"
          />
          <label>Valor do saque</label>
        </div>

        <p class="withdraw__info">
          O valor estará disponível na conta no próximo dia útil após as 14h.
        </p>

        <hr class="modal__line mb-4" />
      </div>
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--cancel"
          @click="closeWithdrawModal"
        >
          Cancelar
        </button>
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          :disabled="withdrawButtonDisabled"
          @click="openWithdrawConfirmationModal"
        >
          <i class="fa fa-check"> </i>
          Confirmar saque
        </button>
      </div>
    </Modal>
    <Modal
      :is-open="modalWithdrawConfirmation.isOpen"
      @close="closeWithdrawConfirmationModal"
    >
      <h3 class="modal__title">Confirmar saque</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <div class="bank-data">
          <h4 class="bank-data__title bank-data__item">Informações do saque</h4>
          <div class="bank-data__item bank-data__item--dark">
            <p>Valor requisitado</p>
            <span v-if="recipient.account.withdraw_value">
              {{ formatBRLCurrency(removeValueMask(recipient.account.withdraw_value)) }}
            </span>
          </div>
          <div class="bank-data__item">
            <p>Custo da transferência:</p>
            <span> {{ formatBRLCurrency(recipient.fee) }}</span>
          </div>
          <div class="bank-data__item bank-data__item--dark">
            <p>Valor a ser creditado na conta</p>
            <span v-if="recipient.account.withdraw_value">
              {{
                formatBRLCurrency(
                  removeValueMask(recipient.account.withdraw_value) - recipient.fee
                )
              }}
            </span>
          </div>
        </div>
      </div>
      <hr class="modal__line mt-2 mb-5" />
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--cancel"
          @click="closeWithdrawConfirmationModal"
        >
          Cancelar
        </button>
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          @click="sendWithdraw"
        >
          <i class="fa fa-check"> </i>
          Efetuar saque
        </button>
      </div>
    </Modal>
    <Modal
      :is-open="modalWithdrawFeedback.isOpen"
      @close="closeWithdrawFeedbacktionModal"
    >
      <h3 class="modal__title">Saque realizado</h3>
      <hr class="modal__line" />
      <div class="d-flex flex-column">
        <p class="text-center" style="color: white; min-height: 119px">
          <span class="xgrow-large-bold">Eba!</span>
          O seu saque foi efetuado com sucesso. O valor estará disponível na sua conta no
          próximo dia útil a partir das 14:00 horas.
        </p>
      </div>
      <hr class="modal__line mb-4" />
      <div class="modal__actions">
        <button
          type="button"
          class="btn modal__actions--confirm"
          data-bs-dismiss="modal"
          aria-label="Close"
          @click="closeWithdrawFeedbacktionModal"
        >
          OK
        </button>
      </div>
    </Modal>
    <ModalComponent :is-open="message.isOpen" @close="closeMessageModal">
      <template v-slot:title>
        Ocorreu um problema no carregamento das informações
      </template>

      <template v-slot:content>
        <div class="d-flex flex-column">
          <p class="px-2 text-center xgrow-large-bold">
            Por favor contate a equipe de suporte!
          </p>
          <p class="px-2 text-center">{{ message.status }}</p>
        </div>
      </template>

      <template v-slot:footer>
        <button
          type="button"
          class="btn btn-success"
          data-bs-dismiss="modal"
          aria-label="Close"
          @click="closeMessageModal"
        >
          OK
        </button>
      </template>
    </ModalComponent>
    <Loading :is-open="loading.active" status="loading" />
  </div>
</template>

<script>
import FinancialCard from "../../../../../js/components/XgrowDesignSystem/Cards/FinancialCard";
import FinancialIndicatorCard from "../../../../../js/components/FinancialIndicatorCard";
import FinancialIndicatorItem from "../../../../../js/components/FinancialIndicatorItem";
import Tooltip from "../../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip";
import Modal from "../../../../../js/components/XgrowDesignSystem/Modals/Modal";
import BarChart from "../../../../../js/components/XgrowDesignSystem/Charts/BarChart";
import Loading from "../../../../../js/components/StatusModalComponent";
import ModalComponent from "../../../../../js/components/ModalComponent";
import Alert from "../../../../../js/components/XgrowDesignSystem/Alert/Alert";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import formatBRLCurrency from "../../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";

import moment from "moment";
import axios from "axios";
import {maska} from "maska";

export default {
  components: {
    FinancialCard,
    FinancialIndicatorCard,
    FinancialIndicatorItem,
    Tooltip,
    DatePicker,
    Multiselect,
    Modal,
    ModalComponent,
    BarChart,
    Loading,
    Alert,
  },
  mixins: [formatBRLCurrency],
  directives: { maska },
  props: {
    getWithdraw: {
      type: Function,
      default: () => {}
    }
  },
  data() {
    return {
      withdrawButtonDisabled: true,
      financialCards: [
        {
          title: "R$ 0,00",
          subtitle: "Disponível para saque",
          borderColor: "#93BC1E",
          hasTooltip: false,
          withdrawn: true,
        },
        {
          title: "R$ 0,00",
          subtitle: "Saldo Atual",
          borderColor: "#85F49E",
          hasTooltip: true,
          tooltipText: "O saldo atual estará disponível em até 30 dias para saque.",
        },
        {
          title: "R$ 0,00",
          subtitle: "Faturamento total",
          borderColor: "#85F4E0",
          hasTooltip: true,
          tooltipText: "Total vendido no Período",
        },
        {
          title: "R$ 0,00",
          subtitle: "A receber",
          borderColor: "#E1BB32",
          hasTooltip: true,
          tooltipText: "Parcelas do sem Limite em aberto.",
        },
      ],
      message: {
        isOpen: false,
        status: "loading",
      },
      loading: {
        active: false,
      },
      datePicker: [],
      period: "",
      barGraph: {
        labels: [],
        dataSet: [],
        option: {},
      },
      creditCardBrands: {
        other: { content: "", total: 0 },
        common: [],
        total: 0,
      },
      conversion: {
        pix: { generated: 0, paid: 0, percent: "0%" },
        boleto: { generated: 0, paid: 0, percent: "0%" },
      },
      totalTransaction: {
        paid: 0,
        pending: 0,
        failed: 0,
        chargeback: 0,
        expired: 0,
        refunded: 0,
        pending_refund: 0,
        total: 0,
      },
      cardMultiples: {
        single: 0,
        multiple: 0,
      },
      typePayment: {
        boleto: "0%",
        pix: "0%",
        credit_card: "0%",
      },
      ticketAverage: 0,
      sumTransactions: 0,
      transactionByStatus: {
        paid: { title: "Pago", percent: 0, value: "R$ 0,00", color: "#93BC1E" },
        pending: { title: "Pendente", percent: 0, value: "R$ 0,00", color: "#F4E558" },
        refunded: { title: "Estornado", percent: 0, value: "R$ 0,00", color: "#F45858" },
        chargeback: {
          title: "Chargeback",
          percent: 0,
          value: "R$ 0,00",
          color: "#D6D6D6",
        },
        receive: { title: "A Receber", percent: 0, value: "R$ 0,00", color: "#676f84" },
      },
      modalWithdraw: {
        isOpen: false,
      },
      modalWithdrawConfirmation: {
        isOpen: false,
      },
      modalWithdrawFeedback: {
        isOpen: false,
      },
      recipient: {
        account: {
          branch_number: "",
          branch_check_digit: "",
          bank: "",
          account_check_digit: "",
          account_number: "",
          available_amount: 0,
          withdraw_value: "",
        },
        document: "",
        type: "individual",
        fee: 0,
      },
      filter: {
        status: {
          options: {
            paid: "Pago",
            pending: "Pendente",
            failed: "Com falha",
            chargeback: "Chargeback",
            refunded: "Estornado",
            expired: "Expirado",
          },
          selected: [],
        },
      },
    };
  },
  methods: {
    sendWithdraw: async function () {
      this.loading.active = true;
      this.closeWithdrawConfirmationModal();
      const amount = this.removeValueMask(this.recipient.account.withdraw_value);
      try {
        await axios.post("/recipient/withdrawal", { amount });
        this.openWithdrawFeedbacktionModal();
        await this.getRecipientBalance();
        await this.getWithdraw()
      } catch (e) {
        this.openMessageModal("recipient/withdrawa error");
      }
      this.recipient.account.withdraw_value = "";
      this.withdrawButtonDisabled = true;
      this.loading.active = false;
    },
    async getData(period = this.period, allDate = 0) {
      const params = { period, allDate };

      this.loading.active = true;

      await this.getRecipientBalance();
      await this.getRecipientInfo();
      await this.graphTransactionByStatus(params);
      await this.graphCreditCardStatusTransactions(params);
      await this.graphTransactionsByInstallments(params);
      await this.getSumTransactions(params);
      await this.getAverageTicketPrice(params);
      await this.getCardMultiples(params);
      await this.getCreditCardBrands(params);

      await this.getGeneratedVsPaid(params, "boleto");
      await this.getGeneratedVsPaid(params, "pix");
      await this.getPercentTypePayment(params);
      await this.getToReceive(params);
      await this.getTotalBilling(params);
      await this.getTotalTransactions(params);
      await this.graphTransactionsByPeriod(params);

      this.loading.active = false;
    },

    async graphCreditCardStatusTransactions(params) {
      try {
        let total = 0;

        const res = await axios.get(
          "/api/reports/financial/status-graph-credit-card-transactions",
          {
            params,
          }
        );

        let graphColors = [];

        const colors = {
          "Transação autorizada": "#93bc1e",
          "Erro não especificado": "#ff0000",
          "Transação não autorizada": "#ff0000",
          "Saldo insuficiente": "#f4e558",
        };

        // Removed 0% values from the chart
        let graphData = res.data.response.transactions.filter((data) => {
          return data.value > 0;
        });

        if (graphData.length < 1) {
          $("#chart-card-transactions-message").removeClass("d-none");
          $("#chart-card-transactions-container").addClass("d-none");
          return;
        } else {
          $("#chart-card-transactions-message").addClass("d-none");
          $("#chart-card-transactions-container").removeClass("d-none");
        }

        graphData.forEach((data) => {
          total += data.value;
          if (!(data.name in colors)) {
            const randomColor = "#" + Math.floor(Math.random() * 16777215).toString(16);
            colors[data.name] = randomColor;
            graphColors.push(randomColor);
          } else {
            graphColors.push(colors[data.name]);
          }
        });

        let graphTransactions = `<ul class="px-0">`;
        graphData.forEach((value) => {
          graphTransactions += `
                        <li class="d-flex justify-content-between align-items-center border-bottom p-2">
                            <div class="d-flex">
                                <i style="color: ${
                                  colors[value.name]
                                }" class="fa fa-circle fa-fw me-2"></i>
                                <p class="xgrow-medium-regular">${value.name}</p>
                            </div>
                            <p class="xgrow-medium-bold green-info">${value.value}</p>
                        </li>
                    `;
        });
        graphTransactions += `
                <li class="d-flex justify-content-between align-items-center border-bottom p-2">
                    <div class="d-flex">
                        <i style="color: #676f84" class="fa fa-circle fa-fw me-2"></i>
                        <p class="xgrow-medium-regular">Total</p>
                    </div>
                    <p class="xgrow-medium-bold green-info">${total}</p>
                </li>
                `;
        graphTransactions += `</ul>`;

        let statusChart = echarts.init(
          document.getElementById("chart-card-transactions"),
          "#000000"
        );
        const options = {
          textStyle: {
            color: "#FFFFFF",
            fontWeight: 500,
            fontSize: 12,
          },
          tooltip: {
            trigger: "item",
            formatter: function (params, ticket, callback) {
              return `<u><strong>${params.name}</strong></u><br> ${params.value} (${params.percent}%)`;
            },
          },
          backgroundColor: "transparent",
          series: [
            {
              name: "Status das transações em cartão",
              type: "pie",
              radius: "50%",
              center: ["50%", "50%"],
              data: res.data.response.transactions,
              color: graphColors,
              emphasis: {
                itemStyle: {
                  shadowBlur: 10,
                  shadowOffsetX: 0,
                  shadowColor: "rgba(0, 0, 0, 0.5)",
                },
              },
              label: {
                show: false,
              },
            },
          ],
        };

        statusChart.setOption(options);
        window.addEventListener("resize", function () {
          statusChart.resize();
        });
        $("#chart-card-transactions-description").html(graphTransactions);
      } catch (error) {
        this.openMessageModal("status-graph-credit-card-transactions");
      }
    },
    graphTransactionsByInstallments: async function (params) {
      this.barGraph.labels = [];
      this.barGraph.dataSet = [];

      this.barGraph.option = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            min: 0,
            grid: { color: "#2A2E39", borderColor: "transparent" },
            ticks: { color: "#E7E7E7", scaleFontSize: 12, stepSize: 1, fontSize: 60 },
          },
          x: {
            grid: { display: false },
            ticks: {
              color: "#E7E7E7",
              maxRotation: 90,
              minRotation: 0,
              scaleFontSize: 5,
            },
          },
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            displayColors: false,
            cornerRadius: 16,
            padding: 12,
            backgroundColor: "#2C3037",
            titleAlign: "center",
            titleFont: {
              weight: "bold",
              family: "Avantgarde, TeX Gyre Adventor, URW Gothic L, sans-serif",
              size: 18,
              color: "#E7E7E7",
            },
            bodyFont: {
              weight: "normal",
              family: "Avantgarde, TeX Gyre Adventor, URW Gothic L, sans-serif",
              size: 12,
              color: "#E7E7E7",
            },
            footerAlign: "center",
            callbacks: {
              title: (context) => `${parseFloat(context[0].raw)}`,
              label: (context) =>
                `${context.label == "à vista" ? context.label : "em " + context.label}`,
            },
          },
        },
      };

      await axios
        .get("/api/reports/financial/installments-transactions", { params })
        .then(({ data }) => {
          let { dataBar, labels } = data;

          if (dataBar.length == 0 || labels.legnth == 0) return;

          let dataCount = dataBar.reduce((total, data) => (total += data), 0);

          if (dataCount == 0) return;

          const labelZero = labels.indexOf(0);
          const aVista = labels.indexOf(1);

          labels.forEach((label, index) => {
            if (labelZero == index) return;

            this.barGraph.labels.push(`${label > 1 ? label + " vezes" : "à vista"}`);
          });

          /*
                        label 0 is the new return from "a vista" payments
                        validate and sum with label 1 value (old return from "a vista" payments)
                        case have label 0 and label 1
                    */

          if (labelZero != -1 && aVista > -1) {
            dataBar[aVista] += dataBar[labelZero];
            dataBar.splice(labelZero, 1);
          }

          let boilerPlateBarGraphData = {
            borderRadius: { topLeft: 5, topRight: 5 },
            backgroundColor: "#6677A5",
            data: dataBar,
          };

          this.barGraph.dataSet.push(boilerPlateBarGraphData);
        })
        .catch(() => {
          this.openMessageModal("installments-transactions error");
        });
    },
    checkWithdrawAvailable: function () {
      const account = this.recipient.account;
      if (account.bank == '000') {
          this.withdrawButtonDisabled = true;
          return;
      }

      let [withdraw_value, available_amount] = [
        account.withdraw_value,
        account.available_amount,
      ];

      withdraw_value = this.removeValueMask(withdraw_value);

      if (
        withdraw_value === "" ||
        withdraw_value === undefined ||
        withdraw_value < 4.67
      ) {
        this.withdrawButtonDisabled = true;
        return;
      }
      if (withdraw_value > available_amount) {
        errorToast(
          "Atenção!",
          `O valor solicitado ultrapassa o valor disponível para saque!`
        );
        this.withdrawButtonDisabled = true;
        return;
      }

      this.withdrawButtonDisabled = false;
      return;
    },
    graphTransactionsByPeriod: async function (params) {
      try {
        const res = await axios.get("/api/reports/financial/period-transactions", {
          params,
        });

        let periodChart = echarts.init(
          document.getElementById("chart-period"),
          "#000000"
        );
        const options = {
          legend: {
            data: ["Vendas", "Pagas", "Pendentes", "Estornadas", "Chargeback"],
            textStyle: {
              color: "#FFFFFF",
            },
          },
          backgroundColor: "transparent",
          xAxis: {
            type: "category",
            data: res.data.labels,
            nameLocation: "middle",
            nameTextStyle: {
              color: "#FFFFFF",
              padding: [20, 0],
            },
            axisLabel: {
              color: "#FFFFFF",
            },
          },
          yAxis: [
            {
              type: "value",
              nameLocation: "middle",
              nameTextStyle: {
                color: "#FFFFFF",
                padding: [30, 0],
              },
              axisLabel: {
                color: "#FFFFFF",
              },
            },
          ],
          series: [
            {
              name: "Vendas",
              data: res.data.dataBar,
              type: "bar",
              color: "#5CDEE6",
            },
            {
              name: "Pagas",
              data: res.data.dataPaid,
              type: "line",
              color: "#93BC1E",
            },
            {
              name: "Pendentes",
              data: res.data.dataPending,
              type: "line",
              color: "#F4E558",
            },
            {
              name: "Estornadas",
              data: res.data.dataRefunded,
              type: "line",
              color: "#F45858",
            },
            {
              name: "Chargeback",
              data: res.data.dataChargeback,
              type: "line",
              color: "#D6D6D6",
            },
          ],
          tooltip: {
            trigger: "axis",
            axisPointer: {
              type: "shadow",
            },
            formatter: function (params, ticket, callback) {
              let text = `
                                <u><strong>${params[0].name}</strong></u><br>
                            `;

              for (let i = 0; i < params.length; i++) {
                text += `${
                  params[i].seriesName == "Vendas" ? "Total vendas" : params[i].seriesName
                }: ${formatCoin(params[i].value)}<br>`;
              }

              return text;
            },
          },
        };

        periodChart.setOption(options);
        window.addEventListener("resize", function () {
          periodChart.resize();
        });
      } catch (error) {}
    },
    getTotalTransactions: async function (params) {
      await axios
        .get("/api/reports/financial/total-transactions", { params })
        .then(({ data }) => {
          const response = data.data;

          if (response.length == 0) return;

          response.forEach((transaction) => {
            this.totalTransaction[transaction.status] = transaction.count;
          });
        })
        .catch(() => {
          this.openMessageModal("total-transactions error");
        });
    },
    getGeneratedVsPaid: async function (params, typePayment) {
      this.conversion[typePayment] = { generated: 0, paid: 0, percent: "0%" };

      await axios
        .get(
          `/api/reports/financial/generated-paid-transactions?type_payment=${typePayment}`,
          { params }
        )
        .then((response) => {
          const { generated, paid } = response.data.data;
          const percent = this.percentageCalculate(paid, generated);

          this.conversion[typePayment] = { generated, paid, percent };
        })
        .catch(() => {
          this.openMessageModal("generated-paid-transactions error");
        });
    },
    getToReceive: async function (params) {
      await axios
        .get("/api/reports/financial/total-to-receive", { params })
        .then(({ data }) => {
          const response = data.data;

          this.financialCards.forEach((card) => {
            if (card.subtitle == "A receber")
              card.title = this.formatBRLCurrency(response.total);
          });
        })
        .catch(() => {
          this.openMessageModal("total-to-receive error");
        });
    },
    getTotalBilling: async function (params) {
      await axios
        .get("/api/reports/financial/total-billing", { params })
        .then(({ data }) => {
          const response = data.data;

          this.financialCards.forEach((card) => {
            if (card.subtitle == "Faturamento total")
              card.title = this.formatBRLCurrency(response.total);
          });
        })
        .catch(() => {
          this.openMessageModal("total-billing error");
        });
    },
    getCreditCardBrands: async function (params) {
      this.creditCardBrands.other = { content: "", total: 0 };
      this.creditCardBrands.common = [];
      this.creditCardBrands.total = 0;

      await axios
        .get("/api/reports/financial/card-brands", { params })
        .then((response) => {
          let { brands, total } = response.data.data;

          brands = brands.sort((a, b) => b.count - a.count);

          this.creditCardBrands.total = total;

          brands.forEach((item, index) => {
            const percent = this.percentageCalculate(item.count, total);

            if (index < 5) {
              this.creditCardBrands.common.push({ brand: item.brand, percent });
            } else {
              this.creditCardBrands.other.content += `${item.brand}: ${percent}<br>`;
              this.creditCardBrands.other.total += item.count;
            }
          });

          this.creditCardBrands.other.total = this.percentageCalculate(
            this.creditCardBrands.other.total,
            total
          );
        })
        .catch(() => {
          this.openMessageModal("card-brands error");
        });
    },
    getAverageTicketPrice: async function (params) {
      await axios
        .get("/api/reports/financial/average-ticket-transactions", { params })
        .then(({ data }) => {
          const { total } = data.data;

          if (total == 0) return;

          this.ticketAverage = total;
        })
        .catch(() => {
          this.openMessageModal("average-ticket-transactions error");
        });
    },
    getSumTransactions: async function (params) {
      await axios
        .get("/api/reports/financial/sum-transactions", { params })
        .then(({ data }) => {
          const { total } = data.data;

          if (total == 0) return;

          this.sumTransactions = total;
        })
        .catch(() => {
          this.openMessageModal("sum-transactions error");
        });
    },
    getPercentTypePayment: async function (params, status = null) {
      this.typePayment = {
        boleto: "0%",
        pix: "0%",
        credit_card: "0%",
      };

      if (status) params.status = status;

      await axios
        .get("/api/reports/financial/percent-type-payment-transactions", { params })
        .then(({ data }) => {
          const response = data.data;

          if (response.length == 0) return;

          response.forEach((method) => {
            this.typePayment[method.type_payment] =
              parseFloat(method.percent).toFixed(2) + "%";
          });
        })
        .catch(() => {
          this.openMessageModal("type-payment-transactions error");
        });
    },
    getCardMultiples: async function (params) {
      await axios
        .get("/api/reports/financial/card-multiples", { params })
        .then(({ data }) => {
          const response = data.data;

          if (response.length == 0) return;

          response.forEach((method) => {
            this.cardMultiples[method.card] = method.count;
          });
        })
        .catch(() => {
          this.openMessageModal("card-multiples error");
        });
    },
    graphTransactionByStatus: async function (params) {
      await axios
        .get("/api/reports/financial/status-graph-transactions", { params })
        .then((response) => {
          const { data, labels } = response.data;

          if (data.length == 0) return;

          const total = data.reduce((total, label) => (total += label.value), 0);

          const status = {
            Paga: "paid",
            Pendente: "pending",
            Estornada: "refunded",
            Chargeback: "chargeback",
            "A Receber": "receive",
          };

          labels.forEach((label) => {
            const item = data.find((item) => item.name == label);
            if (item) {
              let percent = this.percentageCalculate(item.value, total);
              percent = percent.substring(0, percent.length - 1);
              if (this.transactionByStatus[status[label]]) {
                this.transactionByStatus[status[label]].percent = parseFloat(percent);
                this.transactionByStatus[status[label]].value = this.formatBRLCurrency(
                  item.value
                );
              }
            }
          });
        })
        .catch(() => {
          this.openMessageModal("status-graph-transactions error");
        });
    },
    async getRecipientBalance() {
      const params = {};

      await axios
        .get("/recipient/balance/", {
          params,
        })
        .then(async ({ data }) => {
          const response = data;

          if (!response) return;

          this.financialCards.forEach((card) => {
            if (card.subtitle === "Disponível para saque") {
              card.title = this.formatBRLCurrency((response.available_amount ?? 0) / 100);
              this.recipient.account.available_amount =
                (response.available_amount ?? 0) / 100;
            }

            if (card.subtitle === "Saldo Atual")
              card.title = this.formatBRLCurrency(
                (response.waiting_funds_amount ?? 0) / 100
              );
          });
        })
        .catch(() => {
          this.openMessageModal("recipient-balance error");
        });
    },
    async getRecipientInfo() {
      try {
        const params = {};

        const data = await axios.get("/recipient/info/", {
          params,
        });
        const account = data.data.default_bank_account;

        if (!account) return;

        const { document, type } = data.data;

        this.recipient.account = { ...this.recipient.account, ...account };
        this.recipient.document = document;
        this.recipient.fee = account.bank === "237" ? 0 : 3.67;
      } catch (e) {
        this.openMessageModal("recipient-info error");
      }
    },
    updatePeriod: function () {
      const firstDate = moment(this.datePicker[0]).format("DD/MM/YYYY");
      const lastDate = moment(this.datePicker[1]).format("DD/MM/YYYY");

      this.period = `${firstDate} - ${lastDate}`;
    },
    updateDatePicker: function (date) {
      this.datePicker = date;
      this.updatePeriod();

      this.filter.status.selected = [];
      this.getData();
    },
    defaultDatePicker: function () {
      const today = new Date();
      const lastMonth = new Date(moment().subtract(1, "month"));

      this.updateDatePicker([lastMonth, today]);
    },
    percentageCalculate: function (item, total) {
      return total && item != 0 ? ((item * 100) / total).toFixed(2) + "%" : "0%";
    },
    changeFilter: async function (value, property) {
      this.loading.active = true;
      this.filter[property].selected = value;

      const allDate = 0;
      const period = this.period;
      const params = { period, allDate };
      const selected = this.filter[property].selected;

      await this.getPercentTypePayment(params, selected);

      this.loading.active = false;
    },
    checkHasTransactionsByStatus: function () {
      let total = 0;

      for (const status in this.transactionByStatus) {
        total += this.transactionByStatus[status].percent;
      }

      return total > 0;
    },

    removeValueMask: function (value) {
      if (value == "") return;

      try {
        value = value.replaceAll(".", "");
        value = value.replace(",", ".");
        return parseFloat(value);
      } catch {}
    },

    formatCpfCnpj(item) {
      if (!item) return;

      if (item.length > 11) {
        return item.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
      }

      return item.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
    },

    openMessageModal: function (status) {
      this.message.isOpen = true;
      this.message.status = status;
    },
    closeMessageModal: function () {
      this.message.isOpen = false;
    },

    openWithdrawModal: function () {
      this.modalWithdraw.isOpen = true;
    },
    closeWithdrawModal: function () {
      this.modalWithdraw.isOpen = false;
    },
    openWithdrawConfirmationModal: function () {
      this.closeWithdrawModal();
      this.modalWithdrawConfirmation.isOpen = true;
    },
    closeWithdrawConfirmationModal: function () {
      this.modalWithdrawConfirmation.isOpen = false;
    },
    openWithdrawFeedbacktionModal: function () {
      this.modalWithdrawFeedback.isOpen = true;
    },
    closeWithdrawFeedbacktionModal: function () {
      this.modalWithdrawFeedback.isOpen = false;
    },
  },
  async mounted() {
    this.defaultDatePicker();
  },
};
</script>

<style>
#barChart {
  height: 345px !important;
}

.financial-body-card {
  color: #fff;
  width: 100%;
  height: 100% !important;
  margin: auto;
  border: double 2px transparent;
  border-radius: 5px;
  background-color: #222429;
  padding: 0;
}

.colored-card {
  position: relative;
}

.colored-card:first-child {
  min-width: 240px !important;
}

.withdrawn-button {
  max-width: 70px;
  max-height: 29px;
  margin-top: 0 !important;
  position: absolute !important;
  right: 10px !important;
  top: 22px !important;
}

@media screen and (max-width: 992px) {
  .colored-card {
    max-width: 100%;
  }
}
</style>
