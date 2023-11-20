<template>
    <div>
        <div class="xgrow-card card-dark py-4">
            <Table id="salesTransactionsTable">
                <template v-slot:title>
                    <div class="xgrow-table-header w-100">
                        <Title>Status da importação</Title>
                        <Subtitle>
                            Verifique o status dos arquivos carregados e
                            realize o upload novamente, caso precise.
                        </Subtitle>
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
                                placeholder="Pesquise pelo nome do arquivo..."
                                v-model="filter.search"
                                class="search-input"
                            />
                            <!-- <FilterButton target="advancedFilters" /> -->
                        </div>
                    </div>
                </template>
                <!-- <template v-slot:collapse>
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
                </template> -->
                <template v-slot:header>
                    <th>ID</th>
                    <th>Data de importação</th>
                    <th>Status</th>
                    <th>Arquivo original</th>
                    <th>Resultado</th>
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
                        <td colspan="11" class="no-result">

                            <p class="text-center my-4">
                                <img src='/xgrow-vendor/assets/img/new-no-result.svg' alt='Nenhum resultado encontrado.'/>
                            </p>

                            <Title class="mb-3 justify-content-center">
                                Você não possui arquivos em importação
                            </Title>

                            <Subtitle
                                style="font-weight: 600"
                                class="justify-content-center"
                            >
                                Mas não se procupe, você pode adicionar alunos
                                manualmente ou importar uma lista na aba “Mailing” acima.
                            </Subtitle>
                        </td>
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

import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
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
        Title
    },
    mixins: [formatDateSingleLine, formatBRLCurrency],
    data() {
        return {
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
    methods: {
        async searchByTerm() {
            this.pagination.currentPage = 1;
            const term = this.filter.search;
            setTimeout(async () => {
                if (term === this.filter.search) {
                    await this.getCoupons();
                }
            }, 1000);
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
