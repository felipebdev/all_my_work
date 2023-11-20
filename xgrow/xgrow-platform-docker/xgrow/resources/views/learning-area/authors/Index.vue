<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container has-border>
        <template v-slot:header-left>
            <Title>Autores: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todos os autores cadastrados ou adicione novos.</Subtitle>
        </template>
        <template v-slot:header-right>
            <router-link :to="{ name: 'author-new' }">
                <DefaultButton status="success" icon="fas fa-plus" text="Novo autor" />
            </router-link>
        </template>
        <template v-slot:content>
            <XgrowTable id="authorDatatable" min-height class="mt-2">
                <template v-slot:filter>
                    <Row>
                        <Col md="6" lg="6" xl="4">
                        <Input id="search-field" placeholder="Pesquise por nome ou e-mail..." class="w-100" is-search
                            icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:header>
                    <th>Nome</th>
                    <th>Currículo</th>
                    <th>Status</th>
                    <th style="width: 50px;"></th>
                </template>
                <template v-slot:body>
                    <tr v-if="authors?.length > 0" v-for="item in authors" :key="item.id">
                        <td class="w-25">
                            <ProfileRow
                                :profile="{ img: item.author_photo_url ?? 'https://las.xgrow.com/background-default.png', title: item.name_author, subtitle: item.author_email }"
                                rounded />
                        </td>
                        <td>{{ resume(item.author_desc, 100) }}</td>
                        <td>
                            <SwitchButton v-model="item.status" @change="changeStatusAuthor(item)" />
                        </td>
                        <td>
                            <ButtonDetail>
                                <router-link :to="{ name: 'author-edit', params: { id: item.id } }">
                                    <li class="option">
                                        <button class="option-btn">
                                            <i class="fa fa-pencil"></i> Editar autor
                                        </button>
                                    </li>
                                </router-link>
                                <li class="option">
                                    <button class="option-btn"
                                        @click="() => { authorModal.active = true; authorId = item.id }">
                                        <i class="fa fa-trash text-danger"></i> Excluir autor
                                    </button>
                                </li>
                            </ButtonDetail>
                        </td>
                    </tr>
                    <NoResult v-else :colspan="8" title="Nenhum autor encontrado!"
                        subtitle="Não há dados a serem exibidos. Clique em novo autor para adicionar." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </XgrowTable>
        </template>
    </Container>

    <ConfirmModal :is-open="authorModal.active">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir autor?</h1>
            <p>Ao remover este autor, o mesmo não poderá ser recuperado!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { authorModal.active = false; authorId = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteAuthor" />
        </div>
    </ConfirmModal>
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import XgrowTable from "../../../js/components/Datatables/Table.vue";
import Pagination from "../../../js/components/Datatables/Pagination.vue";
import NoResult from "../../../js/components/Datatables/NoResult.vue";
import ButtonDetail from "../../../js/components/Datatables/ButtonDetail.vue";
import SwitchButton from "../../../js/components/XgrowDesignSystem/SwitchButton.vue";
import FilterButton from "../../../js/components/XgrowDesignSystem/Buttons/FilterButton.vue";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import ProfileRow from "../../../js/components/Datatables/ProfileRow.vue";
import ConfirmModal from "../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import Resume from "../../../js/components/XgrowDesignSystem/Mixins/resume.js"
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../js/store/components/loading";
import { axiosGraphqlClient } from "../../../js/config/axiosGraphql";
import { ALL_AUTHORS_QUERY_AXIOS } from "../../../js/graphql/queries/authors";
import { DELETE_AUTHOR_MUTATION_AXIOS, UPDATE_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/authors";

export default {
    name: "Index",
    components: {
        ConfirmModal, ProfileRow, Col, Row, Input, FilterButton, SwitchButton, ButtonDetail,
        NoResult, Pagination, XgrowTable, Title, Subtitle, DefaultButton, Container, LoadingStore, Breadcrumb
    },
    mixins: [Resume],
    watch: {
        "filter.searchValue": async function () {
            await this.searchByTerm();
        }
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Autores", link: false },
            ],

            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            /** Author */
            authorModal: {
                active: false
            },
            authors: [],
            authorId: null,

            filter: {
                searchValue: ""
            }
        }
    },
    computed: {
        ...mapStores(useLoadingStore),
    },
    methods: {
        ...mapActions(useLoadingStore, ['setLoading']),
        /** On change page */
        onPageChange: async function (page) {
            this.pagination.currentPage = page;
            await this.getAuthors();
        },
        /** Limit by size itens */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getAuthors();
        },
        /** Search author by Name or Email */
        searchByTerm: async function () {
            const term = this.filter.searchValue;
            setTimeout(async () => {
                if (term === this.filter.searchValue) {
                    await this.getAuthors()
                }
            }, 1000);
        },
        /** Get authors */
        getAuthors: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    query: ALL_AUTHORS_QUERY_AXIOS,
                    variables: {
                        name_author: this.filter.searchValue,
                        author_email: this.filter.searchValue,
                        page: this.pagination.currentPage,
                        limit: this.pagination.limit
                    }
                };

                const res = await axiosGraphqlClient.post(contentAPI, query);
                this.authors = res.data.data.authors.data;
                this.pagination.totalResults = res.data.data.authors.total;
                this.pagination.totalPages = Math.ceil(res.data.data.authors.total / this.pagination.limit);
                if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", "Ocorreu um erro ao obter as informações, tente novamente em instantes.");
                console.log(e);
            }
        },
        /** Delete Author By ID */
        deleteAuthor: async function () {
            try {
                this.authorModal.active = false;
                this.loadingStore.setLoading(true);
                const query = {
                    query: DELETE_AUTHOR_MUTATION_AXIOS,
                    variables: { id: this.authorId }
                };

                const res = await axiosGraphqlClient.post(contentAPI, query);
                if (res.data.hasOwnProperty('errors') && res.data.errors.length > 0) throw new Error(res.data.errors[0].message)
                this.authors = this.authors.filter(author => author.id !== this.authorId);
                this.pagination.totalResults -= 1;
                this.loadingStore.setLoading();
                successToast("Autor removido!", `Autor removido com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e.message ?? "Ocorreu um erro ao deletar as informações, tente novamente em instantes.");
            }
        },
        /** Change author status */
        changeStatusAuthor: async function (author) {
            try {
                this.loadingStore.setLoading(true);
                const { id, name_author, status } = author;

                const query = {
                    query: UPDATE_AUTHOR_MUTATION_AXIOS,
                    variables: { id, status }
                };

                const res = await axiosGraphqlClient.post(contentAPI, query);
                if (res.data.hasOwnProperty('errors') && res.data.errors.length > 0) throw new Error(res.data.errors[0].message)
                const authorIndex = this.authors.findIndex(author => author.id === id);
                this.authors[authorIndex].status = status;
                this.loadingStore.setLoading();
                successToast("Status alterado!", `Você ${status ? 'ativou' : 'desativou'} o(a) autor(a) ${name_author}`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e.message ?? "Ocorreu um erro ao atualizar as informações, tente novamente em instantes.");
            }
        },

    },
    async mounted() {
        await this.getAuthors();
    }
}
//await new Promise(r => setTimeout(r, 2000)); // Lazy for test
</script>
