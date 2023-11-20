<template>
    <XgrowTabContent id="transactionsSales" :selected="isActive">
        <Table id="tableProductInfo">
            <template v-slot:header>
                <th>Produto</th>
                <th>Valor do plano</th>
                <th>Tarifa XGROW</th>
                <th>Valor líquido</th>
                <th>Cupom</th>
                <th>Parcelas pagas</th>
                <th>Data da transação</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td>
                        <span class="title">
                            <a :href="`/products/${product_id}/plans`">{{ product_name }}</a>
                                <TooltipComponent
                                    v-if="product.isRecurrent"
                                    text="Cobrança recorrente"
                                    position="right"
                                    icon="<i class='fas fa-sync'></i>"
                                    class="ms-2">
                                </TooltipComponent>
                            </span>
                        <span class="subtitle">{{ plan_name }}</span>
                    </td>
                    <td>{{ plan_value }}</td>
                    <td>{{ rates_xgrow }}</td>
                    <td>{{ net_value }}</td>
                    <td>{{ coupon }}</td>
                    <td>{{ installments_paid }}</td>
                    <td>
                        <span>{{formatDateSingleLine(transaction_date)}}</span>
                    </td>
                </tr>
            </template>
        </Table>

        <Table id="tableClientInfo">
            <template v-slot:header>
                <th>Cliente</th>
                <th>CPF/CNPJ</th>
            </template>
            <template v-slot:body>
                <tr>
                    <td class="w-50">
                        <div class="title">
                            <a :href="`/subscribers/${subscribers_id}/edit`">{{ subscribers_name }}</a>
                        </div>
                        <div class="subtitle">{{ subscribers_email }}</div>
                    </td>
                    <td>{{ document_number }}</td>
                </tr>
            </template>
        </Table>
    </XgrowTabContent>
</template>

<script>
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import TooltipComponent from "../../../../../../js/components/TooltipComponent.vue";
import Table from "../../../../../../js/components/Datatables/Table.vue";
import formatDateSingleLine from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";

export default {
    name: "transactions-sales-component",
    components: {XgrowTabContent, TooltipComponent, Table},
    mixins: [formatDateSingleLine],
    props: {
        isActive: {
            type: Boolean,
            required: true
        },
        saleInformation: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            ...this.saleInformation,
        }
    },
    computed: {
        product: function () {
            return {
                name: this.product_name.replaceAll(/\[R]$/g, ''),
                isRecurrent: this.product_name.includes('[R]')
            }
        }
    },
}
</script>

<style scoped lang="scss">
.info {
    font-weight: 700;
    display: flex;
    align-items: center;
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
