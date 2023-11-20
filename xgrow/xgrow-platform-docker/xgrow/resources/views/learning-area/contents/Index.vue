<template>
    <LoadingStore />
    <Breadcrumb :items="breadcrumbs" class="mb-3" />
    <Container has-border>
        <template v-slot:header-left>
            <Title>Conteúdos: {{ pagination.totalResults }}</Title>
            <Subtitle>Veja todos os conteúdos cadastrados ou adicione novos.</Subtitle>
        </template>
        <template v-slot:header-right>
            <!-- <router-link :to="{ name: 'content-new' }">
                <DefaultButton status="success" icon="fas fa-plus" text="Novo conteúdo" />
            </router-link> -->
        </template>
        <template v-slot:content>
            <DraggableTable id="ContentDatatable" min-height class="mt-2">
                <template v-slot:filter>
                    <Row>
                        <Col md="6" lg="6" xl="4">
                        <Input id="search-field" placeholder="Pesquise por nome do conteúdo" class="w-100" is-search
                            icon="<i class='fas fa-search'></i>" iconColor="#93BC1E" v-model="filter.searchValue" />
                        </Col>
                    </Row>
                </template>
                <template v-slot:thead>
                    <th>Conteúdo</th>
                    <th>Curso</th>
                    <th>Módulo</th>
                    <th class="w-170">Autor</th>
                    <th></th>
                    <th style="width: 50px;"></th>
                </template>
                <template v-slot:tbody>
                    <template v-if="contents.length > 0">
                        <draggable v-model="contents" item-key="id" tag="tbody" ghost-class="ghost" :disabled="true">
                            <template #item="{ element }">
                                <tr>
                                    <td>
                                        <ContentProfile
                                            :profile="{ img: element.horizontal_image ?? 'https://las.xgrow.com/background-default.png', title: element.title, subtitle: element.contentType }" />
                                    </td>
                                    <td>
                                        {{ element.module?.courses?.name ?? ' - ' }}
                                    </td>
                                    <td>
                                        {{ element.module?.name ?? ' - ' }}
                                    </td>
                                    <td>
                                        <SelectWithImage :id="`author-${element.author_id}`" :options="authors"
                                            placeholder="Selecione o autor" v-model="element.author_id"
                                            @change="openAuthorModal(element.author_id, element)" />
                                    </td>
                                    <td>
                                        <SelectStatus :id="`active-${element.id}`" :options="status"
                                            v-model="element.is_published" placeholder="Selecione uma situação"
                                            @change="changeContentStatus(element.is_published, element)" />
                                    </td>
                                    <td>
                                        <ButtonDetail>
                                            <router-link :to="{ name: 'content-edit', params: { content_id: element.id } }">
                                                <li class="option">
                                                    <button class="option-btn">
                                                        <i class="fa fa-pencil"></i> Editar conteúdo
                                                    </button>
                                                </li>
                                            </router-link>
                                            <li class="option">
                                                <button class="option-btn" @click="duplicateContent(element)">
                                                    <img src="/xgrow-vendor/assets/img/icons/fa-copy.svg" class="img-icon"
                                                        alt="Duplicate"> Duplicar conteúdo
                                                </button>
                                            </li>
                                            <li class="option">
                                                <button class="option-btn"
                                                    @click="() => { contentModal.delete = true; contentId = element.id }">
                                                    <i class="fa fa-trash text-danger"></i> Excluir conteúdo
                                                </button>
                                            </li>
                                        </ButtonDetail>
                                    </td>
                                </tr>
                            </template>
                        </draggable>
                    </template>
                    <DraggableNoResult v-else :colspan="7" title="Nenhum curso encontrado!"
                        subtitle="Não há dados a serem exibidos. Clique em novo conteúdo para adicionar." />
                </template>
                <template v-slot:footer>
                    <Pagination class="mt-4" :total-pages="pagination.totalPages" :total="pagination.totalResults"
                        :current-page="pagination.currentPage" @page-changed="onPageChange" @limit-changed="onLimitChange">
                    </Pagination>
                </template>
            </DraggableTable>
        </template>
    </Container>

    <ConfirmModal :is-open="contentModal.delete">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir conteúdo?</h1>
            <p>Ao remover este curso, o mesmo não poderá ser recuperado bem como os widgets dele!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { contentModal.delete = false; contentId = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteContent" />
        </div>
    </ConfirmModal>

    <ConfirmModal :is-open="contentModal.duplicate">
        <i class="fas fa-times-circle fa-7x text-danger"></i>
        <div class="modal-body__content">
            <h1>Excluir conteúdo?</h1>
            <p>Ao remover este curso, o mesmo não poderá ser recuperado bem como os widgets dele!</p>
        </div>
        <div class="modal-body__footer">
            <DefaultButton text="Cancelar" outline @click="() => { contentModal.duplicate = false; contentId = null }" />
            <DefaultButton text="Excluir mesmo assim" status="success" @click="deleteContent" />
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
// import axios from "axios";
/** Dragabble */
import draggable from "vuedraggable";
import DraggableTable from "../../../js/components/XgrowDesignSystem/DraggableTable/Table.vue";
import DraggableNoResult from "../../../js/components/XgrowDesignSystem/DraggableTable/DraggableNoResult.vue";
import ButtonDetail from "../../../js/components/Datatables/ButtonDetail.vue";
import Pagination from "../../../js/components/Datatables/Pagination.vue";

import Breadcrumb from "../../../js/components/XgrowDesignSystem/Breadcrumb/Breadcrumb.vue";
import Container from "../../../js/components/XgrowDesignSystem/Cards/Container.vue";
import Title from "../../../js/components/XgrowDesignSystem/Typography/Title.vue";
import Subtitle from "../../../js/components/XgrowDesignSystem/Typography/Subtitle.vue";
import Input from "../../../js/components/XgrowDesignSystem/Form/Input.vue";
import Select from "../../../js/components/XgrowDesignSystem/Form/Select.vue";
import SelectWithImage from "../../../js/components/XgrowDesignSystem/Form/SelectWithImage.vue";
import ConfirmModal from "../../../js/components/XgrowDesignSystem/Modals/ConfirmModal.vue";
import ImageUpload from "../../../js/components/XgrowDesignSystem/Form/ImageUpload.vue";
import DefaultButton from "../../../js/components/XgrowDesignSystem/Buttons/DefaultButton.vue";
import Col from "../../../js/components/XgrowDesignSystem/Utils/Col.vue";
import Row from "../../../js/components/XgrowDesignSystem/Utils/Row.vue";
import LoadingStore from "../../../js/components/XgrowDesignSystem/Utils/LoadingStore.vue";
import SelectStatus from "../components/SelectStatus.vue";
import ContentProfile from "./components/ContentProfile.vue";
import HeaderLine from "../../../js/components/XgrowDesignSystem/Utils/HeaderLine.vue";

import { useLoadingStore } from "../../../js/store/components/loading";
import { mapActions, mapStores } from "pinia";
import { emailRegex } from "../../../js/components/XgrowDesignSystem/Extras/functions";
import { axiosGraphqlClient } from '../../../js/config/axiosGraphql';
import { GET_ALL_CONTENTS_QUERY_AXIOS, GET_CONTENT_BY_ID_QUERY_AXIOS } from "../../../js/graphql/queries/contents";
import { UPDATE_CONTENT_AUTHOR_MUTATION_AXIOS, UPDATE_CONTENT_STATUS_MUTATION_AXIOS, DELETE_CONTENT_MUTATION_AXIOS, CREATE_CONTENT_MUTATION_AXIOS } from "../../../js/graphql/mutations/contents";
import { SAVE_FAST_AUTHOR_MUTATION_AXIOS } from "../../../js/graphql/mutations/authors";
import { GET_ALL_AUTHORS_QUERY_AXIOS } from "../../../js/graphql/queries/authors";
import axios from "axios";

export default {
    name: "Index",
    components: {
        Row,
        Col,
        LoadingStore,
        Pagination,
        DefaultButton,
        Subtitle,
        Title,
        Container,
        Breadcrumb,
        draggable,
        DraggableTable,
        DraggableNoResult,
        Select,
        ConfirmModal,
        Input,
        ImageUpload,
        ButtonDetail,
        SelectWithImage,
        SelectStatus,
        ContentProfile,
        HeaderLine
    },
    data() {
        return {
            dragging: false,

            /** Breadcrumbs */
            breadcrumbs: [
                { title: "Resumo", link: "/" },
                { title: "Área de aprendizagem", link: false },
                { title: "Conteúdos", link: false },
            ],

            /** Datatables and Pagination */
            contents: [],
            contentId: null,
            contentModal: {
                delete: false,
                duplicate: false,
            },
            filter: {
                searchValue: ""
            },
            pagination: {
                totalPages: 1,
                totalResults: 0,
                currentPage: 1,
                limit: 25,
            },

            /** Authors */
            authors: [],
            authorModal: { active: false, name: '', email: '', image: 'https://las.xgrow.com/background-default.png' },

            /** Select draft status */
            status: [
                { value: false, name: 'Rascunho', img: '/xgrow-vendor/assets/img/icons/edit.svg' },
                { value: true, name: 'Publicado', img: '/xgrow-vendor/assets/img/icons/web.svg' },
            ]
        }
    },
    watch: {
        "filter.searchValue": async function () {
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
            await this.getContents();
        },
        /** Limit by size items */
        onLimitChange: async function (value) {
            this.pagination.limit = value;
            this.pagination.currentPage = 1;
            await this.getContents();
        },
        /** Search author by Name or Email */
        searchByTerm: async function () {
            const term = this.filter.searchValue;
            setTimeout(async () => {
                if (term === this.filter.searchValue) {
                    await this.getContents()
                }
            }, 1000);
        },
        /** CONTENT */
        /** Get all contents */
        getContents: async function () {
            try {
                this.loadingStore.setLoading(true);
                const query = {
                    "query": GET_ALL_CONTENTS_QUERY_AXIOS,
                    "variables": { page: this.pagination.currentPage, limit: this.pagination.limit, title: this.filter.searchValue }
                };
                if (this.filter.searchValue === "") delete query.variables.title;
                const res = await axiosGraphqlClient.post(contentAPI, query);
                const { total, data } = res.data.data.contents;
                this.contents = data;
                this.pagination.totalResults = total;
                this.pagination.totalPages = Math.ceil(total / this.pagination.limit);
                if (this.pagination.totalPages === 0) this.pagination.totalPages = 1;
                this.loadingStore.setLoading();
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** Change the course status */
        changeContentStatus: async function (value, content) {
            try {
                const query = {
                    "query": UPDATE_CONTENT_STATUS_MUTATION_AXIOS,
                    "variables": { id: content.id, is_published: value === "true" }
                };
                this.loadingStore.setLoading(true);
                await axiosGraphqlClient.post(contentAPI, query);
                this.loadingStore.setLoading();
                successToast("Conteúdo atualizado!", `A situação foi alterada com sucesso!`)
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** Duplicate Content */
        duplicateContent: async function (item) {
            try {
                this.loadingStore.setLoading(true);
                const getQuery = {
                    "query": GET_CONTENT_BY_ID_QUERY_AXIOS,
                    "variables": { id: item.id }
                };
                const res = await axiosGraphqlClient.post(contentAPI, getQuery);
                const content = Object.assign({}, res.data.data.content);
                delete content.id
                delete content.widgets

                content.title = `${content.title} - (Duplicado)`
                const notWidget = ['audio', 'video', 'redirect'].includes(content.contentType)

                if (!notWidget) {
                    content.widgets = res.data.data.content.widgets
                    if (content.widgets.length === 0) {
                        content.contentType = 'widgets'
                        content.widgets = [
                            {
                                position: 1,
                                text: content.title,
                                text_type: 'h2',
                                type: 'text',
                            }
                        ]
                    } else {
                        content.widgets = content.widgets.map(widget => {
                            const widgetWithoutFile = { ...widget };
                            delete widgetWithoutFile.File
                            return widgetWithoutFile;
                        })
                    }
                }

                const duplicateQuery = {
                    "query": CREATE_CONTENT_MUTATION_AXIOS,
                    "variables": content
                };
                await axiosGraphqlClient.post(contentAPI, duplicateQuery);
                await this.getContents();
                this.loadingStore.setLoading();
                successToast("Conteúdo duplicado!", `O conteúdo foi duplicado com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                console.log(e);
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** Delete content by Id */
        deleteContent: async function () {
            try {
                this.loadingStore.setLoading(true);
                this.contentModal.delete = false
                const query = {
                    "query": DELETE_CONTENT_MUTATION_AXIOS,
                    "variables": { id: this.contentId }
                };
                await axiosGraphqlClient.post(contentAPI, query);
                await this.getContents();
                this.loadingStore.setLoading();
                successToast("Conteúdo removido!", `Conteúdo removido com sucesso!`);
            } catch (e) {
                this.loadingStore.setLoading();
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** AUTHORS */
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
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
            }
        },
        /** Open Author modal if you need add one */
        openAuthorModal: async function (value, content) {
            if (value === 'new') {
                /** Trick to return select */
                this.authorModal.name = '';
                this.authorModal.email = '';
                this.authorModal.image = 'https://las.xgrow.com/background-default.png';
                const index = this.contents.findIndex(item => item.id === content.id);
                this.contents[index].author_id = content.authorOld;
                this.authorModal.active = true;
            } else {
                const query = {
                    "query": UPDATE_CONTENT_AUTHOR_MUTATION_AXIOS,
                    "variables": { id: content.id, name: content.name, author_id: value }
                };
                try {
                    this.loadingStore.setLoading(true);
                    await axiosGraphqlClient.post(contentAPI, query);
                    this.loadingStore.setLoading();
                    successToast("Conteúdo atualizado!", "O autor foi alterado com sucesso.")
                } catch (e) {
                    this.loadingStore.setLoading();
                    errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
                }
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
                errorToast("Ocorreu um erro", `Ocorreu um problema ao tentar salvar as alterações no conteúdo. Tente novamente mais tarde.`);
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
    async mounted() {
        await this.getAuthors();
        await this.getContents();
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

.img-icon {
    filter: brightness(0) saturate(100%) invert(73%) sepia(86%) saturate(2134%) hue-rotate(30deg) brightness(99%) contrast(76%);
    height: 1rem;
}

.modal-body__content {
    border-radius: 8px;
    padding: 1rem;
    margin-top: 0 !important;
}
</style>
