<template>
    <div class="row mb-3">
        <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
            <FinancialCard border-color="#adff2f" :title="metrics.paid || formatBRLCurrency(0)"
                subtitle="Faturamento total">
                <XgrowTooltip id="transaction_paid" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="Valor total das vendas realizadas" />
            </FinancialCard>

            <FinancialCard border-color="#24cefe" :title="metrics.commission || formatBRLCurrency(0)"
                subtitle="Minha comissão">
                <XgrowTooltip id="transaction_commission" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="Valor percentual recebido na distribuição das vendas" />
            </FinancialCard>

            <FinancialCard border-color="#ffff00" :title="metrics.pending || formatBRLCurrency(0)" subtitle="Pendente">
                <XgrowTooltip id="transaction_pending" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="Boletos e pix gerados que ainda não foram pagos" />
            </FinancialCard>

            <FinancialCard border-color="#ff9900" :title="metrics.late || formatBRLCurrency(0)"
                subtitle="Falha no pagamento">
                <XgrowTooltip id="transaction_late" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="Transações sem limite com erro na cobrança" />
            </FinancialCard>

            <FinancialCard border-color="#e22222" :title="metrics.refunded || formatBRLCurrency(0)" subtitle="Estornado">
                <XgrowTooltip id="transaction_refunded" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="A soma dos valores devolvidos para os clientes" />
            </FinancialCard>

            <FinancialCard border-color="#f5f6fa" :title="metrics.chargeback || formatBRLCurrency(0)" subtitle="Chargeback">
                <XgrowTooltip id="transaction_chargeback" icon='<i class="fas fa-info-circle"></i>'
                    tooltip="Pagamentos estornados direto pelo banco" />
            </FinancialCard>
        </div>
    </div>

    <div class="xgrow-card card-dark">
        <XgrowTable id="salesTransactionsTable" min-height sortable :table-header="tableHeader" :sort="sortTable"
            :order="order" @sort-table="sortTable">
            <template v-slot:title>
                <div class="d-flex align-items-center gap-1 flex-wrap justify-content-md-between w-100 slot-header">
                    <div class="xgrow-table-header">
                        <h5 class="title">
                            Transações:
                            {{ pagination.totalResults }}
                            <span v-show="filterTitle">| </span>
                            <span class="filters" v-html="filterTitle" /><br />
                        </h5>
                        <span>Veja todas as transações realizadas.</span>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between py-2 gap-2 flex-wrap w-100">
                    <div class="d-flex gap-3 align-items-center flex-wrap">
                        <Input id="search-field" placeholder="Pesquise nome ou e-mail..."
                            icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue"
                            style="min-width: 200px; max-width: 310px" />
                        <FilterButton target="filterSalesTransactions" />
                    </div>
                    <div class="d-flex gap-3">
                        <ExportLabel>
                            <IconButton @click="exportReport('csv')" img-src="/xgrow-vendor/assets/img/reports/csv.svg"
                                title="Exportar em CSV" />
                            <IconButton @click="exportReport('xlsx')" img-src="/xgrow-vendor/assets/img/reports/xls.svg"
                                title="Exportar em XLSX" />
                        </ExportLabel>
                    </div>
                </div>
            </template>
            <template v-slot:collapse>
                <div class="mb-3 collapse" id="filterSalesTransactions">
                    <div class="filter-container">
                        <div class="p-2 px-3">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 my-2">
                                    <p class="title-filter">
                                        <i class="fas fa-filter"></i> Filtros avançados
                                    </p>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <div class="xgrow-form-control">
                                        <Multiselect :options="filter.product.options" v-model="filter.product.selected"
                                            :searchable="true" mode="tags" placeholder="Produto" @select="filterProducts"
                                            :canClear="false" @deselect="filterProducts">
                                            <template v-slot:noresults>
                                                <p class="multiselect-option" style="opacity: 0.5">
                                                    Produto não encontrado...
                                                </p>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <div class="xgrow-form-control">
                                        <Multiselect :options="filter.plans.options" v-model="filter.plans.selected"
                                            :searchable="true" mode="tags" placeholder="Plano" @select="changeFilter"
                                            :canClear="false" @deselect="changeFilter" :disabled="!showPlans"
                                            :title="plansTitle">
                                            <template v-slot:noresults>
                                                <p class="multiselect-option" style="opacity: 0.5">
                                                    Plano não encontrado...
                                                </p>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <div class="xgrow-form-control mb-2">
                                        <Multiselect :options="filter.type.options" v-model="filter.type.selected"
                                            :searchable="true" mode="tags" placeholder="Tipo" @select="changeFilter"
                                            :canClear="false" @deselect="changeFilter">
                                            <template v-slot:noresults>
                                                <p class="multiselect-option" style="opacity: 0.5">
                                                    Tipo não encontrado...
                                                </p>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <div class="xgrow-form-control mb-2">
                                        <Multiselect :options="activeStatusOptions" v-model="filter.status.selected"
                                            :searchable="true" mode="tags" placeholder="Status" @select="changeFilter"
                                            :canClear="false" @deselect="changeFilter">
                                            <template v-slot:noresults>
                                                <p class="multiselect-option" style="opacity: 0.5">
                                                    Status não encontrado...
                                                </p>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <div class="xgrow-form-control mb-2">
                                        <Multiselect :options="filter.method.options" v-model="filter.method.selected"
                                            :searchable="true" mode="tags" placeholder="Método de pagamento..."
                                            @select="changeFilter" :canClear="false" @deselect="changeFilter">
                                            <template v-slot:noresults>
                                                <p class="multiselect-option" style="opacity: 0.5">
                                                    Método de pagamento não encontrado...
                                                </p>
                                            </template>
                                        </Multiselect>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <DatePicker v-model:value="filter.daterange.value" format="DD/MM/YYYY" :clearable="true"
                                        type="date" range placeholder="Data de cobrança" @change="searchByDate" />
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3" v-if="showInstallmentPaid">
                                    <Input id="installmentPaid" label="Número da parcela"
                                        placeholder="Busque pelo número da parcela desejada..."
                                        :mask="'#################################'" type="number"
                                        @input="searchByInstallmentPaidNumber" v-model="filter.installmentPaid" />
                                </div>

                                <div class="col-lg-12 col-md-12 col-sm-12 my-3">
                                    <p><b>Opções de visualização</b></p>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                    <SwitchButton v-model="filter.showCouponOnly" @change.prevent="changeFilter"
                                        style="margin-left: 0 !important">
                                        Mostrar apenas transações com cupom de desconto
                                    </SwitchButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:body>
                <TransactionsTableRowComponent v-for="(transaction, index) in results" :key="index"
                    :transaction="transaction" :modal-function="openModal" @transaction-retry="transactionRetry"
                    :toggle-refund-fail-modal="toggleRefundFailModal"
                >
                </TransactionsTableRowComponent>
                <tr v-if="results.length < 1">
                    <td colspan="13" class="text-center">Não há dados a serem exibidos</td>
                </tr>
            </template>
            <template v-slot:footer>
                <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                    :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                </Pagination>
            </template>
        </XgrowTable>
    </div>

    <TransactionsModalComponent :is-open="isOpen" :close-function="closeModal" :modal-data="modalData"
        :get-transactions="getTransactions">
    </TransactionsModalComponent>

    <RetryPaymentModal :is-open="retryModal.isOpen" :close-function="closeRetryModal" :modal-data="retryModal.modalData">
    </RetryPaymentModal>
    <RefundFail :is-open="isOpenRefundFailModal" :toggle="toggleRefundFailModal"/>
</template>

<script>
import RefundFail from "../../../../js/components/XgrowDesignSystem/Modals/RefundFail.vue";
import Pagination from "../../../../js/components/Datatables/Pagination.vue";
import Input from "../../../../js/components/XgrowDesignSystem/Input.vue";
import SwitchButton from "../../../../js/components/XgrowDesignSystem/SwitchButton.vue";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import TransactionsTableRowComponent from "./TransactionsTableRowComponent.vue";
import TransactionsModalComponent from "./modal/TransactionsModalComponent.vue";
import axios from "axios";
import moment from "moment";
import FinancialCard from "../../../../js/components/XgrowDesignSystem/Cards/FinancialCard";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import IconButton from "../../../../js/components/XgrowDesignSystem/Buttons/IconButton";
import ExportLabel from "../../../../js/components/XgrowDesignSystem/Utils/ExportLabel";
import XgrowTable from "../../../../js/components/Datatables/Table";
import formatBRLCurrency from "../../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import XgrowTooltip from "../../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip";
import RetryPaymentModal from "./modal/RetryPaymentModalComponent";

export default {
    name: "transactions-component",
    mixins: [formatBRLCurrency],
    components: {
        RetryPaymentModal,
        XgrowTooltip,
        XgrowTable,
        ExportLabel,
        IconButton,
        FilterButton,
        FinancialCard,
        Pagination,
        TransactionsTableRowComponent,
        TransactionsModalComponent,
        Input,
        SwitchButton,
        Multiselect,
        DatePicker,
        RefundFail
  },
    emits: ["startLoading", "endLoading"],
    data() {
        return {
            isOpenRefundFailModal: false,
            showPlans: false,
            /** Modal */
            isOpen: false,
            modalData: {},

            /** Modal retry data */
            retryModal: {
                isOpen: false,
                modalData: {},
            },

            /** Metrics */
            metrics: {},

            /** Datatables and Pagination */
            results: [],
            order: [],
            tableHeader: [
                { col: 'payment_order_number', title: "ID Pedido" },
                { col: 'subscribers_name', title: "Cliente" },
                { col: 'plans_name', title: "Produto" },
                { col: 'installments', title: "Parcelas" },
                { col: 'subscriber_joining_date', title: "Dt. adesão" },
                { col: 'payment_payment_date', title: "Dt. cobrança" },
                { col: 'payment_plan_status', title: "Status" },
                { col: 'type_payment', title: "Método" },
                { col: 'payment_plan_customer_value', title: "Minha comissão" },
                { col: null, title: "" },
            ],

            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            filter: {
                searchValue: "",
                plans: {
                    options: {},
                    selected: [],
                },
                product: {
                    options: {},
                    selected: [],
                },
                type: {
                    options: {
                        P: "Venda Única",
                        U: "Venda Sem Limite",
                        R: "Assinatura",
                    },
                    selected: [],
                },
                status: {
                    options: {
                        paid: "Pago",
                        pending: "Pendente",
                        refunded: "Estornado",
                        failed: "Falha no pagamento",
                        chargeback: "Chargeback",
                        expired: "Expirado",
                        pending_refund: "Estorno pendente",
                    },
                    selected: [],
                },
                installmentPaid: '',
                method: {
                    options: {
                        boleto: "Boleto",
                        credit_card: "Cartão de Crédito",
                        pix: "Pix",
                        multiple_means_c: "Múltiplos Cartões",
                        multiple_means_bc: "Multimeios (Cartões de crédito + Boleto)",
                    },
                    selected: [],
                },
                daterange: {
                    value: null,
                    formated: null,
                },
                showCouponOnly: false,
            },
            plans: [],
        };
    },
    computed: {
        plansTitle() {
            if (!this.showPlans && this.filter.product.selected.length > 1) {
                return "Você pode somente filtrar por plano, caso selecione um único produto.";
            } else if (!this.showPlans) {
                return "Você precisa selecionar um produto, para habilitar o filtro por plano.";
            }
            return "";
        },
        filterTitle() {
            let filterTitle = "";

            const filters = [
                { id: "product", name: "Produtos", type: "list" },
                { id: "plans", name: "Planos", type: "list" },
                { id: "type", name: "Tipo", type: "list" },
                { id: "status", name: "Status", type: "list" },
                { id: "method", name: "Método de pagamento", type: "list" },
                { id: "daterange", name: "Data de cobrança", type: "date" },
                { id: "installmentPaid", name: "Número de parcelas", type: "text" },
            ];

            filters.map((filter) => {
                const selectedFilter = this.filter[filter.id];

                if (filter.type === "list" && selectedFilter.selected.length > 0) {
                    const items = selectedFilter.selected.map((value) => {
                        return ` <span class="filters-tags">${selectedFilter.options[value]}</span>`;
                    });

                    filterTitle = filterTitle.length > 0 ? filterTitle + " | " : filterTitle;
                    filterTitle += `${filter.name}: ${items.join(" ")}`;
                } else if (filter.type === "date" && selectedFilter.formated) {
                    const dateRange = ` <span class="filters-tags">${selectedFilter.formated}</span>`;

                    filterTitle = filterTitle.length > 0 ? filterTitle + " | " : filterTitle;
                    filterTitle += `${filter.name}: ${dateRange}`;
                } else if (filter.type === "text" && selectedFilter && this.showInstallmentPaid) {
                    const value = ` <span class="filters-tags">${selectedFilter}</span>`;

                    filterTitle = filterTitle.length > 0 ? filterTitle + " | " : filterTitle;
                    filterTitle += `${filter.name}: ${value}`;
                }
            });

            if (filterTitle.length > 0) {
                filterTitle = '<i class="fas fa-filter" style="color: #addf45;"></i> ' + filterTitle;
            }

            return filterTitle;
        },
        showInstallmentPaid() {
            const type = !this.filter.type.selected.includes('P');
            const filtersType = this.filter.type.selected.length;
            const status = !(this.filter.status.selected.filter(status => status != 'paid').length > 0);

            return type && filtersType && status;
        },
        activeStatusOptions() {
            if (this.filter.installmentPaid) {
                const { paid }  = this.filter.status.options;
                return { paid };
            }

            return this.filter.status.options;
        }
    },
    watch: {
        "filter.searchValue": function () {
            this.searchByTerm();
        },
        "filter.product.selected": async function () { },
        showInstallmentPaid(value) {
            if(!value) this.filter.installmentPaid = "";
        }
    },
    methods: {
        toggleRefundFailModal(status) {
      this.isOpenRefundFailModal = status
    },
    async filterProducts() {
            if (this.filter.product.selected.length === 1) {
                await this.getPlans(this.filter.product.selected[0]);

                this.showPlans = true;
            } else {
                this.filter.plans.options = {};
                this.filter.plans.selected = [];

                this.showPlans = false;
            }

            await this.changeFilter();
        },
        async getPlans(productId) {
            const url = plansUrl.replace(":productId", productId);

            try {
                const res = await axios.get(url);

                res.data.data.forEach((plan) => {
                    this.filter.plans.options[plan.id] = `${plan.name}`;
                    //this.filter.plans.options[plan.id] = plan.name;
                });
            } catch (e) { }
        },
        async transactionRetry(transaction) {
            try {
                this.$emit("startLoading");
                let requestURL = retrievePayment.replace(/:paymentId/g, transaction.payments_id);

                await axios.post(requestURL);
                this.retryModal.isOpen = true;
                this.$emit("endLoading");
            } catch (e) {
                this.$emit("endLoading");
                this.retryModal.isOpen = true;
                this.retryModal.modalData.error = true;
                this.retryModal.modalData.message = e.response.data.message;
            }
        },
        closeRetryModal: async function () {
            await this.loadData();
            this.retryModal.modalData = {};
            this.retryModal.isOpen = false;
        },
        async openModal(transaction) {
            const requestURL = getTransactionsDetailsURL.replace(
                /:paymentId/g,
                transaction.payment_plan_id
            );

            this.$emit("startLoading");
            try {
                const params = {};

                const res = await axios.get(requestURL, {
                    params,
                });

                if (res.data.error === true) {
                    this.$emit("endLoading");
                    errorToast(
                        "Algum erro aconteceu!",
                        "Não foi possível carregar os dados da transação, entre em contato com o suporte."
                    );
                    return;
                }

                let {
                    commissions,
                    payment_information,
                    sale_information,
                } = res.data.response[0].response;
                commissions.payment_multiple = transaction.payment_multiple_means;

                payment_information.payment_status = transaction.payment_plan_status;

                this.modalData = {
                    payment_id: transaction.payment_plan_id,
                    commissions,
                    payment_information,
                    sale_information,
                };
                this.$emit("endLoading");
                this.isOpen = true;
            } catch (error) {
                this.$emit("endLoading");
                errorToast(
                    "Algum erro aconteceu!",
                    "Não foi possível carregar os dados da transação, entre em contato com o suporte."
                );
            }
        },
        closeModal: function () {
            this.modalData = {};
            this.isOpen = false;
        },
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getTransactions();
        },
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getTransactions();
        },
        async getTransactions() {
            this.$emit("startLoading");
            const params = {
                statusPayment: this.filter.status.selected,
                paymentType: this.filter.type.selected,
                paymentMethod: this.filter.method.selected,
                productsId: this.filter.product.selected,
                plansId: this.filter.plans.selected,
                searchTerm: this.filter.searchValue,
                period: this.filter.daterange.formated,
                onlyWithCoupon: this.filter.showCouponOnly ? 1 : 0,
                offset: this.pagination.limit,
                page: this.pagination.currentPage,
                order: this.order,
                installmentPaid: this.includeInstallmentPaidOnFilter()
            };

            try {
                const res = await axios.get(getTransactionsURL, {
                    params,
                });

                if (res.data.error === true) {
                    this.$emit("endLoading");
                    errorToast(
                        "Algum erro aconteceu!",
                        "Não foi possível carregar os dados das transações, entre em contato com o suporte."
                    );
                    return;
                }

                const data = res.data.response[0].response;

                /** Populate metrics */
                this.metrics = data.metrics;
                /** Populate results */
                this.results = data.rows.data;
                /** Pagination */
                this.pagination.totalPages = data.rows.last_page;
                this.pagination.totalResults = data.rows.total;
                this.pagination.currentPage = data.rows.current_page;
                this.pagination.limit = data.rows.per_page;
                this.$emit("endLoading");
            } catch (e) {
                this.$emit("endLoading");
                errorToast(
                    "Algum erro aconteceu!",
                    "Não foi possível carregar os dados das transações, entre em contato com o suporte."
                );
            }
        },
        async getProducts() {
            this.$emit("startLoading");
            try {
                const res = await axios.get(getProductsList);
                if (res.data.status !== "success") {
                    this.$emit("endLoading");
                    errorToast(
                        "Algum erro aconteceu!",
                        "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
                    );
                    return;
                }

                res.data.products.forEach((product) => {
                    this.filter.product.options[product.id] = product.name;
                });
                this.$emit("endLoading");
            } catch (e) {
                this.$emit("endLoading");
                errorToast(
                    "Algum erro aconteceu!",
                    "Não foi possível carregar a lista de produtors, entre em contato com o suporte."
                );
            }
        },
        /** Change data on filter */
        changeFilter: async function () {
            this.pagination.currentPage = 1;
            await this.getTransactions();
        },
        searchByTerm: async function () {
            const term = this.filter.searchValue;
            setTimeout(async () => {
                if (term === this.filter.searchValue) {
                    await this.getTransactions();
                }
            }, 1000);
        },
        searchByInstallmentPaidNumber: async function () {
            const number = this.filter.installmentPaid;
            setTimeout(async () => {
                if (number === this.filter.installmentPaid) {
                    await this.getTransactions();
                }
            }, 1000);
        },
        searchByDate: async function (date) {
            date[0] !== null && date[1] !== null
                ? (this.filter.daterange.formated = `${moment(date[0]).format(
                    "DD/MM/YYYY"
                )}-${moment(date[1]).format("DD/MM/YYYY")}`)
                : (this.filter.daterange.formated = null);
            this.pagination.currentPage = 1;
            await this.getTransactions();
        },
        loadData: async function () {
            await this.getProducts();
            await this.getTransactions();
        },
        async exportReport(typeFile) {
            infoToast(
                "Gerando relatório...",
                "Acompanhe o status do arquivo em Relatórios > Listas Exportadas."
            );

            const params = {
                reportName: "transactions",
                typeFile: typeFile,
                statusPayment: this.filter.status.selected,
                paymentType: this.filter.type.selected,
                paymentMethod: this.filter.method.selected,
                productsId: this.filter.product.selected,
                plansId: this.filter.plans.selected,
                installmentPaid: this.filter.installmentPaid,
                searchTerm: this.filter.searchValue,
                period: this.filter.daterange.formated,
                onlyWithCoupon: this.filter.showCouponOnly ? 1 : 0,
                order: this.order
            };

            try {
                await axios.get(generateReportURL, {
                    params,
                });
                successToast(
                    "Relatório gerado!",
                    "Você pode acessar o arquivo gerado em Relatórios > Listas Exportadas."
                );
            } catch (error) {
                errorToast(
                    "Algum erro aconteceu!",
                    "Ocorreu um erro inesperado, por favor contate o suporte."
                );
            }
        },
        sortTable: async function (items) {
            this.order = items;
            await this.getTransactions();
        },
        includeInstallmentPaidOnFilter: function () {
            if (
                this.filter.type.selected.includes('P')
                || this.filter.type.selected.length == 0
                || this.filter.status.selected.filter(status => status != 'paid').length > 0
            ) {
                return null;
            }

            return this.filter.installmentPaid;
        }
    },
    mounted() {
        this.loadData();
    },
};
</script>
<style lang="scss">
.is-disabled {
    background: #393d49 !important;
    border: #595b63 !important;

    .multiselect-caret {
        background: #595b63 !important;
    }
}

.filters-tags {
    background: var(--green1) !important;
    display: inline-block;
    font-size: 14px;
    padding: 2px 4px;
    background: var(--green1);
    border-radius: 4px;
    font-weight: 400;
}
</style>

<style scoped lang="scss">
:deep(.form-group) {
    #search-field {
        height: 40px;
    }

    span {
        top: 7px !important;
    }
}

.slot-header {
    padding-bottom: 24px;
    border-bottom: 1px solid #414655;
}

.title-filter {
    font-size: 1rem;
    font-weight: 700;
    line-height: 1.625rem;

    .fas {
        color: #addf45;
    }
}

.filters {
    font-size: 16px;
}
</style>
