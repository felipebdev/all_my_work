<template>
    <tr>
        <td>
            <p class="title">
                <a :href="`/subscribers/${subscription.subscribers_id}/edit`">
                    {{ subscription.subscribers_name }}
                </a>
            </p>
            <p class="subtitle">{{ subscription.subscribers_email }}</p>
        </td>
        <td>
            <p class="title">
                <a :href="`/products/${subscription.product_id}/plans`">
                    {{ subscription.product_name }}
                </a>
            </p>
            <p class="subtitle">{{ subscription.plans_name }}</p>
        </td>
        <td>
            <span>{{ formatBRLCurrency(subscription.customer_value) }}</span>
        </td>
        <td>
            <span v-html="formatDateTimeDualLine(subscription.created_at)"></span>
        </td>
        <td>
            <span v-html="formatDateTimeDualLine(subscription.cancellation_date)"></span>
        </td>
        <td>
            <span>{{ formatDateSingleLine(subscription.last_payment) }}</span>
        </td>
        <td>
            <StatusBadge :status="subscription.subscription_status"/>
        </td>
        <td>
            <OptionsMenuComponent>
                <li class="option">
                    <button class="option-btn" @click.prevent="getSubscriptionData(subscription)">
                        <i class="fas fa-eye"></i>Ver detalhes
                    </button>
                </li>
                <li class="option">
                    <a :href="formatWhatsappLink(subscription.subscribers_cel_phone)" target="_blank"
                       class="option-btn">
                        <span class="material-symbols-outlined">send</span>
                        Enviar mensagem no whatsapp
                    </a>
                </li>
            </OptionsMenuComponent>
        </td>
    </tr>
</template>

<script>
import StatusBadge from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import OptionsMenuComponent from "../../../../js/components/OptionsMenuComponent";
import axios from "axios";
import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import formatWhatsappLink from "../../../../js/components/XgrowDesignSystem/Mixins/formatWhatsappLink";
import formatDateSingleLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";

export default {
    name: "SubscriptionRow",
    components: {OptionsMenuComponent, StatusBadge},
    mixins: [formatDateTimeDualLine, formatBRLCurrency, formatWhatsappLink, formatDateSingleLine],
    props: {subscription: {type: Object, required: true}},
    emits: ['loading', 'subscriptionModal'],
    methods: {
        /** Modal and API Functions */
        getSubscriptionData: async function (subscription) {
            try {
                this.$emit("loading", true);
                const res = await axios.get(`${getSubscriptionsURL}/${subscription.subscribers_id}/${subscription.plans_id}/${subscription.payment_order_number}`);

                if (res.data.error === true) {
                    this.$emit("endLoading");
                    errorToast("Algum erro aconteceu!", "Não foi possível carregar os dados da transação, entre em contato com o suporte.");
                    return;
                }

                this.$emit("loading", false);
                this.$emit("subscriptionModal", res.data.response[0].response);
            } catch (e) {
                this.$emit("loading", false);
                errorToast("Algum erro aconteceu!", "Não foi possível carregar os dados da transação, entre em contato com o suporte.");
            }
        },
    }
}
</script>

<style lang="scss" scoped>
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
</style>
