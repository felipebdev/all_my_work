<template>
    <XgrowTabContent id="noLimitPayments" :selected="isActive">
        <XgrowTable id="noLimitPaymentsTable">
            <template v-slot:header>
                <th>Pedido</th>
                <th>Transação</th>
                <th>Produto</th>
                <th>Data pgto.</th>
                <th>Método pgto.</th>
                <th>Origem pgto.</th>
                <th>Valor</th>
                <th>Valor líquido</th>
                <th>Status</th>
                <th></th>
            </template>
            <template v-slot:body>
                <tr v-for="(payment, idx) in recurrencePayments" :key="`${idx}-${payment.order_number}`">
                    <td>{{ payment.order_number }}</td>
                    <td>{{ payment.order_code || '-' }}</td>
                    <td>
                        <span class="title">
                            <a :href="`/products/${payment.product_id}/plans`">
                                {{ payment.name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ payment.plan_name }}</span>
                    </td>
                    <td><span v-html="formatDateSingleLine(payment.payment_date)"></span></td>
                    <td>{{ payment.type_payment }}</td>
                    <td>{{ payment.payment_source || '-' }}</td>
                    <td><span v-html="formatBRLCurrency(payment.payment_plan_plan_price)"></span></td>
                    <td><span v-html="formatBRLCurrency(payment.payment_plan_customer_value)"></span></td>
                    <td>
                        <StatusBadge :status="payment.status"/>
                    </td>
                    <td>
                        <OptionsMenuComponent v-if="payment.status === 'paid'">
                            <li class="option">
                                <button class="option-btn"
                                        @click.prevent="openPaidProofModal({paymentId: payment.id, ...payment})">
                                    <span class="material-symbols-outlined">send</span>
                                    Reenviar comprovante de compra
                                </button>
                            </li>
                            <li class="option">
                                <button class="option-btn" @click.prevent="openModal(payment)">
                                    <i class="fas fa-undo"></i>
                                    Estornar este pagamento
                                </button>
                            </li>
                            <li class="option">
                                <button class="option-btn" @click.prevent="openModal(payment, false)">
                                    <i class="fas fa-undo"></i>
                                    Estornar todos os pagamentos
                                </button>
                            </li>
                        </OptionsMenuComponent>
                    </td>
                </tr>
            </template>
        </XgrowTable>

        <RefundModal
            :is-open="isOpen && modal === null"
            :close-function="closeModal"
            :modal-data="modalData"
            :refund-function="refund"
        >
        </RefundModal>
        <PaymentProofModal
            :is-open="isOpen && modal === 'paid-proof'" :close-function="closeModal"
            :modal-data="modalData">
        </PaymentProofModal>
    </XgrowTabContent>
</template>

<script>
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import OptionsMenuComponent from "../../../../../../js/components/OptionsMenuComponent.vue";
import XgrowTable from "../../../../../../js/components/Datatables/Table";
import RefundModal from "./RefundModal.vue";
import PaymentProofModal from "./PaymentProofModal";
import axios from 'axios';
import StatusBadge from "../../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateSingleLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatBRLCurrency from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";

export default {
    name: "no-limit-payments-component",
    mixins: [formatDateSingleLine, formatBRLCurrency],
    components: {
        StatusBadge,
        XgrowTable,
        XgrowTabContent,
        OptionsMenuComponent,
        RefundModal,
        PaymentProofModal,
    },
    props: {
        isActive: {
            type: Boolean,
            required: true
        },
        recurrencePayments: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            /** Modal */
            isOpen: false,
            modalData: {},
            modal: null,
        }
    },
    methods: {
        /** Modal */
        openModal: function (value, single = true) {
            this.modalData = value;
            this.modalData.single = single;
            this.isOpen = true;
        },
        closeModal: function () {
            this.modalData = {};
            this.isOpen = false;
            this.modal = null
        },
        openPaidProofModal: function (value) {
            this.modalData = value;
            this.isOpen = true;
            this.modal = 'paid-proof';
        },
        /** Refunds functions */
        refund(type, payment_plan_id, data) {
            axios
                .post(postRefundURL, {
                    type: type.toString(),
                    payment_plan_id: payment_plan_id.toString(),
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
    },
}
</script>

<style scoped lang="scss">
:deep(.xgrow-table) {
    font-size: 0.875rem !important;
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
