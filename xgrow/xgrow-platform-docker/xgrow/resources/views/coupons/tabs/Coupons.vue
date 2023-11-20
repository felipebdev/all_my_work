<template>
    <div>
        <VerifyDocument v-if="verifyDocument" :description="recipientStatusMessage"/>
        <div class="xgrow-card card-dark py-4">
            <Table id="salesTransactionsTable">
                <template v-slot:title>
                    <div class="xgrow-table-header w-100">
                        <div class="d-flex justify-content-between">
                            <h5 class="title">
                                Cupons: {{ pagination.totalResults }}
                            </h5>
                            <a
                                href="/coupons/create"
                                role="button"
                                class="xgrow-button create-coupon"
                                style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-evenly;
                                "
                            >
                                <i class="fa fa-plus"></i> Novo cupom
                            </a>
                        </div>
                        <hr />
                    </div>
                </template>
                <template v-slot:filter>
                    <div
                        class="d-flex my-3 flex-wrap gap-3 align-items-end justify-content-between"
                    >
                        <div class="d-flex gap-3 align-items-end flex-wrap">
                            <Input
                                style="margin: 0px; width: 300px;"
                                id="searchIpt"
                                icon="<i class='fa fa-search'></i>"
                                placeholder="Pesquise pelo nome do cupom..."
                                v-model="filter.search"
                                class="search-input"
                            />
                            <FilterButton target="advancedFilters" />
                        </div>
                    </div>
                </template>
                <template v-slot:collapse>
                    <div
                        class="mb-3 collapse collapse-card advancedFilters"
                        id="advancedFilters"
                    >
                        <div class="p-2 px-3" style="border-radius: inherit">
                            <Row>
                                <Col
                                    classes="mt-2 mb-4 d-flex gap-2 align-items-center"
                                >
                                    <Subtitle
                                        ><i
                                            class="fa fa-filter advancedFilters__icon"
                                        ></i>
                                        Filtros Avançados</Subtitle
                                    >
                                </Col>
                            </Row>
                            <Row>
                                <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                                    <Multiselect
                                        :options="filter.plans.options"
                                        v-model="filter.plans.selected"
                                        :searchable="true"
                                        mode="tags"
                                        placeholder="Digite ou selecione o plano"
                                        :canClear="true"
                                        @select="changeFilter"
                                        @deselect="changeFilter"
                                        @clear="changeFilter('plans')"
                                    >
                                        <template v-slot:noresults>
                                            <p
                                                class="multiselect-option"
                                                style="opacity: 0.5"
                                            >
                                                Plano não encontrado...
                                            </p>
                                        </template>
                                    </Multiselect>
                                </Col>
                                <Col sm="12" md="6" lg="6" xl="6" class="my-4">
                                    <DatePicker
                                        class="w-100"
                                        v-model="filter.maturity.value"
                                        format="DD/MM/YYYY"
                                        :clearable="true"
                                        type="date"
                                        range
                                        placeholder="Validade"
                                        @change="searchByDate"
                                    />
                                </Col>
                            </Row>
                        </div>
                    </div>
                </template>
                <template v-slot:header>
                    <th>Nome</th>
                    <th>Plano</th>
                    <th>Validade</th>
                    <th>Desconto</th>
                    <th style="width: 60px"></th>
                </template>
                <template v-if="coupons.length > 0" v-slot:body>
                    <tr :key="coupon.id" v-for="coupon in coupons">
                        <td>
                            {{ coupon.code }}
                        </td>
                        <td>
                            <a
                                :href="`/products/edit-plan-product/${coupon.plans_id}`"
                                style="color: #fff"
                            >
                                {{ coupon.plan_name }}
                            </a>
                        </td>
                        <td>
                            {{ formatDateSingleLine(coupon.maturity) }}
                        </td>
                        <td>
                            {{
                                coupon.value_type === "P"
                                    ? `${String(coupon.value).replace(
                                          ".",
                                          ","
                                      )}%`
                                    : formatBRLCurrency(coupon.value)
                            }}
                        </td>
                        <td>
                            <DropdownButton
                                :id="coupon.id"
                                :items="getActions(coupon.id, coupon.code)"
                            />
                        </td>
                    </tr>
                </template>
                <template v-else v-slot:body>
                    <tr>
                        <td colspan="7" class="text-center">Não há Cupons.</td>
                    </tr>
                </template>
                <template v-slot:footer>
                    <Pagination
                        class="mt-4"
                        :total-pages="pagination.totalPages"
                        :total="pagination.totalResults"
                        :current-page="pagination.currentPage"
                        @page-changed="
                            (page) => paginationChange('currentPage', page)
                        "
                        @limit-changed="
                            (page) => paginationChange('limit', page)
                        "
                    />
                </template>
            </Table>
            <StatusModal :is-open="loading" status="loading" />
            <QuestionModal
                :is-open="questionModal.open"
                :title="questionModal.title"
                :description="questionModal.description"
                :callback="questionModal.callback"
            />
        </div>
    </div>
</template>

<script>
import Table from "../../../js/components/Datatables/Table";
import Pagination from "../../../js/components/Datatables/Pagination";

import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import FilterButton from "../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import Modal from "../../../js/components/XgrowDesignSystem/Modals/Modal";
import QuestionModal from "../../../js/components/XgrowDesignSystem/Modals/QuestionModal";
import VerifyDocument from "../../../js/components/XgrowDesignSystem/Alert/VerifyDocument";
import DropdownButton from "../../../js/components/XgrowDesignSystem/Buttons/DropdownButtonV2";
import formatDateSingleLine from "../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine";
import formatBRLCurrency from "../../../js/components/XgrowDesignSystem/Mixins/formatBRLCurrency";
import StatusModal from "../../../js/components/StatusModalComponent";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";
import axios from "axios";
import moment from "moment";
import DatePicker from "vue-datepicker-next";
import "vue-datepicker-next/index.css";
import "vue-datepicker-next/locale/pt-br";

export default {
    name: "coupons",
    components: {
        DropdownButton,
        Table,
        Pagination,
        Subtitle,
        Row,
        Col,
        FilterButton,
        Input,
        Multiselect,
        StatusModal,
        Modal,
        VerifyDocument,
        QuestionModal,
        DatePicker,
    },
    mixins: [formatDateSingleLine, formatBRLCurrency],
    data() {
        return {
            recipientStatusMessage,
            questionModal: {
                open: false,
                title: "",
                callback: () => {},
            },
            verifyDocument,
            loading: false,
            coupons: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
            filter: {
                search: "",
                plans: {
                    options: [],
                    selected: [],
                },
                maturity: {
                    value: "",
                    formated: "",
                },
            },
        };
    },
    watch: {
        "filter.search": function () {
            this.searchByTerm();
        },
    },
    computed() {},
    async mounted() {
        await this.getCoupons();
        await this.getPlans();
    },
    methods: {
        async getCoupons() {
            this.loading = true;

            try {
                const res = await axios.get(getAllURL, {
                    params: {
                        page: this.pagination.currentPage,
                        offset: this.pagination.limit,
                        maturity: this.filter.maturity.formated,
                        plans_id: this.filter.plans.selected,
                        search: this.filter.search,
                    },
                });

                const couponsResponse = res.data.response.coupons;

                this.coupons = couponsResponse.data;

                this.pagination.totalPages = couponsResponse.last_page;
                this.pagination.totalResults = couponsResponse.total;
                this.pagination.currentPage = couponsResponse.current_page;
            } catch (error) {
                errorToast(
                    "Algum erro aconteceu!",
                    `Houve um erro ao alterar o registro: ${error.response.data.message}`
                );
            }

            this.loading = false;
        },
        async searchByDate(date) {
            if (!date[0]) {
                this.filter.maturity.formated = null;
            } else {
                const startDate = moment(this.filter.maturity.value[0]).format(
                    "YYYY-MM-DD"
                );
                const finalDate = moment(this.filter.maturity.value[1]).format(
                    "YYYY-MM-DD"
                );
                this.filter.maturity.formated = [startDate, finalDate];
            }
            await this.changeFilter();
        },
        async changeFilter(clear = false) {
            if (["maturity", "plans"].includes(clear)) {
                this.filter[clear].selected = [];
            }
            this.pagination.currentPage = 1;
            await this.getCoupons();
        },
        async paginationChange(type, page) {
            this.pagination[type] = parseInt(page);
            await this.getCoupons();
        },
        async searchByTerm() {
            this.pagination.currentPage = 1;
            const term = this.filter.search;
            setTimeout(async () => {
                if (term === this.filter.search) {
                    await this.getCoupons();
                }
            }, 1000);
        },
        getActions(id, name, hideDelete) {
            const result = [
                {
                    name: "Editar",
                    ico: "fa fa-pencil",
                    url: "/coupons/" + id + "/edit",
                },
                {
                    name: "Excluir",
                    ico: "fa-solid fa-xmark red",
                    url: "#",
                    callback: () =>
                        this.openModal(
                            `Deseja excluir este cupom?`,
                            `Caso você exclua o cupom ${name} essa ação não poderá ser desfeita`,
                            () => this.deleteCoupon(id)
                        ),
                    hide: hideDelete,
                },
            ];

            return result;
        },
        openModal(title, description, callback) {
            this.questionModal.open = true;
            this.questionModal.title = title;
            this.questionModal.description = description;
            this.questionModal.callback = callback;
        },
        async getPlans() {
            const response = await axios.get(getAllPlansURL);

            const plans = response.data.response.plans;

            this.filter.plans.options = plans.map((plan) => {
                return { value: plan.id, label: plan.name };
            });
        },
        async deleteCoupon(id) {
            const url = deleteUrl.replace(":id", id);

            try {
                await axios.delete(url);
                successToast("Sucesso!", "Cupom excluído com sucesso.");

                this.questionModal.open = false;
                await this.getCoupons();
            } catch (error) {
                errorToast(
                    "Algum erro aconteceu!",
                    `Houve um erro ao alterar o registro: ${error.response.data.message}`
                );
            }
        },
    },
};
</script>

<style lang="scss">
#advancedFilters {
    border-top: 1px solid rgba(255, 255, 255, 0.25);
    border-bottom: 1px solid rgba(255, 255, 255, 0.25);
    background: rgba(0, 0, 0, 0.2);
}

.create-coupon:hover {
    color: #fff !important;
}
</style>
