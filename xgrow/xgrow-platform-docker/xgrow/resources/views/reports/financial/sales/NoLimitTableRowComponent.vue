<template>
    <tr>
        <td>
            <span class="d-flex w-100 title">
                <a :href="`/subscribers/${transaction.subscribers_id}/edit`">{{ transaction.subscribers_name }}</a>
            </span>
            <span class="d-flex w-100 subtitle">{{ transaction.subscribers_email }}</span>
        </td>
        <td>
            <div class="d-flex w-100 title">
                <a :href="`/products/${transaction.product_id}/plans`">{{ transaction.product_name }}</a>
            </div>
            <div class="d-flex w-100 subtitle">
                {{ transaction.plan_name }}
                <TooltipComponent
                    v-if="transaction.payment_order_bump !== null"
                    text="Orderbump"
                    position="right"
                    class="ms-2"
                >
                </TooltipComponent>
            </div>
        </td>
        <td>{{ formatBRLCurrency(transaction.payment_customer_value) }}</td>
        <td>{{ transaction.payment_installment_number }}/{{ transaction.payment_installments }}</td>
        <td><span v-html="formatDateTimeDualLine(transaction.payment_created_at)"></span></td>
        <td><span v-html="formatDateSingleLine(transaction.billing_date)"></span></td>
        <td><span v-html="formatDateTimeDualLine(transaction.cancellation_date)"></span></td>
        <td><span v-html="formatDateTimeDualLine(transaction.payment_confirmed_at)"></span></td>
        <td>
            <StatusBadge :status="transaction.subscription_status"/>
        </td>
        <td>
            <StatusBadge :status="transaction.payment_status"/>
        </td>
        <td>
            <OptionsMenuComponent>
                <li class="option">
                    <button class="option-btn"
                            @click.prevent="modalFunction(transaction)">
                        <i class="fas fa-eye"></i>
                        Ver detalhes
                    </button>
                </li>
                <li class="option" v-if="transaction.subscribers_cel_phone">
                    <a :href="formatWhatsappLink(transaction.subscribers_cel_phone)" class="option-btn" target="_blank">
                        <span class="material-symbols-outlined">send</span>
                        Enviar mensagem no whatsapp
                    </a>
                </li>
                <li class="option">
                    <button class="option-btn" @click.prevent="cancelRecurrence(transaction)">
                        <i class="fas fa-ban danger-icon"></i>
                        Cancelar recorrência
                    </button>
                </li>
            </OptionsMenuComponent>
        </td>
    </tr>
</template>

<script>
import TooltipComponent from "../../../../js/components/TooltipComponent.vue";
import OptionsMenuComponent from "../../../../js/components/OptionsMenuComponent.vue";
import StatusBadge from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import formatDateSingleLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import formatWhatsappLink from "../../../../js/components/XgrowDesignSystem/Mixins/formatWhatsappLink";

export default {
    name: "no-limit-table-row-component",
    mixins: [formatDateSingleLine, formatDateTimeDualLine, formatBRLCurrency, formatWhatsappLink],
    components: {
        StatusBadge,
        TooltipComponent,
        OptionsMenuComponent,
    },
    emits: ['cancelRecurrence'],
    props: {
        transaction: {
            type: Object,
            required: true,
        },
        modalFunction: {
            type: Function,
            required: true,
        }
    },
    methods: {
        cancelRecurrence: function (transaction) {
            this.$emit('cancelRecurrence', transaction)
        },
    },
};
</script>

<style scoped lang="scss">
.content {
    display: flex;
    height: 100%;
}

.title {
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
    color: #C1C5CF;
    font-size: 0.875rem;
    font-weight: 400;
}

.danger-icon {
    color: #F96C6C !important;
}
</style>
