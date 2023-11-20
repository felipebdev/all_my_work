<template>
    <xgrow-table-component :id="'content-table'">
        <template v-slot:title>
            <div
                class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                <div class="xgrow-table-header">
                    <h5 class="title">Transações Sem Limite: 30</h5>
                    <span class="subtitle">Veja todos as transações realizadas no sem limite.</span>
                </div>
                <div>
                    <div class="d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                        <div class="xgrow-input me-1 xgrow-input-search">
                            <input id="ipt-global-filter" placeholder="Pesquisa um coprodutor..." type="text"
                                   v-model="filter.searchValue">
                            <span class="xgrow-input-cancel">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                        </div>
                        <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv"
                                aria-bs-expanded="false" aria-bs-controls="collapseDiv"
                                class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                            <span>Filtros avançados
                                <i class="fa fa-chevron-down" aria-hidden="true"></i>
                            </span>
                        </button>
                        <div class="export-buttons d-none">
                            <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                <img src="/xgrow-vendor/assets/img/reports/txt.svg" alt="Exportar em CSV">
                            </button>
                            <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                <img src="/xgrow-vendor/assets/img/reports/xls.svg" alt="Exportar em XLSX">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:collapse>
            <div class="mb-3 collapse" id="collapseDiv">
                <div class="filter-container">
                    <div class="p-2 px-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 my-2">
                                <p class="title-filter">Filtros avançados</p>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                <div class="xgrow-form-control mb-2">
                                    <multiselect-component
                                        v-model="filter.product"
                                        :options="filter.products"
                                        :searchable="true"
                                        mode="tags"
                                        @select="null"
                                        @clear="null"
                                        placeholder="Digite ou selecione o nome do produto..."
                                    />
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                <div class="xgrow-form-control mb-2">
                                    <multiselect-component
                                        v-model="filter.productType"
                                        :options="filter.productTypes"
                                        @select="null"
                                        @clear="null"
                                        placeholder="Digite ou selecione o tipo do produto..."
                                    />
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                <div class="xgrow-form-control mb-2">
                                    <multiselect-component
                                        v-model="filter.statusPayment"
                                        :options="filter.statusPayments"
                                        :searchable="true"
                                        mode="tags"
                                        @select="null"
                                        @clear="null"
                                        placeholder="Digite ou selecione o status..."
                                    />
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-5 mb-3">
                                <div class="xgrow-form-control mb-2">
                                    <multiselect-component
                                        v-model="filter.methodPayment"
                                        :options="filter.methodPayments"
                                        :searchable="true"
                                        mode="tags"
                                        @select="null"
                                        @clear="null"
                                        placeholder="Digite ou selecione o método de pagamento..."
                                    />
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 mb-3">
                                <xgrow-daterange-component
                                    v-model:value="filter.dateRangeValue"
                                    format="DD/MM/YYYY"
                                    :clearable="false"
                                    type="date"
                                    range
                                    placeholder="Data do Pagamento"
                                    @change="null"/>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="chkHasCupom" type="checkbox"
                                           v-model="filter.hasCoupom">
                                    <label class="form-check-label" for="chkHasCupom">Mostrar apenas transações com
                                        cupom de desconto</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-slot:header>
            <th>Pedido</th>
            <th>Produto</th>
            <th>Cliente</th>
            <th>Nº parcelas</th>
            <th>Data de pgto.</th>
            <th>Status</th>
            <th>Método pgto.</th>
            <th>Comissão</th>
            <th></th>
        </template>
        <template v-slot:body>
            <tr v-if="results.length > 0" v-for="item in results" :key="item.id">
                <td>{{ item.transaction }}</td>
                <td>{{ item.product }}</td>
                <td>{{ item.name }}<br>{{ item.email }}</td>
                <td>{{ item.charges }}</td>
                <td>{{ $filters.formatDateTimeBR(item.paymentDate) }}</td>
                <td v-html="$filters.modifyPaymentStatus(item.status)"></td>
                <td>{{ item.paymentMethod }}</td>
                <td>{{ $filters.formatCurrency(item.commission) }}</td>
                <td></td>
            </tr>
            <tr v-else>
                <td colspan="6" class="xgrow-no-content">
                    Não há dados a serem exibidos.
                </td>
            </tr>
        </template>
        <template v-slot:footer>
            <xgrow-pagination-component
                class="mt-4"
                :total-pages="pagination.totalPages"
                :total="pagination.totalResults"
                :current-page="pagination.currentPage"
                @page-changed="onPageChange"
                @limit-changed="onLimitChange">
            </xgrow-pagination-component>
        </template>
    </xgrow-table-component>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import moment from "moment";

export default {
    name: "TransactionNoLimitComponent",
    components: {
        "xgrow-table-component": Table,
        "multiselect-component": Multiselect,
        "xgrow-daterange-component": DatePicker,
        "xgrow-pagination-component": Pagination
    },
    data() {
        return {
            filter: {
                searchValue: "",
                product: [],
                products: [
                    {value: 1, label: "Produto 1"},
                    {value: 2, label: "Produto 2"},
                    {value: 3, label: "Produto 3"}
                ],
                productType: [],
                productTypes: [
                    {value: 1, label: "Curso"},
                    {value: 2, label: "OrderBump"},
                    {value: 3, label: "UpSell"}
                ],
                statusPayment: [],
                statusPayments: [
                    {value: "success", label: "Realizada"},
                    {value: "fail", label: "Falha"}
                ],
                methodPayment: [],
                methodPayments: [
                    {value: "credit_card", label: "Cartão de Crédito"},
                    {value: "pix", label: "Pix"},
                    {value: "boleto", label: "Boleto"}
                ],
                dateRangeValue: null,
                dateRangeFormat: null,
                hasCoupom: false
            },
            results: [
                {
                    id: 1,
                    transaction: "6102AED0CA02E",
                    product: "Triad da Produtividade",
                    name: "Fernando Martins",
                    email: "fernando.martins@gmail.com",
                    charges: "1x",
                    paymentDate: new Date(),
                    status: "paid",
                    paymentMethod: "boleto",
                    commission: 263.00
                },
                {
                    id: 2,
                    transaction: "6102AED0CA02E",
                    product: "Triad da Produtividade",
                    name: "Fernando Martins",
                    email: "fernando.martins@gmail.com",
                    charges: "1x",
                    paymentDate: new Date(),
                    status: "paid",
                    paymentMethod: "boleto",
                    commission: 452.00
                },
                {
                    id: 3,
                    transaction: "6102AED0CA02E",
                    product: "Triad da Produtividade",
                    name: "Fernando Martins",
                    email: "fernando.martins@gmail.com",
                    charges: "3x",
                    paymentDate: new Date(),
                    status: "paid",
                    paymentMethod: "pix",
                    commission: 377.00
                }
            ],
            pagination: {
                totalPages: 1,
                totalResults: 1,
                currentPage: 1,
                limit: 25
            }
        };
    },
    methods: {
        /** Start Period component */
        startPeriod: function () {
            const startDate = new Date();
            const endDate = new Date(new Date().setDate(startDate.getDate() + 30));
            this.filter.dateRangeValue = [startDate, endDate];
            this.filter.dateRangeFormat = moment(startDate).format("DD/MM/YYYY") + " - " + moment(endDate).format("DD/MM/YYYY");
        },
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            // await this.search();
        }
    },
    async created() {
        this.startPeriod();
        this.pagination.totalResults = this.results.length;
    }
};
</script>

<style scoped lang="scss">
#ipt-global-filter {
    height: 40px;
}

.xgrow-table-header {
    font-family: Open Sans, serif;

    .title {
        font-weight: 600;
        font-size: 22px;
    }

    .subtitle {
        font-weight: 300;
        font-size: 16px;
    }
}

.title-filter {
    font-weight: 600;
    font-size: 18px;
}

.filter-container {
    border: none;
}

tr {
    height: 64px;

    td {
        vertical-align: middle;
    }
}
</style>
