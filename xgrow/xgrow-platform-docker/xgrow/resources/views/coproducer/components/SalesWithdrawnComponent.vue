<template>
    <div>
    <div class="col-12 d-flex flex-column flex-lg-row gap-2 wrap overflow-auto py-2">
        <FinancialCard
            border-color="#93BC1E"
            :title="$filters.formatCurrency((stats.available / 100))"
            subtitle="Disponível para saque"
            class="financial-card">
            <button class="withdraw-button xgrow-button xgrow-button-sm" type="button" href="javascript:void(0)"
                @click="withdrawModal=true">
                Sacar
            </button>
        </FinancialCard>
        <FinancialCard
            border-color="#85F49E"
            :title="$filters.formatCurrency((stats.current / 100))"
            subtitle="Saldo atual"
            class="financial-card">
            <XgrowTooltip
                id="receivable"
                icon='<i class="fas fa-info-circle"></i>'
                tooltip="O saldo atual estará disponível em até 30 dias para saque." />
        </FinancialCard>
        <FinancialCard
            border-color="#E1BB32"
            :title="$filters.formatCurrency((stats.pending / 100))"
            subtitle="A receber"
            class="financial-card" />
    </div>
    <div class="xgrow-card card-dark">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <Table :id="'withdrawTable'">
                    <template v-slot:title>
                        <div
                            class="d-flex align-items-center gap-1 flex-wrap justify-content-center justify-content-md-between w-100">
                            <div class="xgrow-table-header">
                                <h5 class="title">Saques: {{ pagination.totalResults }}</h5>
                                <span class="subtitle">Veja os detalhes de todos os saques realizados.</span>
                            </div>
                            <div class="mb-3 d-flex align-items-center py-2 gap-2 flex-wrap w-100">
                                <button type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiv"
                                        aria-bs-expanded="false" aria-bs-controls="collapseDiv" aria-expanded="true"
                                        class="xgrow-button-filter xgrow-button export-button me-1">
                                    <span>Filtros avançados
                                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                    </span>
                                </button>
                            </div>
                        </div>
                        <div class="w-100 collapse" id="collapseDiv">
                            <div class="filter-container">
                                <div class="p-2 px-3">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 my-2">
                                            <p class="title-filter">
                                                <i class="fas fa-filter"></i> Filtros avançados
                                            </p>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-3">
                                            <DatePicker
                                                v-model:value="filter.dateRangeValue"
                                                format="DD/MM/YYYY"
                                                :clearable="true" type="date" range
                                                placeholder="Data do Saque"
                                                @change="searchByDate"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-slot:header>
                        <th>Transação</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </template>
                    <template v-slot:body>
                        <template v-if="results.length > 0">
                            <tr v-for="item in results" :key="item.id">
                                <td>{{ item.id }}</td>
                                <td>{{ $filters.formatDateTimeBR(item.created_at) }}</td>
                                <td>{{ $filters.formatCurrency(item.amount / 100) }}</td>
                                <td><StatusBadge :status="item.status" /></td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td colspan="6" class="xgrow-no-content">
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
            </div>
        </div>
    </div>

    <StatusModalComponent :is-open="loading.active" :status="loading.status"/>

    <ModalComponent :is-open="withdrawModal" @close="withdrawModal=false">
        <template v-slot:title>
            <h5 class="m-0 p-0 mx-3">Realizar saque</h5>
        </template>
        <template v-slot:content>
            <div class="row w-100">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <p>Confirme os dados bancários cadastrados no seu perfil e insira o valor que deseja sacar.</p>
                </div>
                <div class="row p-3 py-4">
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <p>Banco</p>
                        <p>{{ bankDetails.bank }}</p>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <p>Agência</p>
                        <p v-if="bankDetails.branch_check_digit">{{ bankDetails.branch + "-" + bankDetails.branch_check_digit }}</p>
                        <p v-else>{{ bankDetails.branch }}</p>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 mt-3">
                        <p>Conta</p>
                        <p>{{ bankDetails.account + "-" + bankDetails.account_check_digit }}</p>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 mt-3">
                        <p>CPF/CNPJ</p>
                        <p>{{ bankDetails.document }}</p>
                    </div>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 pb-2">
                    <p>Disponível para sacar: {{ $filters.formatCurrency((stats.available / 100)) }}</p>
                </div>
                <Alert
                    :title="'Saque indisponível'"
                    status="warning"
                    v-show="bankDetails.bank == '000'"
                >
                    Sua conta bancária está em análise. Entre em contato com o suporte para solicitar a liberação.
                </Alert>
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <Input
                        id="withdrawValue"
                        label="Valor que deseja sacar"
                        v-model="withdraw.value"
                        type="text"
                        v-maska="['#,##', '##,##', '###,##', '#.###,##', '##.###,##', '###.###,##', '#.###.###,##', '##.###.###,##', '###.###.###,##']"
                    />
                    <small>O valor estará disponível na conta no próximo dia útil após as 14h.</small>
                </div>
            </div>
        </template>
        <template v-slot:footer="slotProps">
            <button type="button" class="btn btn-outline-success" @click="slotProps.closeModal">Cancelar</button>
            <button type="button" class="btn btn-success"
                    @click="simulate"
                    :disabled="parseFloat(withdraw.value) < 1"
                    v-show="bankDetails.bank != '000'"
            >
                Simular
            </button>
        </template>
    </ModalComponent>

    <ModalComponent :is-open="simulatorModal" @close="simulatorModal=false">
        <template v-slot:title>
            <h5 class="m-0 p-0 mx-3">Confirmar saque</h5>
        </template>
        <template v-slot:content>
            <div class="row w-100">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <p>Valor requisitado: R$ {{ withdraw.value }}</p>
                    <p class="my-3">Custo da transferência: {{ $filters.formatBRLCurrency(withdraw.tax) }}</p>
                    <p>Valor a ser creditado na conta: {{ $filters.formatBRLCurrency((withdraw.value.replaceAll('.', '').replace(',', '.')) - withdraw.tax) }}</p>
                </div>
            </div>
        </template>
        <template v-slot:footer="slotProps">
            <button type="button" ref="closeModal" class="btn btn-outline-success" @click="slotProps.closeModal">
                Cancelar
            </button>
            <button type="button" class="btn btn-success" @click="withdrawAccount"
                    :disabled="parseFloat(withdraw.value) < 1">
                Efetuar transação
            </button>
        </template>
    </ModalComponent>
    </div>
</template>


<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";
import StatusModalComponent from "../../../js/components/StatusModalComponent";
import Input from "../../../js/components/XgrowDesignSystem/Input";
import ModalComponent from "../../../js/components/ModalComponent";

import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

import {cpf, cnpj} from "cpf-cnpj-validator";
import moment from "moment";
import axios from "axios";
import {maska} from "maska";
import FinancialCard from "../../../js/components/XgrowDesignSystem/Cards/FinancialCard.vue";
import XgrowTooltip from "../../../js/components/XgrowDesignSystem/Tooltips/XgrowTooltip"
import StatusBadge from "../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import Alert from "../../../js/components/XgrowDesignSystem/Alert/Alert";

export default {
    name: "SalesWithDrawnComponent",
    components: {
        Input,
        Table,
        DatePicker,
        Pagination,
        StatusModalComponent,
        ModalComponent,
        FinancialCard,
        XgrowTooltip,
        StatusBadge,
        Alert,
    },
    props: {
        platformId: {default: null}
    },
    directives: {maska},
    data() {
        return {
            loading: {
                active: false,
                status: "loading"
            },
            filter: {
                searchValue: "",
                statusPayment: [],
                statusPayments: [
                    {value: "success", label: "Realizada"},
                    {value: "fail", label: "Falha"}
                ],
                dateRangeValue: null,
                dateRangeFormated: null,
                hasCoupom: false
            },
            results: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25
            },
            withdrawModal: false,
            simulatorModal: false,
            withdraw: {
                tax: 3.67,
                value: "",
                totalValue: 0
            },
            stats: {
                pending: 0,
                available: 0,
                transferred: 0,
                current: 0
            },
            bankDetails: {
                account: "",
                account_check_digit: "",
                branch: "",
                branch_check_digit: "",
                bank: "",
                document: ""
            }
        };
    },
    methods: {
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getWithdrawList();
        },
        onLimitChange: async function (value) {
            this.pagination.limit = parseInt(value);
            this.pagination.currentPage = 1;
            await this.getWithdrawList();
        },
        async searchByDate(date) {
            this.filter.dateRangeFormated = null

            if (date[0] !== null && date[1] !== null) {
                this.filter.dateRangeFormated = `${moment(date[0]).format("YYYY-MM-DD")}|${moment(date[1]).format("YYYY-MM-DD")}`
            }

            this.pagination.currentPage = 1;
            await this.getWithdrawList();
        },
        async getWithdrawList(platformId = null) {
            this.loading.active = true;
            platformId = platformId === null ? this.platformId : platformId;
            try {
                await this.getBalance(platformId);
                await this.getBankInfo(platformId);
                let withdrawListUrl = withdrawListURL.replace(/:platformId/g, platformId);

                const params = {
                    page: this.pagination.currentPage,
                    offset: this.pagination.limit
                }

                if (this.filter.dateRangeValue !== null) {
                    params.dateRange = this.filter.dateRangeFormated
                }

                const res = await axios.get(withdrawListUrl, { params });
                const data = res.data.response.data;
                this.results = data.data;
                this.pagination.currentPage = data.current_page ?? 1;
                this.pagination.limit = data.per_page ?? 25;
                this.pagination.totalPages = data.last_page ?? 1;
                this.pagination.totalResults = data.total ?? 0;
                this.loading.active = false;
            } catch (e) {
                this.loading.active = false;
            }
        },
        getBalance: async function (platformId) {
            try {
                let balanceDetailsUrl = balanceDetailsURL.replace(/:platformId/g, platformId);
                const res = await axios.get(balanceDetailsUrl);
                const data = res.data.response.data;
                this.stats.current = data.current ?? 0;
                this.stats.pending = data.pending ?? 0;
                this.stats.transferred = data.transferred ?? 0;
                this.stats.available = data.available ?? 0;
            } catch (e) {
                errorToast("Erro ao realizar ação.", e.response.data.message);
            }
        },
        getBankInfo: async function (platformId) {
            try {
                const coproducerBankInformationUrl = coproducerBankInformation.replace(/:platformId/g, platformId);
                const res = await axios.get(coproducerBankInformationUrl);
                const data = res.data.response.data;
                this.bankDetails.account = data.account ?? "Não informado";
                this.bankDetails.account_check_digit = data.account_check_digit ?? "Não informado";
                this.bankDetails.branch = data.branch ?? "Não informado";
                this.bankDetails.branch_check_digit = data.branch_check_digit;
                this.bankDetails.bank = data.bank ?? "Não informado";

                let document;
                if (data.document) {
                    if (cpf.isValid(data.document)) document = cpf.format(data.document);
                    if (cnpj.isValid(data.document)) document = cnpj.format(data.document);
                }

                this.bankDetails.document = document ?? "Não informado";
            } catch (e) {
                errorToast("Erro ao realizar ação.", e.response.data.message);
            }
        },
        withdrawAccount: async function () {
            try {
                const valueMinusTax = this.withdraw.value.replaceAll('.', '').replace(',', '.') - this.withdraw.tax;
                const totalWithdraw = Math.trunc(valueMinusTax * 100);
                const coproducerWithdrawUrl = coproducerWithdrawURL.replace(/:platformId/g, this.platformId);
                await axios.post(coproducerWithdrawUrl, {
                    amount: totalWithdraw,
                    message: "Saque Coprodução Xgrow"
                });
                successToast("Saque realizado com sucesso.", "Ações realizada com sucesso.");
                this.pagination.currentPage = 1;
                this.filter.dateRangeValue = null
                await this.getWithdrawList(this.platformId);
                this.$refs.closeModal.click()
                this.withdraw.value = '';
            } catch (e) {
                errorToast("Erro ao realizar ação.", e.response.data.message);
            }
        },
        simulate() {
            if (this.withdraw.value === null) {
                errorToast("Erro ao realizar ação.", "Digite o valor que deseja sacar!");
                return false
            }
            this.simulatorModal=true;
            this.withdrawModal=false;
        }
    },
};
</script>

<style scoped lang="scss">
.financial-card {
    position:relative;
}

.withdraw-button {
    position: absolute!important;
    right: 10px!important;
    margin-top: 0px!important;
    top: 22px!important;
}

.xgrow-button-filter {
    min-width: 200px;
    max-width: 310px;
}

.fa-chevron-down {
    margin-left: 0.625rem;
}

#ipt-global-filter {
    height: 40px;
}

.xgrow-button-sm {
    width: 60px;
    height: 28px;
    margin-top: -10px
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

    .fas {
        color: #ADDF45;
    }
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
