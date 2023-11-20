<template>
    <XgrowTabContent id="subscriptionsPurchase" :selected="isActive">
        <XgrowTable id="subscriptionsPaymentInfo">
            <template v-slot:header>
                <th>Valor do plano</th>
                <th>Tarifa XGROW</th>
                <th>Valor líquido</th>
                <th>Cupom</th>
                <th>Total de pgto.</th>
                <th>Parcela do cliente</th>
                <th>Status da assinatura</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td>{{ plan_value }}</td>
                    <td>{{ rates_xgrow }}</td>
                    <td>{{ net_value }}</td>
                    <td>{{ coupon }}</td>
                    <td>{{ installments_paid }}</td>
                    <td>{{ plan_value }}</td>
                    <td>
                        <StatusBadge :status="subscription_status"/>
                    </td>
                </tr>
            </template>
        </XgrowTable>

        <XgrowTable id="noLimitClientInfo">
            <template v-slot:header>
                <th>Cliente</th>
                <th>Produto</th>
                <th>CPF/CNPJ</th>
                <th>Início da assinatura</th>
                <th>Último pgto.</th>
                <th>Periodicidade</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td>
                        <span class="title">
                            <a :href="`/subscribers/${subscribers_id}/edit`">
                                {{ subscribers_name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ subscribers_email }}</span>
                    </td>
                    <td>
                        <span class="title">
                            <a :href="`/products/${product_id}/plans`">
                                {{ product_name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ plan_name }}</span>
                    </td>
                    <td>{{ document_number }}</td>
                    <td><span v-html="formatDateTimeDualLine(start_of_subscription)"></span></td>
                    <td><span v-html="formatDateTimeDualLine(purchaseInformation['last payment'])"></span></td>
                    <td>{{ recurrence }}</td>
                </tr>
            </template>
        </XgrowTable>
    </XgrowTabContent>
</template>

<script>
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import XgrowTable from "../../../../../../js/components/Datatables/Table";
import StatusBadge from "../../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateTimeDualLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";

export default {
    name: "subscriptions-purchase-component",
    components: {StatusBadge, XgrowTable, XgrowTabContent},
    mixins: [formatDateTimeDualLine],
    props: {
        isActive: {
            type: Boolean,
            required: true,
        },
        purchaseInformation: {
            type: Object,
            default: {},
        },
    },
    data() {
        return {
            ...this.purchaseInformation,
        };
    },
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
