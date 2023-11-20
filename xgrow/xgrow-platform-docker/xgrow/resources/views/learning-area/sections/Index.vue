<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container has-border>
        <template v-slot:header-left>
            <Title>Seções: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todas as seções cadastradas ou adicione novas.</Subtitle>
        </template>
        <template v-slot:header-right>
            <DefaultButton status="success" icon="fas fa-plus" text="Nova seção" @click="openModal('createModal')" />
        </template>
        <template v-slot:content>
            <Table id="users-table" min-height class="mt-2">
                <template v-slot:filter>
                    <Row>
                        <Col md="6" lg="6" xl="4">
                        <Input id="search-field" placeholder="Pesquise pelo título da seção..." class="w-100" is-search
                            icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="search" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:header>
                    <th v-for="header in [
                        'Seção',
                        'Conteúdos',
                        'Cursos',
                    ]" :key="header">
                        {{ header }}
                    </th>
                    <th style="width: 200px"></th>
                    <th style="width: 80px"></th>
                </template>
                <template v-slot:body v-if="sections.length">
                    <tr :key="`link-${i}`" v-for="(item, i) in sections">
                        <td class="d-flex align-items-center gap-2">
                            <img class="d-block" style="
                                            min-width: 115px;
                                            height: 51px;
                                            border-radius: 4px;
                                            object-fit: cover;
                                            background: #1e2025;
                                        " :src="
                                            item.thumb_horizontal ?
                                                item.thumb_horizontal :
                                                'https://las.xgrow.com/background-default.png'
                                        " />
                            <p style="font-weight: 600; color: white">{{ item.title }}</p>
                        </td>
                        <td>{{ getContentCount(item) }}</td>
                        <td>{{ getCoursesCount(item) }}</td>
                        <td>
                            <SelectStatus :id="`active-${item.id}`" :options="status" v-model="item.published"
                                placeholder="Selecione uma situação" @change="updateSectionStatus(item)" />
                        </td>
                        <td>
                            <ButtonDetail>
                                <li class="option">
                                    <button class="option-btn" @click="redirectToEdit(item.id)">
                                        <i class="fa fa-pencil"></i>
                                        Editar seção
                                    </button>
                                </li>
                                <li class="option">
                                    <button class="option-btn" @click="duplicateSection(item)">
                                        <i class="fa fa-copy"></i> Duplicar seção
                                    </button>
                                </li>
                                <li class="option">
                                    <button class="option-btn" @click="openModal('deleteModal', item.id)">
                                        <i class="fa fa-trash text-danger"></i>
                                        Excluir seção
                                    </button>
                                </li>
                            </ButtonDetail>
                        </td>
                    </tr>
                </template>
                <template v-slot:body v-else>
                    <NoResult :colspan="11" title="Nenhuma seção encontrada!"
                        subtitle="Não há dados a serem exibidos. Clique em nova seção para adicionar." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </Table>

        </template>
    </Container>
    <delete-section :isOpen="deleteModal.isOpen" :id="deleteModal.id" @confirm="deleteSectionById"
        @close="closeModal('deleteModal')" />

    <create-section :isOpen="createModal.isOpen" @confirm="createSection" @close="closeModal('createModal')" />
</template>

<script>
import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton";
import Pagination from "../../../js/components/Datatables/Pagination";
import ButtonDetail from "../../../js/components/Datatables/ButtonDetail";

import Loading from "../../../js/components/XgrowDesignSystem/Utils/Loading";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input";
import SelectWithImage from "../../../js/components/XgrowDesignSystem/Form/SelectWithImage.vue";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row";
import ProfileRow from "../../../js/components/Datatables/ProfileRow";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";

import { mapStores, mapState, mapActions } from "pinia";
import { useSectionsStore } from "../../../js/store/sections";
import Table from "../../../js/components/Datatables/Table.vue";
import DeleteSection from "./modals/DeleteSection.vue";
import CreateSection from "./modals/CreateSection.vue";
import SelectStatus from "../components/SelectStatus.vue";
import NoResult from '../../../js/components/Datatables/NoResult.vue';

import { axiosGraphqlClient } from '../../../js/config/axiosGraphql';
import { ALL_SECTIONS_QUERY_AXIOS } from '../../../js/graphql/queries/sections';
import { DELETE_SECTIONS_AXIOS, CREATE_SECTIONS_AXIOS, UPDATE_SECTIONS_STATUS_AXIOS } from "../../../js/graphql/mutations/sections";
import { GET_COURSE_BY_PARAMS_QUERY_AXIOS } from '../../../js/graphql/queries/courses';

export default {
    name: "Index",
    components: {
        ProfileRow,
        Row,
        Col,
        ImageUpload,
        Input,
        Loading,
        ButtonDetail,
        Pagination,
        DefaultButton,
        Subtitle,
        Title,
        Container,
        Breadcrumb,
        SelectStatus,
        SelectWithImage,
        LoadingStore,
        Table,
        DeleteSection,
        CreateSection,
        NoResult
    },
    data() {
        return {
            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Seções", link: false },
            ],

            /** Select draft status */
            status: [
                {
                    value: false,
                    name: "Rascunho",
                    img: "/xgrow-vendor/assets/img/icons/edit.svg",
                },
                {
                    value: true,
                    name: "Publicado",
                    img: "/xgrow-vendor/assets/img/icons/web.svg",
                },
            ],

            sections: [],

            search: "",

            createForm: {
                title: "",
                verticalImage: 'https://las.xgrow.com/background-default.png',
                horizontalImage: 'https://las.xgrow.com/background-default.png'
            },

            deleteModal: {
                isOpen: false,
                id: ""
            },

            createModal: {
                isOpen: false,
            },

            /** Pagination */
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },
        };
    },
    watch: {
        search() {
            let term = this.search;

            setTimeout(async () => {
                if (term === this.search) {
                    this.pagination.currentPage = 1;
                    this.getSections();
                }
            }, 1000);
        }
    },
    computed: {
        ...mapState(useSectionsStore, [
            "loadingStore"
        ]),
        ...mapStores(useSectionsStore),
    },
    methods: {
        getCoursesCount(item) {
            return item.section_items.filter((el) => el.type == "course")
                .length;
        },
        getContentCount(item) {
            return item.section_items.filter((el) => el.type == "content")
                .length;
        },
        redirectToEdit(id) {
            this.$router.push({ name: "section-edit", params: { id } });
        },
        async onPageChange(page) {
            this.pagination.currentPage = page;

            await this.getSections();
        },
        /** Limit by size items */
        async onLimitChange(value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;

            await this.getSections();
        },
        /** Get all Sections for this platform */
        async getSections() {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": ALL_SECTIONS_QUERY_AXIOS,
                    "variables": {
                        title: this.search,
                        page: this.pagination.currentPage,
                        limit: this.pagination.limit,
                        platform_id: this.platform_id
                    }
                };

                if (this.search == "") delete query.variables.title;

                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data, total } = res.data.data.sections;
                this.sections = data;
                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);
                if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;

            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar as seções. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        /** Delete Sections by id */
        async deleteSectionById(id) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": DELETE_SECTIONS_AXIOS,
                    "variables": { id }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                await this.getSections();
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao deletar uma seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
            this.closeModal('deleteModal');
        },

        /** Create Section */
        async createSection(payload) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": CREATE_SECTIONS_AXIOS,
                    "variables": {
                        title: payload.title,
                        thumb_vertical: payload.thumb_vertical,
                        thumb_horizontal: payload.thumb_horizontal,
                        section_items: []
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                await this.getSections();
            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao criar uma seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
            this.closeModal('createModal');
        },
        /** Duplicate Section */
        async duplicateSection(item) {
            try {
                this.loadingStore.setLoading(true);

                const section = { ...item };

                delete section.id;

                section.title = `${section.title} - (Duplicado)`

                const createQuery = {
                    "query": CREATE_SECTIONS_AXIOS,
                    "variables": section
                };

                await axiosGraphqlClient.post(contentAPI, createQuery);

                await this.getSections();

                this.loadingStore.setLoading();
                successToast("Ação realizada", `A seção foi duplicada com sucesso!`);
            } catch (e) {
                console.log(e)
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar duplicar Seção. Tente novamente mais tarde.`);
            }
        },
        //** update item status */
        async updateSectionStatus(item) {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": UPDATE_SECTIONS_STATUS_AXIOS,
                    "variables": {
                        id: item.id,
                        published: item.published == "true" ? true : false
                    }
                };

                await axiosGraphqlClient.post(contentAPI, query);

                successToast("Ação realizada", `O status da seção foi atualizado com sucesso!`);
            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao atualizar os itens da Seção. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        /** Get all courses for this platform */
        async getCourses() {
            try {
                this.loadingStore.setLoading(true);

                const query = {
                    "query": GET_COURSE_BY_PARAMS_QUERY_AXIOS,
                    "variables": {
                        name: this.searchContent,
                        page: this.pagination.currentPage,
                        limit: this.pagination.limit,
                    }
                };

                if (this.searchContent == "") delete query.variables.name;
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data, total } = res.data.data.courses;

                this.courses = data.map(el => ({ ...el, checked: false }));

                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);

            } catch (e) {
                console.log(e)
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar as seções. Tente novamente mais tarde.`);
            }

            this.loadingStore.setLoading();
        },
        openModal(type, id = null) {
            this[type].isOpen = true;

            if (id) this[type].id = id;
        },
        closeModal(type) {
            this[type].isOpen = false;

            if (this[type].id) this[type].id = "";
            if (this[type].items) this[type].items = [];
        }
    },
    async created() {
        this.platform_id = platform_id.toString();
    },
    async mounted() {
        await this.getSections();
    },
};
</script>

<style lang="scss" scoped>
.custom-select {
    :deep(select) {
        background: #252932 !important;
        border: 1px solid #646d85 !important;
        border-radius: 8px !important;
        height: 40px !important;
        min-height: 40px !important;
    }
}

.modal-body__content {
    background: #333844;
    border-radius: 8px;
    padding: 1rem;
}
</style>
