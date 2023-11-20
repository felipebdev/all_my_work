<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container has-border>
        <template v-slot:header-left>
            <Title>Cursos: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todos os cursos cadastrados ou adicione novos.</Subtitle>
        </template>
        <template v-slot:header-right>
            <router-link :to="{ name: 'course-new' }">
                <DefaultButton status="success" icon="fas fa-plus" text="Novo curso" @click="null" />
            </router-link>
        </template>
        <template v-slot:content>
            <DraggableTable id="courseDatatable" min-height class="mt-2">
                <template v-slot:filter>
                    <Row>
                        <Col md="6" lg="6" xl="4">
                        <Input id="search-field" placeholder="Pesquise por nome do curso" class="w-100" is-search
                            icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.search" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:thead>
                    <th>Curso</th>
                    <th class="w-170">Autor</th>
                    <th>Módulos</th>
                    <th class="w-170"></th>
                    <th style="width: 50px;"></th>
                </template>
                <template v-slot:tbody>
                    <template v-if="courses.length > 0">
                        <draggable v-model="courses" item-key="name" tag="tbody" ghost-class="ghost" :disabled="true">
                            <template #item="{ element }">
                                <tr>
                                    <td>
                                        <ProfileRow
                                            :profile="{ img: element.horizontal_image ?? 'https://las.xgrow.com/background-default.png', title: element.name, subtitle: element.is_experience === 'experience' ? 'Xgrow experience' : 'Tradicional' }" />
                                    </td>
                                    <td>
                                        <SelectWithImage :id="`author-${element.author_id}`" :options="authors"
                                            placeholder="Selecione o autor" v-model="element.author_id"
                                            @change="openAuthorModal(element.author_id, element)" />
                                    </td>
                                    <td>{{ element.Modules.length }}</td>
                                    <td>
                                        <SelectStatus :id="`active-${element.id}`" :options="status"
                                            v-model="element.active" placeholder="Selecione uma situação"
                                            @change="changeCourseStatus(element.active, element)" />
                                    </td>
                                    <td>
                                        <ButtonDetail>
                                            <router-link :to="{ name: 'course-edit', params: { id: element.id } }">
                                                <li class="option">
                                                    <button class="option-btn">
                                                        <i class="fa fa-pencil"></i> Editar curso
                                                    </button>
                                                </li>
                                            </router-link>
                                            <!-- <li class="option d-none">
                                                <button class="option-btn"
                                                    @click="callAlert('Função duplicar curso: #' + element.id)">
                                                    <i class="fa fa-copy"></i> Duplicar curso
                                                </button>
                                            </li> -->
                                            <li class="option">
                                                <button class="option-btn"
                                                    @click="() => { courseModal.active = true; course_id = element.id }">
                                                    <i class="fa fa-trash text-danger"></i> Excluir curso
                                                </button>
                                            </li>
                                        </ButtonDetail>
                                    </td>
                                </tr>
                            </template>
                        </draggable>
                    </template>
                    <DraggableNoResult v-else :colspan="7" title="Nenhum curso encontrado!"
                        subtitle="Não há dados a serem exibidos. Clique em novo curso para adicionar." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </DraggableTable>
        </template>
    </Container>

    <ConfirmModal :is-open="courseModal.active">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir curso?</h1>
            <p>Ao remover este curso, o mesmo não poderá ser recuperado bem como os conteúdos dele!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { courseModal.active = false; course_id = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteCourse" />
        </div>
    </ConfirmModal>

    <ConfirmModal :is-open="authorModal.active">
        <Row class="w-100">
            <Title class="justify-content-center">Novo autor</Title>
            <Subtitle class="justify-content-center">Insira os dados básicos do novo autor da sua plataforma:</Subtitle>
        </Row>
        <div class="modal-body__content" style="background: #333844;">
            <Row>
                <Col>
                <Title :is-form-title="true">Dados do autor</Title>
                <Input id="author_name" label="Nome" v-model="authorModal.name" placeholder="Insira o nome do autor..." />
                </Col>
                <Col>
                <Input id="author_email" label="Email" v-model="authorModal.email" placeholder="Insira o email do autor..."
                    type="email" />
                </Col>
                <Col class="text-start mt-2">
                <ImageUpload title="Foto do autor"
                    subtitle="A imagem aparece nos menus de navegação e as vezes, no título da seção.<br>Tamanho mínimo: 180 x 180"
                    refer="authorImage" ref="authorImage" @send-image="receiveImage" :src="authorModal.image" />
                </Col>
            </Row>
        </div>
        <HeaderLine />
        <div class="modal-body__footer mt-0">
            <DefaultButton text="Cancelar" outline @click="authorModal.active = false" />
            <DefaultButton text="Salvar" status="success" @click="saveAuthor" />
        </div>
    </ConfirmModal>
</template>

<script>
/** Dragabble */
import draggable from "vuedraggable";
import DraggableTable from "../../../js/components/XgrowDesignSystem/DraggableTable/Table.vue";
import DraggableNoResult from "../../../js/components/XgrowDesignSystem/DraggableTable/DraggableNoResult.vue";

import axios from "axios";

import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Pagination from "../../../js/components/Datatables/Pagination.vue";
import ButtonDetail from "../../../js/components/Datatables/ButtonDetail.vue";

import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import SelectStatus from "../components/SelectStatus.vue";
import SelectWithImage from "../../../js/components/XgrowDesignSystem/Form/SelectWithImage.vue";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import ProfileRow from "../../../js/components/Datatables/ProfileRow.vue";
import ConfirmModal from "../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";

import { ALL_COURSES_QUERY_AXIOS } from "../../../js/graphql/queries/courses";
import { GET_ALL_AUTHORS_QUERY_AXIOS } from "../../../js/graphql/queries/authors";
import { DELETE_COURSE_MUTATION_AXIOS, UPDATE_COURSE_STATUS_MUTATION_AXIOS, UPDATE_COURSE_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/courses";
import { SAVE_FAST_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/authors";
import { emailRegex } from "../../../js/components/XgrowDesignSystem/Extras/functions";
import { axiosGraphqlClient } from "../../../js/config/axiosGraphql";
import { mapActions, mapStores } from "pinia";
import { useLoadingStore } from "../../../js/store/components/loading";
import HeaderLine from "../../../js/components/XgrowDesignSystem/Utils/HeaderLine.vue";


export default {
    name: "Index",
    components: {
        ConfirmModal,
        ProfileRow,
        DraggableNoResult,
        DraggableTable,
        Row,
        Col,
        ImageUpload,
        Input,
        ButtonDetail,
        Pagination,
        DefaultButton,
        Subtitle,
        Title,
        Container,
        Breadcrumb,
        draggable,
        SelectStatus,
        SelectWithImage,
        LoadingStore,
        HeaderLine
    },
    data() {
        return {
            dragging: false,
            enabled: true,

            course_id: null,
            courseModal: {
                active: false
            },

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Cursos", link: false },
            ],

            /** Author modal */
            authors: [],
            authorModal: { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' },

            /** Datatables and Pagination */
            courses: [],
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            /** Select draft status */
            status: [
                { value: false, name: 'Rascunho', img: '/xgrow-vendor/assets/img/icons/edit.svg' },
                { value: true, name: 'Publicado', img: '/xgrow-vendor/assets/img/icons/web.svg' },
            ],

            /** Filter search */
            filter: {
                search: null
            }
        }
    },
    watch: {
        "filter.search": async function () {
            await this.searchByTerm();
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
            await this.getCourses();
        },
        /** Limit by size items */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getCourses();
        },
        /** Search course by name */
        searchByTerm: async function () {
            const term = this.filter.search;
            setTimeout(async () => {
                if (term === this.filter.search) {
                    await this.getCourses()
                }
            }, 1000);
        },

        /** COURSE */
        /** Change the course status */
        changeCourseStatus: async function (value, course) {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": UPDATE_COURSE_STATUS_MUTATION_AXIOS,
                    "variables": { id: course.id, name: course.name, active: course.active === 'true' }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                successToast("Curso atualizado!", `A situação foi alterada com sucesso!`)
                await this.getCourses();
                this.loadingStore.setLoading();
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
            }
        },
        /** Get all courses for this platform */
        getCourses: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": ALL_COURSES_QUERY_AXIOS,
                    "variables": { page: this.pagination.currentPage, limit: this.pagination.limit }
                };
                if (this.filter.search) query.variables.name = this.filter.search
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data, total } = res.data.data.courses;
                this.courses = data;
                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);
                if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;
                this.loadingStore.setLoading();
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao listar os cursos. Tente novamente mais tarde.`);
            }
        },
        /** Delete course by Id */
        deleteCourse: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.courseModal.active = false
                const query = {
                    "query": DELETE_COURSE_MUTATION_AXIOS,
                    "variables": { id: this.course_id }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                await this.getCourses();
                this.loadingStore.setLoading();
                successToast("Curso removido!", `Curso removido com sucesso!`);
            } catch (e) {
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar excluir o curso. Tente novamente mais tarde.`);
            }
        },
        /** AUTHOR */
        /** Open Author modal if you need add one */
        openAuthorModal: async function (value, course) {
            if (value === 'new') {
                /** Trick to return select */
                this.authorModal.name = '';
                this.authorModal.email = '';
                this.authorModal.image = 'https://las.xgrow.com/background-default.png';
                const index = this.courses.findIndex(item => item.id === course.id);
                this.courses[index].author_id = course.authorOld;
                this.authorModal.active = true;
            } else {
                const query = {
                    "query": UPDATE_COURSE_AUTHOR_MUTATION_AXIOS,
                    "variables": { id: course.id, name: course.name, author_id: value }
                };
                try {
                    this.loadingStore.setLoading(true);
                    await axiosGraphqlClient.post(contentAPI, query);
                    this.loadingStore.setLoading();
                    successToast("Curso atualizado!", "O autor foi alterado com sucesso.")
                } catch (e) {
                    this.loadingStore.setLoading();
                    errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
                }
            }
        },
        /** Get Authors */
        getAuthors: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_ALL_AUTHORS_QUERY_AXIOS,
                    "variables": { name_author: "", author_email: "", page: 1, limit: 1000 }
                };
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { data } = res.data.data.authors;
                this.authors = [];
                this.authors = data.map(item => ({ value: item.id, name: item.name_author, img: item.author_photo_url }));
                this.authors.push({ value: 'new', name: 'Adicionar novo', img: '/xgrow-vendor/assets/img/icons/plus-cicle.svg' });
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
            }
        },
        /** Author Validation */
        authorValidation: function () {
            if (this.authorModal.name === '')
                throw new Error("O nome do autor é obrigatório!")
            if (this.authorModal.email === '')
                throw new Error("O email do autor é obrigatório!")
            if (this.authorModal.email && !emailRegex(this.authorModal.email))
                throw new Error("O email digitado é inválido!")
            if (this.authorModal.image === 'https://las.xgrow.com/background-default.png')
                throw new Error("A imagem do autor é obrigatória!")
        },
        /** Save Author */
        saveAuthor: async function () {
            try {
                this.authorValidation();
                this.authorModal.active = false
                this.loadingStore.setLoading(true);
                const query = {
                    "query": SAVE_FAST_AUTHOR_MUTATION_AXIOS,
                    "variables": {
                        name_author: this.authorModal.name,
                        author_email: this.authorModal.email,
                        author_photo_url: this.authorModal.image ?? null, status: true
                    }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                await this.getAuthors();
                successToast("Autor cadastrado!", `O autor ${this.authorModal.name} foi cadastrado com sucesso!`);
                this.authorModal = { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' }
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", e.message ?? `Ocorreu um problema ao tentar salvar as alterações no curso. Tente novamente mais tarde.`);
            }
        },
        /** Receive Author image */
        receiveImage: async function (obj) {
            try {
                const formData = new FormData();
                formData.append('image', obj.file.files[0])
                this.loadingStore.setLoading(true);
                const res = await axios.post(uploadImageURL, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                this.loadingStore.setLoading();
                this.authorModal.image = res.data.response.file
            } catch (e) {
                errorToast("Algum erro aconteceu!", e.message);
            }
        },
    },
    async created() {
        await this.getAuthors();
    },
    async mounted() {
        await this.getCourses();
    }
};
</script>

<style lang="scss" scoped>
.custom-select {
    :deep(select) {
        background: #252932 !important;
        border: 1px solid #646D85 !important;
        border-radius: 8px !important;
        height: 40px !important;
        min-height: 40px !important;
    }
}

.modal-body__content {
    border-radius: 8px;
    padding: 1rem;
    margin-top: 0 !important;
}
</style>
