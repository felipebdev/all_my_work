<template>
    <XgrowTabContent id="transactionsComission" :selected="isActive">
        <Table id="transactionsYourComissionTable">
            <template v-slot:header>
                <th class="th-1">Valor líquido da transação</th>
                <th>Sua comissão</th>
            </template>
            <template v-slot:body>
                <tr v-if="producer !== null">
                    <td>{{ formatBRLCurrency(producer.net_value) }}</td>
                    <td>{{ formatBRLCurrency(producer.commission) }}</td>
                </tr>
                <tr v-else>
                    <td colspan="2" class="text-center">Não há dados a serem exibidos</td>
                </tr>
            </template>
        </Table>

        <Table id="transactionsCoproducerComissionTable">
            <template v-slot:header>
                <th class="th-1">Valor líquido da transação</th>
                <th class="th-2">Coprodutor</th>
                <th>Comissão do coprodutor</th>
            </template>
            <template v-slot:body>
                <tr v-for="(coproducer, idx) in co_producers" :key="idx">
                    <td>{{ formatBRLCurrency(coproducer.net_value) }}</td>
                    <td>{{ coproducer.name }}</td>
                    <td>{{ formatBRLCurrency(coproducer.commission) }}</td>
                </tr>
                <tr v-if="co_producers.length < 1">
                    <td colspan="3" class="text-center">Não há dados a serem exibidos</td>
                </tr>
            </template>
        </Table>

        <Table id="transactionsCoproducerComissionTable">
            <template v-slot:header>
                <th class="th-1">Valor líquido da transação</th>
                <th class="th-2">Afiliado</th>
                <th>Comissão do afiliado
                    <Tooltip
                        v-if="payment_multiple"
                        :has-info="true"
                        tooltip="Em múltiplos cartões a comissão corresponde ao valor proporcional do cartão selecionado. Para comissão total da venda considere também a comissão dos outros cartões."
                        id="affiliateComssion"
                        icon="<i class='fas fa-circle-info'></i>"
                    />
                </th>
            </template>
            <template v-slot:body>
                <tr v-for="(affiliate, idx) in affiliates" :key="idx">
                    <td>{{ formatBRLCurrency(affiliate.net_value) }}</td>
                    <td>{{ affiliate.name }}</td>
                    <td>{{ formatBRLCurrency(affiliate.commission) }}</td>
                </tr>
                <tr v-if="affiliates.length < 1">
                    <td colspan="3" class="text-center">Não há dados a serem exibidos</td>
                </tr>
            </template>
        </Table>
    </XgrowTabContent>
</template>

<script>
import XgrowTabContent from "../../../../../../js/components/XgrowDesignSystem/Tab/XgrowTabContent.vue";
import Tooltip from "../../../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip.vue";
import Table from "../../../../../../js/components/Datatables/Table.vue";
import formatBRLCurrency from "../../../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";

export default {
    name: "transactions-comissions-component",
    components: {XgrowTabContent, Table, Tooltip},
    mixins: [formatBRLCurrency],
    props: {
        isActive: {
            type: Boolean,
            required: true
        },
        commissions: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            ...this.commissions,
        }
    },
}
</script>

<style scoped lang="scss">
.th-1{
    width: 235px;
}
.th-2{
    width: 250px;
}
</style>
