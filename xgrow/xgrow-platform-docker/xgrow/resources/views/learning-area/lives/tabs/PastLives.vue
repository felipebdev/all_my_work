<template>
    <Container has-border>
        <template v-slot:header-left>
            <Title>Lives realizadas: {{ results.length }}</Title>
            <Subtitle>Veja os detalhes de suas lives realizadas ou crie uma nova.</Subtitle>
        </template>
        <template v-slot:header-right>
            <router-link :to="{ name: 'lives-new' }">
                <DefaultButton status="success" icon="fas fa-plus" text="Nova live" />
            </router-link>
        </template>
        <template v-slot:content>
            <div class="d-flex align-items-center justify-content-between py-2 gap-2 flex-wrap w-100">
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <Input id="search-field" placeholder="Pesquise pelo nome ou e-mail do autor..."
                        icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue"
                        style="min-width: 300px; max-width: 400px" />
                    <FilterButton target="filterAuthors" />
                </div>
            </div>
            <XgrowTable id="pastLivesTable" minHeight>
                <template v-slot:collapse>
                    <div class="mb-3 collapse" id="filterAuthors">
                        <div class="filter-container">
                            <div class="p-2 px-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 my-2">
                                        <p class="title-filter">
                                            <i class="fas fa-filter"></i> Filtros avançados
                                        </p>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12 my-3">
                                        <Subtitle>Filtros em breve...</Subtitle>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                <template v-slot:header>
                    <th class="w-36">ID</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Status</th>
                    <th></th>
                </template>
                <template v-slot:body>
                    <tr v-if="results.length > 0" v-for="(item, index) in results" :key="item._id">
                        <td class="w-36">{{ index + 1 }}</td>
                        <td>
                            <ProfileRow
                                :profile="{ img: item.authorImg ?? 'https://las.xgrow.com/background-default.png', title: item.title, subtitle: item.author }" />
                        </td>
                        <td style="max-width: 270px">{{ item.description }}</td>
                        <td>{{ formatDateSingleLine(item.date) }}</td>
                        <td><span v-html="getLiveDate(item.date, item.finishDate)"></span></td>
                        <td>
                            <StatusBadge :status="item.isEnabled.toString()" />
                        </td>
                        <td>
                            <ButtonDetail>
                                <router-link :to="{ name: 'lives-index' }">
                                    <li class="option">
                                        <router-link class="option-btn"
                                            :to="{ name: 'lives-edit', params: { id: item._id } }">
                                            <i class="fa fa-pencil"></i> Editar
                                        </router-link>
                                    </li>
                                </router-link>
                                <li class="option">
                                    <button class="option-btn" @click="$emit('delete', item._id)">
                                        <i class="fa fa-trash text-danger"></i> Excluir live
                                    </button>
                                </li>
                            </ButtonDetail>
                        </td>
                    </tr>
                    <NoResult v-else :colspan="7" title="Nenhum autor encontrado!"
                        subtitle="Não há dados a serem exibidos. Clique em nova live para adicionar." />
                </template>
            </XgrowTable>
        </template>
        <template v-slot:footer>
            <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
            </Pagination>
        </template>
    </Container>
</template>

<script>
import Container from "../../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Input from "../../../../js/components/XgrowDesignSystem/Form/Input";
import Row from "../../../../js/components/XgrowDesignSystem/Utils/Row";
import LiveCard from "../components/LiveCard";
import Pagination from "../../../../js/components/Datatables/Pagination";
import XgrowTable from "../../../../js/components/Datatables/Table";
import FilterButton from "../../../../js/components/XgrowDesignSystem/Buttons/FilterButton";
import ButtonDetail from "../../../../js/components/Datatables/ButtonDetail";
import NoResult from "../../../../js/components/Datatables/NoResult";
import ProfileRow from "../../../../js/components/Datatables/ProfileRow";
import formatDateTimeDualLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateTimeDualLine";
import formatDateSingleLine from "../../../../js/components/XgrowDesignSystem/Mixins/formatDateSingleLine"
import moment from "moment";
import StatusBadge from "../../../../js/components/XgrowDesignSystem/Badge/StatusBadge";
import { RouterLink } from 'vue-router';

export default {
    name: "PastLives",
    components: {
        StatusBadge,
        ProfileRow,
        NoResult,
        ButtonDetail,
        FilterButton, XgrowTable, Pagination, LiveCard, Row, Input, DefaultButton, Subtitle, Title, Container,
        RouterLink
    },
    mixins: [formatDateSingleLine, formatDateTimeDualLine],
    props: {
        results: { type: Array, required: true }
    },
    data() {
        return {
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            filter: {
                searchValue: ""
            }
        }
    },
    methods: {
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            // await this.getAuthors();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            // await this.getAuthors();
        },
        getLiveDate: function (start, end) {
            const Hstart = moment(start).format('HH:mm[h]');
            const Hend = moment(end).format('HH:mm[h]');

            return `Início: ${Hstart}<br>Fim: ${Hend}`;
        }
    }
}
</script>

<style lang="scss" scoped>
:deep(.form-group) {
    #search-field {
        height: 40px;
    }

    span {
        top: 7px !important;
    }
}

.w-36 {
    width: 36px;
    text-align: center;
}
</style>
