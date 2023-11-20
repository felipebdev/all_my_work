<template>
  <tr>
    <td class="order-number">{{ transaction.payment_order_number }}</td>
    <td>
      <span class="d-flex w-100 title">
        <a :href="`/subscribers/${transaction.subscribers_id}/edit`">{{
          transaction.subscribers_name
        }}</a>
      </span>
      <span class="d-flex w-100 subtitle">{{ transaction.subscribers_email }}</span>
    </td>
    <td class="product-name">
      <div class="d-flex w-100 title">
        <a :href="`/products/${transaction.product_id}/plans`">{{
          transaction.product_name
        }}</a>
        <TooltipComponent
          v-if="transaction.plans_type_plan !== 'P'"
          text="Cobrança recorrente"
          position="right"
          icon="<i class='fas fa-sync'></i>"
          class="ms-2"
        >
        </TooltipComponent>
      </div>
      <div class="d-flex w-100 subtitle">
        {{ transaction.plans_name }}
        <TooltipComponent
          v-if="transaction.payment_plan_type === 'order_bump'"
          text="Orderbump"
          position="right"
          class="ms-2"
        >
        </TooltipComponent>
      </div>
    </td>
    <td>{{ transaction.actual_installment }}</td>

        <td class="date">
            <div class="d-flex aling-items-center">
                <span v-html="formatDateSingleLine(transaction.subscriber_joining_date)"></span>
            </div>
        </td>

        <td :class="`date ${transaction.payment_confirmed_at !== null &&
            formatDateSingleLine(transaction.payment_payment_date) !==
            formatDateSingleLine(transaction.payment_confirmed_at) ? 'd-flex' : ''}`"
        >
            <span v-html="formatDateSingleLine(transaction.payment_payment_date)"></span>
            <TooltipComponent
                v-if="
                    transaction.payment_confirmed_at !== null &&
                    formatDateSingleLine(transaction.payment_payment_date) !== formatDateSingleLine(transaction.payment_confirmed_at)
                "
                :text="`O pagamento foi efetuado em ${formatDateSingleLine(transaction.payment_confirmed_at)}`"
                position="right"
                class="ms-2"
            />
        </td>

        <td :class="transaction.payment_refund_failed_at ? 'd-flex align-items-center gap-2' : ''">
            <StatusBadge :status="transaction.payment_plan_status"/>
            <Tooltip
                :id="`tooltip-${(Math.random() * 9999).toFixed()}`"
                v-if="transaction.payment_refund_failed_at"
                @click="toggleRefundFailModal(true)"
                icon='<i class="fas fa-info-circle"></i>'
                tooltip="Falha ao estornar pagamento"
            />
        </td>
        <td class="payment-method">
            <div class="content">
                {{ transaction.type_payment }}
                <TooltipComponent
                    v-if="transaction.payment_multiple_means !== 0"
                    icon="<i class='fas fa-credit-card'></i>"
                    text="Múltiplos cartões"
                    position="right"
                    class="ms-2"
                >
                </TooltipComponent>
            </div>
        </td>
        <td>{{ formatBRLCurrency(transaction.commission) }}</td>
        <td>
            <OptionsMenuComponent>
                <li class="option" v-if="transaction.payment_plan_status === 'failed'">
                    <button class="option-btn" @click="$emit('transactionRetry', transaction)">
                        <span class="material-symbols-outlined">autorenew</span> Realizar a cobrança novamente
                        ({{ transaction.remaining_tries }})
                    </button>
                </li>
                <li class="option">
                    <button class="option-btn"
                            @click.prevent="modalFunction(transaction)">
                        <span class="material-symbols-outlined">visibility</span>
                        Ver detalhes
                    </button>
                </li>
                <li class="option" v-if="transaction.subscribers_cel_phone">
                    <a :href="formatWhatsappLink(transaction.subscribers_cel_phone)" class="option-btn" target="_blank">
                        <span class="material-symbols-outlined">send</span>
                        Enviar mensagem no whatsapp
                    </a>
                </li>
            </OptionsMenuComponent>
        </td>
    </tr>
</template>

<script>
import TooltipComponent from "../../../../js/components/TooltipComponent.vue";
import OptionsMenuComponent from "../../../../js/components/OptionsMenuComponent.vue";
import StatusBadge from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Tooltip from "../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip.vue";
import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import formatDateSingleLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import formatWhatsappLink from "../../../../js/components/XgrowDesignSystem/Mixins/formatWhatsappLink";

export default {
  name: "transactions-table-row-component",
  components: { StatusBadge, TooltipComponent, OptionsMenuComponent, Tooltip },
  mixins: [
    formatDateTimeDualLine,
    formatBRLCurrency,
    formatWhatsappLink,
    formatDateSingleLine,
  ],
  props: {
    toggleRefundFailModal: {
      type: Function,
      default: () => {},
    },
    transaction: {
      type: Object,
      required: true,
    },
    modalFunction: {
      type: Function,
      required: true,
    },
  },
  computed: {
    product: function () {
      return {
        name: this.product_name.replaceAll(/\[R]$/g, ""),
        isRecurrent: this.product_name.includes("[R]"),
      };
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
  color: #c1c5cf;
  font-size: 0.875rem;
  font-weight: 400;
}
</style>
<style>
.fa-circle-info {
  color: var(--green4);
}
</style>
