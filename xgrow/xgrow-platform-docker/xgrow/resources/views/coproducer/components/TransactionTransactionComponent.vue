<template>
<div>
    <StatusModalComponent :is-open="loading.active" :status="loading.status"/>
    <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
        <FinancialCard
            :title="$filters.formatCurrency(stats.commission)"
            subtitle="Comissão líquida"
            border-color="#4DA2D1"
        />
        <FinancialCard
            :title="$filters.formatCurrency(stats.pending)"
            subtitle="Pendente"
            border-color="#F4E558"
        />
        <FinancialCard
            :title="$filters.formatCurrency(stats.refunded)"
            subtitle="Estornado"
            border-color="#F45858"
        />
        <FinancialCard
            :title="$filters.formatCurrency(stats.chargeback)"
            subtitle="Chargeback"
            border-color="#D6D6D6"
        />
    </div>
    <div class="xgrow-card card-dark">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-lg-12">
                <Table :id="'transactionTable'">
                    <template v-slot:title>
                        <div
                            class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                            <div class="xgrow-table-header">
                                <h5 class="title">Transações: {{ pagination.totalResults }}</h5>
                                <span class="subtitle">Veja todos as transações realizadas.</span>
                            </div>
                            <div class="d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                                <button
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDivTransaction"
                                    aria-bs-expanded="false" aria-bs-controls="collapseDiv"
                                    class="xgrow-button-filter xgrow-button export-button me-1"
                                    aria-expanded="true">
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
                    </template>
                    <template v-slot:collapse>
                        <div class="mb-3 collapse" id="collapseDivTransaction">
                            <div class="filter-container">
                                <div class="p-2 px-3">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 my-2">
                                            <p class="title-filter">
                                                <i class="fas fa-filter"></i>Filtros avançados
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                            <div class="xgrow-form-control mb-2">
                                                <Multiselect
                                                    v-model="filter.productType"
                                                    :options="filter.productTypes"
                                                    mode="tags"
                                                    @select="changeFilter"
                                                    @clear="remove"
                                                    @deselect="changeFilter"
                                                    placeholder="Selecione o tipo do produto..."
                                                />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                            <div class="xgrow-form-control mb-2">
                                                <Multiselect
                                                    v-model="filter.statusPayment"
                                                    :options="filter.statusPayments"
                                                    :searchable="true"
                                                    mode="tags"
                                                    @select="changeFilter"
                                                    @clear="changeFilter"
                                                    @deselect="changeFilter"
                                                    placeholder="Digite ou selecione o status..."
                                                />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 mb-3">
                                            <div class="xgrow-form-control mb-2">
                                                <Multiselect
                                                    v-model="filter.methodPayment"
                                                    :options="filter.methodPayments"
                                                    :searchable="true"
                                                    mode="tags"
                                                    @select="changeFilter"
                                                    @clear="changeFilter"
                                                    @deselect="changeFilter"
                                                    placeholder="Digite ou selecione o método de pagamento..."
                                                />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                                            <DatePicker
                                                v-model:value="filter.dateRangeValue"
                                                format="DD/MM/YYYY"
                                                :clearable="true"
                                                type="date"
                                                range
                                                placeholder="Data do Pagamento"
                                                @change="searchByDate"/>
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
                        <template v-if="results.length > 0" >
                            <tr v-for="item in results" :key="item.id">
                                <td>{{ item.order_code }}</td>
                                <td>{{ item.product_name }}</td>
                                <td>{{ item.client_name }}<br>{{ item.client_email }}</td>
                                <td>{{ item.installments }}</td>
                                <td>{{ $filters.formatDateTimeBR(item.payment_date) }}</td>
                                <td v-html="$filters.modifyPaymentStatus(item.payment_status)"></td>
                                <td v-html="$filters.modifyPaymentMethod(item.payment_method)"></td>
                                <td>{{ $filters.formatCurrency(item.commission) }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="xgrow-button table-action-button m-1" type="button"
                                                :id="'dropdownMenuButton'+[[item.id]]" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu table-menu xgrow-dropdown-menu"
                                            :aria-labelledby="'dropdownMenuButton'+[[item.id]]">
                                            <li>
                                                <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                                @click="showDetails(item.id)">
                                                    Ver detalhes
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="9" class="xgrow-no-content">
                                Não há dados a serem exibidos.
                            </td>
                        </tr>
                    </template>
                    <template v-slot:footer>
                        <Pagination
                            class="mt-4"
                            :total-pages="pagination.totalPages"
                            :total="pagination.totalResults"
                            :current-page="pagination.currentPage"
                            @page-changed="onPageChange"
                            @limit-changed="onLimitChange">
                        </Pagination>
                    </template>
                </Table>

                <ModalComponent :is-open="detailModal" @close="detailModal=false" modal-size="xl">
                    <template v-slot:title>
                        <h5 class="m-0 p-0 mx-3">Detalhes da transação de: <u>{{ detailResults.client_name }}</u></h5>
                    </template>
                    <template v-slot:content>
                        <div class="row w-100">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <Table :id="'transactionDetailTable'">
                                    <template v-slot:title>
                                        <div
                                            class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                                            <div class="xgrow-table-header">
                                                <h5 class="title">Informações de pagamento</h5>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-slot:header>
                                        <th>Produto</th>
                                        <th>Plano</th>
                                        <th>Valor do plano</th>
                                        <th>Comissão</th>
                                    </template>
                                    <template v-slot:body>
                                        <tr>
                                            <td>{{ detailResults.product }}</td>
                                            <td>{{ detailResults.plan }}</td>
                                            <td>{{ $filters.formatCurrency(detailResults.plan_value) }}</td>
                                            <td>{{ $filters.formatCurrency(detailResults.commission) }}</td>
                                        </tr>
                                    </template>
                                </Table>
                            </div>
                        </div>
                    </template>
                    <template v-slot:footer="slotProps">
                        <button type="button" class="btn btn-success" @click="slotProps.closeModal">Voltar</button>
                    </template>
                </ModalComponent>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import ModalComponent from "../../../js/components/ModalComponent";
import FinancialCard from "../../../js/components/XgrowDesignSystem/Cards/FinancialCard.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

import moment from "moment";
import axios from "axios";
import StatusModalComponent from "../../../js/components/StatusModalComponent";

export default {
    name: "TransactionNoLimitComponent",
    components: {
        Table,
        Multiselect,
        DatePicker,
        Pagination,
        ModalComponent,
        FinancialCard,
        StatusModalComponent
    },
    props: {
        platformId: {default: null}
    },
    data() {
        return {
            loading: {
                active: false,
                status: "loading"
            },
            filter: {
                searchValue: "",
                productType: [],
                productTypes: [
                    {value: 'R', label: "Assinatura"},
                    {value: 'P', label: "Venda única"},
                    {value: 'U', label: 'Venda Sem Limite'},
                ],
                statusPayment: [],
                statusPayments: [
                    {value: "paid", label: "Pago"},
                    {value: "pending", label: "Pendente"},
                    {value: "canceled", label: "Cancelado"},
                    {value: "chargeback", label: "Chargeback"},
                    {value: 'failed', label: 'Falha no pagamento'},
                    {value: 'expired', label: 'Expirado'},
                    {value: 'refunded', label: 'Estornado'},
                ],
                methodPayment: [],
                methodPayments: [
                    {value: "credit_card", label: "Cartão de Crédito"},
                    {value: "pix", label: "Pix"},
                    {value: "boleto", label: "Boleto"}
                ],
                dateRangeValue: null,
                hasCoupom: false
            },
            results: [],
            detailResults: {
                product: "",
                plan: "",
                plan_value: 0,
                commission: 0,
                client_name: ""
            },
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25
            },
            detailModal: false,
            stats: {
                commission: 0,
                pending: 0,
                refunded: 0,
                chargeback: 0
            },
        };
    },
    methods: {
        async remove() {
            this.filter.productType = []
            this.pagination.currentPage = 1;
            await this.getTransactionList()
        },
        async changeFilter() {
            this.pagination.currentPage = 1;
            await this.getTransactionList()
        },
        async searchByDate() {
            this.pagination.currentPage = 1;
            await this.getTransactionList();
        },
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getTransactionList();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            this.pagination.currentPage = 1;
            await this.getTransactionList();
        },
        /** Show transaction detail */
        showDetails: async function (id) {
            this.loading.active = true;
            try {
                let transactionDetailsUrl = transactionDetailsURL.replace(/:platformId/g, this.platformId);
                const res = await axios.post(transactionDetailsUrl, {id_sale: id});
                this.detailResults = res.data.response.data;
                this.loading.active = false;
                this.detailModal = true;
            } catch (e) {
                errorToast('Ocorreu um erro.', e.response.data.message);
                this.loading.active = false;
            }
        },
        /** Get withdraw list */
        getTransactionList: async function (platformId = null) {
            this.loading.active = true;
            platformId = platformId === null ? this.platformId : platformId;
            try {

                const params = {
                    page: this.pagination.currentPage,
                    offset: this.pagination.limit,
                    payment_method: this.filter.methodPayment,
                    payment_status: this.filter.statusPayment,
                    product_type: this.filter.productType
                }

                if (this.filter.dateRangeValue !== null && this.filter.dateRangeValue[0] !== null) {
                    const firstDate = new Date(this.filter.dateRangeValue[0]).toISOString('pt-br').substring(0, 10)
                    const secondDate = new Date(this.filter.dateRangeValue[1]).toISOString('pt-br').substring(0, 10)
                    params.period = []
                    params.period[0] = `${firstDate} 00:00:00`
                    params.period[1] = `${secondDate} 23:59:59`
                }

                let transactionListUrl = transactionListURL.replace(/:platformId/g, platformId);
                const res = await axios.get(transactionListUrl, { params });
                const data = res.data.response;
                this.stats.commission = data.commission;
                this.stats.pending = data.pending;
                this.stats.refunded = data.refunded;
                this.stats.chargeback = data.chargeback;
                this.results = data.transactions.data;
                this.pagination.currentPage = data.transactions.current_page;
                this.pagination.limit = data.transactions.per_page;
                this.pagination.totalPages = data.transactions.last_page;
                this.pagination.totalResults = data.transactions.total;
                this.loading.active = false;
            } catch (e) {
                console.log("error:", e);
                this.loading.active = false;
            }
        }
    },
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

u {
    color: #93bc1e;
}

.xgrow-button-filter {
    min-width: 200px;
    max-width: 310px;
}

.fa-chevron-down {
    margin-left: 0.625rem;
}

.fa-filter {
    color: #ADDF45;
    margin-right: 5px;
}
</style>
