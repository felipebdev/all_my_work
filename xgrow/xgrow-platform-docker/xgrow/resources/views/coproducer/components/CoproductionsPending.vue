<template>
    <div>
        <div v-if="hasFailed" class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <img :src="imgWarning" style="margin-right: 1rem" />
                <div>
                    <h6>Atenção!</h6>
                    <p>
                        Ocorreu um erro na verificação do seu documento.
                        <a style="color: inherit; font-weight: 700" href="https://ajuda.xgrow.com/pt-br/"
                            target="_blank">Clique aqui</a>
                        para entrar em contato com o suporte e começar a receber comissões.
                    </p>
                </div>
            </div>
        </div>
        <Table :id="'coproductionTable'">
            <template v-slot:title>
                <div
                    class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                    <div class="xgrow-table-header">
                        <h5 class="title">Pedidos pendentes: {{ results.length }}</h5>
                        <span class="subtitle">Veja os detalhes de todos os pedidos de coprodução pendentes.</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                            <div class="xgrow-input me-1 xgrow-input-search">
                                <input id="ipt-global-filter" placeholder="Busque por plataforma ou produto..." type="text"
                                    v-model="filter.searchValue" />
                                <span class="xgrow-input-cancel">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </span>
                            </div>
                            <button type="button" data-bs-toggle="collapse" data-bs-target="#filterCoproductions"
                                aria-bs-expanded="false" aria-bs-controls="filterCoproductions" v-if="false"
                                class="xgrow-button-filter xgrow-button export-button me-1" aria-expanded="true">
                                <span>Filtros avançados
                                    <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                </span>
                            </button>
                            <div class="export-buttons d-none">
                                <button class="xgrow-button export-button me-1" title="Exportar em CSV">
                                    <img src="/xgrow-vendor/assets/img/reports/txt.svg" alt="Exportar em CSV" />
                                </button>
                                <button class="xgrow-button export-button me-1" title="Exportar em XLSX">
                                    <img src="/xgrow-vendor/assets/img/reports/xls.svg" alt="Exportar em XLSX" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:collapse v-if="false">
                <div class="mb-3 collapse" id="filterCoproductions">
                    <div class="filter-container">
                        <div class="p-2 px-3">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 my-2">
                                    <p class="title-filter">Filtros avançados</p>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                                    <div class="xgrow-form-control mb-2">
                                        <Multiselect v-model="filter.platform" :options="filter.platforms"
                                            :searchable="true" mode="tags" @select="null" @clear="null"
                                            placeholder="Digite ou selecione a plataforma..." />
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                                    <DatePicker v-model:value="filter.dateRangeValue" format="DD/MM/YYYY" :clearable="false"
                                        type="date" range placeholder="Data de vencimento" @change="null" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:header>
                <th>Produto</th>
                <th>Comissão</th>
                <th>Vencimento</th>
                <th>Plataforma</th>
                <th>
                    Emitir notas fiscais
                    <Tooltip id="split_invoice" icon="<i class='fas fa-info-circle'></i>"
                        tooltip="Divide a responsabilidade de emissão de notas fiscais com esse produtor." />
                </th>
                <th>Status</th>
                <th></th>
            </template>
            <template v-slot:body v-if="results.length > 0">
                <tr v-for="item in results" :key="item.id">
                    <td>
                        <div class="product-info">
                            <img :src="
                                item.filename ?? 'https://las.xgrow.com/background-default.png'
                            " />
                            <p>{{ item.product_name }}</p>
                        </div>
                    </td>
                    <td>{{ item.percent }}%</td>
                    <td>
                        {{
                            item.contract_limit === null
                            ? "Indeterminado"
                            : $filters.formatDateBR(item.contract_limit)
                        }}
                    </td>
                    <td>{{ item.platform_name }}</td>
                    <td>{{ item.split_invoice ? "Sim" : "Não" }}</td>
                    <td v-html="$filters.modifyStatus(item.status)"></td>
                    <td>
                        <div class="dropdown">
                            <button v-if="item.status != 'recipient_failed'" class="xgrow-button table-action-button m-1"
                                type="button" :id="`dropdownMenuButton-${item.producer_products_id}`"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <ul class="dropdown-menu table-menu xgrow-dropdown-menu"
                                :aria-labelledby="`dropdownMenuButton-${item.producer_products_id}`">
                                <li>
                                    <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                        @click.prevent="acceptCoproduction(item)">
                                        <i class="fas fa-check me-1 green"></i> Aceitar pedido
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item table-menu-item" href="javascript:void(0)"
                                        @click.prevent="rejectCoproduction(item)">
                                        <i class="fas fa-times me-2 red"></i> Recusar pedido
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </template>
            <template v-slot:body v-else>
                <tr>
                    <td colspan="6" class="xgrow-no-content">Não há dados a serem exibidos.</td>
                </tr>
            </template>
            <template v-slot:footer>
                <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                    :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                </Pagination>
            </template>
        </Table>

        <CoproductionsPendingModal :modal="modal" :change-page="changePage" :close-modal="closeModal"
            @reload-coproduction="reloadCoproduction" />

        <StatusModalComponent :is-open="loading" status="loading" />
    </div>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import ModalComponent from "../../../js/components/ModalComponent";
import CoproductionsPendingModal from "./CoproductionsPendingModal";
import Tooltip from "../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip.vue";

import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";
import moment from "moment";
import axios from "axios";
import imgWarning from "../../../../public/xgrow-vendor/assets/img/documents/warning.svg";
import StatusModalComponent from "../../../js/components/StatusModalComponent";

export default {
    name: "CoproductionsPending",
    components: {
        Table,
        Pagination,
        DatePicker,
        Multiselect,
        ModalComponent,
        CoproductionsPendingModal,
        StatusModalComponent,
        Tooltip,
    },
    data() {
        return {
            loading: false,
            hasFailed: false,
            imgWarning,
            modal: {
                isOpen: false,
                isVerified: true,
                isAccept: true,
                item: {},
            },
            filter: {
                searchValue: "",
                platform: [],
                platforms: [{ value: "Nome da plataforma", label: "Nome da plataforma" }],
                dateRangeValue: null,
                dateRangeFormat: null,
            },
            results: [],
            pagination: {
                totalPages: 1,
                totalResults: 1,
                currentPage: 1,
                limit: 25,
            },
        };
    },
    emits: ["changePage", "chargeDataFlow", "reloadCoproduction"],
    watch: {
        "filter.searchValue": function () {
            this.search();
        },
    },
    methods: {
        /** Start Period component */
        startPeriod: function () {
            const startDate = new Date();
            const endDate = new Date(new Date().setDate(startDate.getDate() + 30));
            this.filter.dateRangeValue = [startDate, endDate];
            this.filter.dateRangeFormat =
                moment(startDate).format("DD/MM/YYYY") +
                " - " +
                moment(endDate).format("DD/MM/YYYY");
        },
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            //send get to backend
        },
        /** Send to coproducer flow*/
        chargeDataFlow: function (item) {
            this.$emit("chargeDataFlow", item);
        },
        /** Send to coproducer flow*/
        coproducerFlow: function () {
            this.changePage("coproducer.flow");
            this.modal.isOpen = false;
        },
        /** Change screen by value */
        changePage: function (screen) {
            this.$emit("changePage", screen);
        },
        /** Show transaction detail */
        getCoproducers: async function () {
            const res = await axios.get(coproducerPendingUrl);
            const json = res.data.response.platforms;
            this.results = json.data;
            this.hasFailed = Boolean(
                this.results.find((item) => item.status === "recipient_failed")
            );
            this.pagination.totalPages = json.last_page;
            this.pagination.totalResults = json.total;
            this.pagination.currentPage = json.current_page;
            this.pagination.limit = json.per_page;
        },
        /** Close modal and clear the data */
        closeModal: function () {
            this.modal = {
                isOpen: false,
                isVerified: true,
                platform: "",
            };
        },
        async acceptCoproduction(item) {
            const coproduction = item;
            const url = updateProducerURL
                .replace(/:idProducerProducts/g, coproduction.producer_products_id)
                .replace(/:producerId/g, coproduction.producer_id);

            this.loading = true;

            try {
                const res = await axios.put(url, { status: "active" });
                await this.reloadCoproduction()
                successToast("Ação realizada com sucesso", res.data.message);
            } catch (error) {
                this.hasFailed = true;
                errorToast("Algo aconteceu", error.response.data.message);
            }
            this.loading = false;
        },
        rejectCoproduction: function (item) {
            this.modal.item = { ...item };
            this.modal.isOpen = true;
            this.modal.isAccept = false;
        },
        reloadCoproduction: async function () {
            await this.getCoproducers();
            this.$emit("reloadCoproduction");
            this.changePage("coproducer.my");
        },
        /** Used for search with timer */
        search: async function () {
            const term = this.filter.searchValue;
            setTimeout(() => {
                if (term === this.filter.searchValue) {
                    this.loading = true;
                    axios
                        .get(coproducerPendingUrl, {
                            params: {
                                offset: this.paginationLimit,
                                term: this.filter.searchValue,
                            },
                        })
                        .then((res) => {
                            const json = res.data.response.platforms;
                            this.results = json.data;
                            this.pagination.totalPages = json.last_page;
                            this.pagination.totalResults = json.total;
                            this.pagination.currentPage = json.current_page;
                            this.pagination.limit = json.per_page;
                        })
                        .catch((err) => console.log(err));
                }
            }, 1000);
        },
    },
    async created() {
        this.startPeriod();
        await this.getCoproducers();
    },
};
</script>
<style>
.table-responsive {
    overflow: inherit;
}
</style>
<style lang="scss" scoped>
td {
    vertical-align: middle;
}

.product-info {
    display: flex;
    align-items: center;

    img {
        width: 60.79px !important;
        height: 42px !important;
        border-radius: 2px;
        object-fit: cover;
        object-position: center center;
        margin-right: 8px;
    }
}

#ipt-global-filter {
    padding-bottom: 10px;
}

.table-menu-item {
    text-decoration: none;
}

.green {
    color: #addf45 !important;
}

.red {
    color: #ff7c7c !important;
}
</style>
