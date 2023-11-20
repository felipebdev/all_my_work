<template>
    <XgrowTabContent id="noLimitInformation" :selected="isActive">

        <XgrowTable id="noLimitPaymentInfo">
            <template v-slot:header>
                <th>Valor do plano</th>
                <th>Tarifa XGROW</th>
                <th>Valor líquido</th>
                <th>Cupom</th>
                <th>Parcelas pagas</th>
                <th>Parcela do cliente</th>
                <th>Status da assinatura</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td>{{ purchaseInformation.plan_value }}</td>
                    <td>{{ purchaseInformation.rates_xgrow }}</td>
                    <td>{{ purchaseInformation['net value'] }}</td>
                    <td>{{ purchaseInformation.coupon }}</td>
                    <td>{{ purchaseInformation.installments_paid }}</td>
                    <td>{{ purchaseInformation.payment_plan_plan_price }}</td>
                    <td>
                        <StatusBadge :status="purchaseInformation.payment_status"/>
                    </td>
                </tr>
            </template>
        </XgrowTable>

        <XgrowTable id="noLimitClientInfo">
            <template v-slot:header>
                <th>Cliente</th>
                <th>Produto</th>
                <th>CPF/CNPJ</th>
                <th>Início de recorrência</th>
                <th>Último pgto.</th>
                <th>Término da recorrência</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td>
                        <span class="title">
                            <a :href="`/subscribers/${purchaseInformation.subscribers_id}/edit`">
                                {{ purchaseInformation.subscribers_name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ purchaseInformation.subscribers_email }}</span>
                    </td>
                    <td>
                        <span class="title">
                            <a :href="`/products/${purchaseInformation.product_id}/plans`">
                                {{ purchaseInformation.product_name }}
                            </a>
                        </span>
                        <span class="subtitle">{{ purchaseInformation.plan_name }}</span>
                    </td>
                    <td>{{ purchaseInformation.subscribers_document_number }}</td>
                    <td><span v-html="formatDateSingleLine(purchaseInformation.start_recurrence)"></span></td>
                    <td><span v-html="formatDateSingleLine(purchaseInformation.last_payment)"></span></td>
                    <td><span v-html="formatDateSingleLine(purchaseInformation.end_recurrence)"></span></td>
                </tr>
            </template>
        </XgrowTable>
    </XgrowTabContent>
</template>

<script>
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import TooltipComponent from "../../../../../../js/components/TooltipComponent";
import XgrowTable from "../../../../../../js/components/Datatables/Table";
import StatusBadge from "../../../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import formatDateSingleLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";

export default {
    name: "no-limit-information-component",
    mixins: [formatDateSingleLine],
    components: {
        StatusBadge,
        XgrowTable,
        TooltipComponent,
        XgrowTabContent,
    },
    props: {
        isActive: {
            type: Boolean,
            required: true
        },
        purchaseInformation: {
            type: Object,
            default: null
        }
    },
}
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
