<template>
  <xgrow-tab-content id="transactionsPayments" :selected="isActive">
    <XgrowTable id="transactionsPaymentsTable">
      <template v-slot:header>
        <th>Pedido</th>
        <th>Transação</th>
        <th>Status</th>
        <th>Cancelado em</th>
        <th>Método</th>
        <th>Origem</th>
        <th>Data</th>
        <th>Valor total</th>
        <th>Valor líquido</th>
        <th></th>
      </template>
      <template v-slot:body>
        <tr>
          <td>{{ payment.order_number }}</td>
          <td>{{ payment.charge_code ?? " - " }}</td>
          <td :class="payment.payment_plan_refund_failed_at ? 'd-flex align-items-center gap-2' : ''">
            <StatusBadge :status="payment.payment_status" />
            <Tooltip
              v-if="payment.payment_plan_refund_failed_at != '-'"
              :id="`tooltip-${(Math.random() * 9999).toFixed()}`"
              icon='<i class="fas fa-info-circle"></i>'
              tooltip="Falha ao estornar pagamento"
              @click="toggleRefundFailModal(true)"
            />
          </td>
          <td>
            {{
              payment.cancellation_date != "-"
                ? formatDateSingleLine(payment.cancellation_date)
                : payment.cancellation_date
            }}
          </td>
          <td>{{ payment.type_payment }}</td>
          <td>{{ payment.origin_of_payment }}</td>
          <td><span v-html="formatDateSingleLine(payment.transaction_date)"></span></td>
          <td>{{ payment.value }}</td>
          <td>{{ payment.net_value }}</td>
          <td>
            <options-menu-component v-if="payment_status === 'paid'">
              <li class="option">
                <button
                  class="option-btn"
                  @click.prevent="
                    openModal({ paymentId, modal: 'paid', ...paymentInformation })
                  "
                >
                  <i class="fas fa-undo"></i>
                  Estornar pagamento
                </button>
              </li>
              <li class="option">
                <button
                  class="option-btn"
                  @click.prevent="
                    openModal({ paymentId, modal: 'paid-proof', ...paymentInformation })
                  "
                >
                  <i class="fas fa-scroll"></i>
                  Reenviar comprovante de compra
                </button>
              </li>
            </options-menu-component>
            <options-menu-component v-if="payment_status === 'refunded'">
              <li class="option">
                <button
                  class="option-btn"
                  @click.prevent="
                    openModal({ paymentId, modal: 'refunded', ...paymentInformation })
                  "
                >
                  <i class="fas fa-undo"></i>
                  Reenviar comprovante de estorno
                </button>
              </li>
              <li class="option">
                <button
                  class="option-btn"
                  @click.prevent="
                    downloadRefundProof({ paymentId, ...paymentInformation })
                  "
                >
                  <i class="fas fa-scroll"></i>
                  Baixar comprovante de estorno
                </button>
              </li>
            </options-menu-component>
            <options-menu-component
              v-if="payment_status === 'expired' && type_payment === 'Boleto'"
            >
              <li class="option">
                <button
                  class="option-btn"
                  @click.prevent="
                    openModal({
                      paymentId,
                      modal: 'resend-boleto',
                      ...paymentInformation,
                    })
                  "
                >
                  <i class="fas fa-undo"></i>
                  Reenviar boleto
                </button>
              </li>
            </options-menu-component>
          </td>
        </tr>
      </template>
    </XgrowTable>

    <refund-modal
      :is-open="isOpen && modal === 'paid'"
      :close-function="closeModal"
      :modal-data="modalData"
      :refund-function="refund"
    >
    </refund-modal>
    <refund-proof-modal
      :is-open="isOpen && modal === 'refunded'"
      :close-function="closeModal"
      :modal-data="modalData"
    >
    </refund-proof-modal>
    <payment-proof-modal
      :is-open="isOpen && modal === 'paid-proof'"
      :close-function="closeModal"
      :modal-data="modalData"
    >
    </payment-proof-modal>
    <resend-boleto-modal
      :is-open="isOpen && modal === 'resend-boleto'"
      :close-function="closeModal"
      :modal-data="modalData"
    >
    </resend-boleto-modal>
    <RefundFail :is-open="isOpenRefundFailModal" :toggle="toggleRefundFailModal"/>
  </xgrow-tab-content>
</template>

<script>
import RefundFail from "../../../../../../js/components/XgrowDesignSystem/Modals/RefundFail.vue";
import Tooltip from "../../../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip.vue";
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import OptionsMenuComponent from "../../../../../../js/components/OptionsMenuComponent.vue";
import TooltipComponent from "../../../../../../js/components/TooltipComponent.vue";
import RefundModal from "./RefundModal.vue";
import RefundProofModal from "./RefundProofModal.vue";
import PaymentProofModal from "./PaymentProofModal.vue";
import ResendBoletoModal from "./ResendBoletoModal.vue";
import axios from "axios";
import XgrowTable from "../../../../../../js/components/Datatables/Table";
import StatusBadge from "../../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateSingleLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";

export default {
  name: "transactions-payments-component",
  mixins: [formatDateSingleLine],
  components: {
    StatusBadge,
    XgrowTable,
    "xgrow-tab-content": XgrowTabContent,
    "options-menu-component": OptionsMenuComponent,
    "xgrow-tooltip-componet": TooltipComponent,
    "refund-modal": RefundModal,
    "refund-proof-modal": RefundProofModal,
    "payment-proof-modal": PaymentProofModal,
    "resend-boleto-modal": ResendBoletoModal,
    Tooltip,
    RefundFail
  },
  props: {
    isActive: {
      type: Boolean,
      required: true,
    },
    paymentInformation: {
      type: Object,
      default: null,
    },
    paymentId: {
      type: [String, Number],
      default: null,
    },
    getTransactions: {
      type: Function,
      required: true,
      default: () => {},
    },
    close: {
      type: Function,
      required: true,
      default: () => {},
    },
  },
  data() {
    return {
      isOpenRefundFailModal: false,
      /** Modal */
      isOpen: false,
      modalData: {},
      modal: "",
      payment: this.paymentInformation,
      /** General data */
      ...this.paymentInformation,
    };
  },
  computed: {
    product: function () {
      return {
        name: this.product_name.replaceAll(/\[R]$/g, ""),
        isRecurrent: this.product_name.includes("[R]"),
      };
    },
  },
  methods: {
    /** Open Modal */
    openModal: function (value) {
      this.modalData = value;
      this.modal = value.modal;
      this.isOpen = true;
    },
    /** Close modal */
    closeModal: function () {
      this.modalData = {};
      this.isOpen = false;
    },
    /** Refunds functions */
    async refund(type, payment_plan_id, data) {
      try {
        const res = await axios.post(postRefundURL, {
          type: type.toString(),
          payment_plan_id: payment_plan_id.toString(),
          single: true,
          account: data.account?.toString() || '',
          account_digit: data.account_digit?.toString() || '',
          agency: data.agency?.toString() || '',
          agency_digit: data.agency_digit?.toString() || '',
          bank_code: data.bank_code?.toString() || '',
          document_number: data.document_number?.toString() || '',
          legal_name: data.legal_name?.toString() || '',
          account_type: data.account_type?.toString() || '',
          reason: data.reason?.toString() || '',
        });

        if (res.data.error) {
          errorToast("Algum erro aconteceu!", res.data.message);
          return;
        }

        this.close();
        await this.getTransactions();

        successToast("Estorno realizado!", res.data.message);
      } catch (error) {
        errorToast("Algum erro aconteceu!", error.response.data.message);
      }
    },
    /** Refund Proof */
    downloadRefundProof: async function (paymentData) {
      const getRefundProofDocumentUrl = getRefundProofDocumentURL.replace(
        /:paymentId/g,
        paymentData.paymentId
      );
      const res = await axios.get(getRefundProofDocumentUrl);
      const template = this.refundDocumentTemplate(res.data);
      const content = htmlToPdfmake(template);
      const docDefinition = { content };
      pdfMake.createPdf(docDefinition).download();
    },
    /** Refund document  for download */
    refundDocumentTemplate: function (data) {
      const purchase = data.purchase;
      const refund = data.refund;
      const subscriber = data.subscriber;

      let products = "";
      purchase.products.forEach((value, index) => {
        let suffix = ", ";
        if (index === purchase.products.length - 1) suffix = "";
        products += `${value.name}${suffix}`;
      });

      let content = `
                <div>
                    <div>
                        <h4>Comprovante de estorno</h4><br>
                    </div>
                    <div>
                        <div><br></div>
                        <div style="font-size:18px">O valor de ${formatCoin(
                          refund.total
                        )} do número de pedido ${
        refund.code
      } foi estornado e estará disponível em sua conta de acordo com o processamento do seu banco.</div>
                    </div>
                    <div><br></div>
                    <div style="font-size:14px">
                        <div>Dados do comprador:</div>
                        <div>Nome: ${subscriber.name}</div>
                        <div>${subscriber.document_type}: ${
        subscriber.document_number
      }</div>
                        <div>E-mail: ${subscriber.email}</div>
                        <div>Celular: ${subscriber.cellphone}</div>
                        <div><br></div>
                        <div>Dados da compra:</div>
                        <div>Produtos: ${products}</div>
                        <div>Total: ${formatCoin(purchase.total)}</div>
                    </div>
                    <div><br></div>
                    <div><br></div>
                    <div>
                        <p style="font-size:12px">Gerado em ${formatDateTimePTBR(
                          new Date()
                        )}</p><br>
                    </div>
                </div>
            `;

      return content;
    },
    toggleRefundFailModal(status) {
      this.isOpenRefundFailModal = status
    },
  },
};
</script>

<style scoped lang="scss"></style>
