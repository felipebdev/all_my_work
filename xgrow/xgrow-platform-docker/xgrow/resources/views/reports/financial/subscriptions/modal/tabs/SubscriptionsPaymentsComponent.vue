<template>
    <XgrowTabContent id="subscriptionsPayments" :selected="isActive">
        <Table id="transactionsPaymentsTable">
            <template v-slot:header>
                <th>Pedido</th>
                <th>Transação</th>
                <th>Produto</th>
                <th>Data do pgto.</th>
                <th>Método pgto.</th>
                <th>Origem do pgto.</th>
                <th>Cancelado em</th>
                <th>Valor</th>
                <th>Valor líquido</th>
                <th>Status</th>
                <th></th>
            </template>
            <template v-slot:body>
                <tr v-for="(payment, idx) in paymentsInformation" :key="idx">
                    <td>{{ payment.order_number }}</td>
                    <td>{{ payment.transaction_code }}</td>
                    <td>
                        <span class="title">
                            <a :href="`/products/${payment.product_id}/plans`">
                                {{ payment.product_name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ payment.plan_name }}</span>
                    </td>
                    <td><span>{{ formatDateSingleLine(payment.transaction_date) }}</span></td>
                    <td>{{ payment.type_payment }}</td>
                    <td>{{ payment.origin_of_payment }}</td>
                    <td><span v-html="formatDateTimeDualLine(payment.cancellation_date)"></span></td>
                    <td>{{ payment.value }}</td>
                    <td>{{ payment.net_value }}</td>
                    <td>
                        <StatusBadge :status="payment.payment_status"/>
                    </td>
                    <td>
                        <OptionsMenuComponent v-if="['paid', 'pago', 'Pago'].includes(payment.payment_status)">
                            <li class="option">
                                <button class="option-btn"
                                        @click.prevent="openModal({ payment_plan_id: payment.payment_plan_id, modal: 'paid-proof', ...payment })">
                                    <span class="material-symbols-outlined">send</span>
                                    Reenviar comprovante de compra
                                </button>
                            </li>
                            <li class="option">
                                <button class="option-btn"
                                        @click.prevent="openModal({ payment_plan_id: payment.payment_plan_id, modal: 'paid', ...payment} )">
                                    <i class="fas fa-undo"></i>
                                    Estornar pagamento
                                </button>
                            </li>
                        </OptionsMenuComponent>
                        <OptionsMenuComponent
                            v-if="['refunded', 'estornado', 'Estornado'].includes(payment.payment_status)">
                            <li class="option">
                                <button class="option-btn"
                                        @click.prevent="openModal({ payment_plan_id: payment.payment_plan_id, modal: 'refunded', ...payment })">
                                    <span class="material-symbols-outlined">send</span>
                                    Reenviar comprovante de estorno
                                </button>
                            </li>
                            <li class="option">
                                <button class="option-btn"
                                        @click.prevent="downloadRefundProof({payment_plan_id: payment.payment_plan_id, ...payment })">
                                    <i class="fas fa-scroll"></i>
                                    Baixar comprovante de estorno
                                </button>
                            </li>
                        </OptionsMenuComponent>
                        <!--                        <OptionsMenuComponent v-if="payment_status === 'expired' && type_payment === 'Boleto'">-->
                        <!--                            <li class="option">-->
                        <!--                                <button class="option-btn"-->
                        <!--                                        @click.prevent="openModal({ payment_plan_id, modal: 'resend-boleto', ...paymentInformation })">-->
                        <!--                                    <i class="fas fa-undo"></i>-->
                        <!--                                    Reenviar boleto-->
                        <!--                                </button>-->
                        <!--                            </li>-->
                        <!--                        </OptionsMenuComponent>-->
                    </td>
                </tr>
            </template>
        </Table>
    </XgrowTabContent>
    <RefundModal
        :is-open="isOpen && modal === 'paid'" :close-function="closeModal"
        :modal-data="modalData" :refund-function="refund">
    </RefundModal>
    <RefundProofModal
        :is-open="isOpen && modal === 'refunded'" :close-function="closeModal"
        :modal-data="modalData">
    </RefundProofModal>
    <PaymentProofModal
        :is-open="isOpen && modal === 'paid-proof'" :close-function="closeModal"
        :modal-data="modalData">
    </PaymentProofModal>
</template>

<script>
import axios from "axios";
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import Table from "../../../../../../js/components/Datatables/Table";
import OptionsMenuComponent from "../../../../../../js/components/OptionsMenuComponent";
import RefundModal from "../../../subscriptions/modal/RefundModal";
import PaymentProofModal from "../../../subscriptions/modal/PaymentProofModal";
import RefundProofModal from "../../../subscriptions/modal/RefundProofModal";
import StatusBadge from "../../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateSingleLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatDateTimeDualLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";

export default {
    name: "subscriptions-payments-component",
    components: {
        StatusBadge,
        RefundProofModal,
        RefundModal,
        PaymentProofModal,
        OptionsMenuComponent,
        XgrowTabContent,
        Table,
    },
    mixins: [formatDateTimeDualLine, formatDateSingleLine],
    props: {
        isActive: {
            type: Boolean,
            required: true,
        },
        paymentsInformation: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            /** Modal */
            isOpen: false,
            modalData: {},
            modal: '',

            /** Payment */
            ...this.paymentsInformation,
        };
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
        refund(type, payment_plan_id, data) {
            axios
                .post(postRefundURL, {
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
                    reason: data.reason?.toString() || '',
                })
                .then(res => {
                    if (res.data.error) {
                        errorToast("Algum erro aconteceu!", res.data.message);
                        return;
                    }
                    successToast("Estorno realizado!", res.data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                })
                .catch(error => {
                    errorToast("Algum erro aconteceu!", error.response.data.message);
                });
        },
        /** Refund Proof */
        downloadRefundProof: async function (paymentData) {
            const getRefundProofDocumentUrl = getRefundProofDocumentURL.replace(/:paymentId/g, paymentData.payment_plan_id);
            const res = await axios.get(getRefundProofDocumentUrl);
            const template = this.refundDocumentTemplate(res.data);
            const content = htmlToPdfmake(template);
            const docDefinition = {content};
            pdfMake.createPdf(docDefinition).download();
        },
        /** Refund document  for download */
        refundDocumentTemplate: function (data) {
            const purchase = data.purchase;
            const refund = data.refund;
            const subscriber = data.subscriber;

            let products = '';
            purchase.products.forEach((value, index) => {
                let suffix = ', ';
                if (index === purchase.products.length - 1) suffix = '';
                products += `${value.name}${suffix}`;
            });

            let content = `
                <div>
                    <div>
                        <h4>Comprovante de estorno</h4><br>
                    </div>
                    <div>
                        <div><br></div>
                        <div style="font-size:18px">O valor de ${formatCoin(refund.total)} do número de pedido ${refund.code} foi estornado e estará disponível em sua conta de acordo com o processamento do seu banco.</div>
                    </div>
                    <div><br></div>
                    <div style="font-size:14px">
                        <div>Dados do comprador:</div>
                        <div>Nome: ${subscriber.name}</div>
                        <div>${subscriber.document_type}: ${subscriber.document_number}</div>
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
                        <p style="font-size:12px">Gerado em ${formatDateTimePTBR(new Date())}</p><br>
                    </div>
                </div>
            `;

            return content;
        },
    }
};
</script>

<style scoped lang="scss">
.info {
    font-weight: 700;
}

.title {
    display: flex;
    width: 100%;
    color: #ffffff;
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 0.25rem;

    a {
        color: #ffffff;
        cursor: pointer;
        text-decoration: underline;
    }
}

.subtitle {
    display: flex;
    width: 100%;
    color: #C1C5CF;
    font-size: 0.875rem;
    font-weight: 400;
}
</style>
